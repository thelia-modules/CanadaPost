<?php
/**
 * This class has been generated by TheliaStudio
 * For more information, see https://github.com/thelia-modules/TheliaStudio
 */

namespace CanadaPost;

use CanadaPost\Api\GetRates;
use CanadaPost\Model\CanadaPostServiceQuery;
use CanadaPost\Model\Config\CanadaPostConfigValue;
use CanadaPost\Model\Map\CanadaPostServiceTableMap;
use Propel\Runtime\Connection\ConnectionInterface;
use Thelia\Core\HttpFoundation\Request;
use Thelia\Core\Translation\Translator;
use Thelia\CurrencyConverter\Tests\CurrencyConverterTest;
use Thelia\Install\Database;
use Thelia\Log\Tlog;
use Thelia\Model\Address;
use Thelia\Model\AddressQuery;
use Thelia\Model\AreaDeliveryModuleQuery;
use Thelia\Model\Base\CurrencyQuery;
use Thelia\Model\Country;
use Thelia\Model\CountryAreaQuery;
use Thelia\Model\Currency;
use Thelia\Model\LangQuery;
use Thelia\Model\Message;
use Thelia\Model\MessageQuery;
use Thelia\Model\OrderPostage;
use Thelia\Module\BaseModule;
use Thelia\Module\DeliveryModuleInterface;
use Thelia\Module\Exception\DeliveryException;

/**
 * Class CanadaPost
 * @package CanadaPost
 */
class CanadaPost extends BaseModule implements DeliveryModuleInterface
{
    const MESSAGE_DOMAIN = "canadapost";
    const ROUTER = "router.canadapost";

    protected static $canadianDollarCurrency = null;

    /** @var Translator $translator */
    protected $translator;

    /** @var Address */
    protected $deliveryAddress = null;


    public function postActivation(ConnectionInterface $con = null)
    {
        $database = new Database($con);

        $database->insertSql(null, [__DIR__ . "/Config/create.sql", __DIR__ . "/Config/insert.sql"]);

        $this->initializeConfig();

        $this->initializeMessage();
    }

    protected function initializeMessage()
    {
        // create new message
        if (null === MessageQuery::create()->findOneByName('mail_canada_post')) {
            $message = new Message();
            $message
                ->setName('mail_canada_post')
                ->setHtmlTemplateFileName('mail-canada-post.html')
                ->setHtmlLayoutFileName('')
                ->setTextTemplateFileName('mail-canada-post.txt')
                ->setTextLayoutFileName('')
                ->setSecured(0);

            $languages = LangQuery::create()->find();

            foreach ($languages as $language) {
                $locale = $language->getLocale();

                $message->setLocale($locale);
                $message->setSubject(
                    $this->trans('Your order {$order_ref} has been shipped.', [], $locale)
                );
                $message->setTitle(
                    $this->trans('Canada Post shipping message', [], $locale)
                );
            }

            $message->save();
        }
    }

    protected function initializeConfig()
    {
        $defaults = [
            CanadaPostConfigValue::ENABLED => 0,
            CanadaPostConfigValue::MODE_PRODUCTION => 0,
            CanadaPostConfigValue::CUSTOMER_NUMBER => '',
            CanadaPostConfigValue::USERNAME => '',
            CanadaPostConfigValue::PASSWORD => '',
            CanadaPostConfigValue::TEST_USERNAME => '',
            CanadaPostConfigValue::TEST_PASSWORD => '',
            CanadaPostConfigValue::CONTRACT_ID => '',
            CanadaPostConfigValue::QUOTE_TYPE_COMMERCIAL => 0,
            CanadaPostConfigValue::INSURANCE => 0,
            CanadaPostConfigValue::ORIGIN_POSTALCODE => '',
            CanadaPostConfigValue::DISALLOWED_SERVICES => '',
            CanadaPostConfigValue::TRACKING_URL => 'http://www.canadapost.ca/cpotools/apps/track/personal/findByTrackNumber?trackingNumber=%tracking-number%&LOCALE=%locale%',
        ];

        foreach ($defaults as $configName => $configValue) {
            if (null === CanadaPost::getConfigValue($configName)) {
                CanadaPost::setConfigValue($configName, $configValue);
            }
        }
    }

    /**
     * This method is called by the Delivery  loop, to check if the current module has to be displayed to the customer.
     * Override it to implements your delivery rules/
     *
     * If you return true, the delivery method will de displayed to the customer
     * If you return false, the delivery method will not be displayed
     *
     * @param Country $country the country to deliver to.
     *
     * @return boolean
     */
    public function isValidDelivery(Country $country)
    {
        if (0 === intval(self::getConfigValue(CanadaPostConfigValue::ENABLED))) {
            return false;
        }

        $this->deliveryAddress = self::getCartDeliveryAddress($this->getRequest());
        if (null === $this->deliveryAddress) {
            // We need a postal/zip code
            if (in_array($this->deliveryAddress->getCountry()->getIsoalpha2(), ['CA', 'US'])) {
                return false;
            }
        }

        if (!$this->isValidCountry($country)) {
            return false;
        }

        return true;
    }

    protected function isValidCountry(Country $country)
    {

        $query = AreaDeliveryModuleQuery::create();
        $query->filterByDeliveryModuleId(self::getModuleId());

        if (class_exists('\\Thelia\\Model\\CountryAreaQuery')) {
            $query
                ->useAreaQuery()
                ->useCountryAreaQuery()
                ->filterByCountryId($country->getId())
                ->endUse()
                ->endUse();
        } else {
            $query
                ->useAreaQuery()
                ->filterByCountry($country)
                ->endUse();
        }

        return (0 < $query->count());
    }

    /**
     * Calculate and return delivery price in the shop's default currency
     *
     * @param Country $country the country to deliver to.
     *
     * @return OrderPostage|float             the delivery price
     * @throws DeliveryException if the postage price cannot be calculated.
     */
    public function getPostage(Country $country)
    {

        if (!$this->isValidDelivery($country)) {
            return false;
        }

        // $cartWeight = $this->getRequest()->getSession()->getCart()->getWeight();
        $session = $this->getRequest()->getSession();
        $cart = $session->getSessionCart();
        $address = $this->deliveryAddress;
        $cartWeight = $cart->getWeight();

        try {
            $priceQuotes = self::getRates(
                $country->getIsoalpha2(),
                $address->getZipcode(),
                $cartWeight,
                $session->getCurrency(true)
            );
        } catch (\Exception $ex) {
            Tlog::getInstance()->warning(
                sprintf(
                    $this->trans('Canada Post not available : %s'),
                    $ex->getMessage()
                )
            );
            throw new DeliveryException($ex->getMessage());
        }

        if (empty($priceQuotes)) {
            throw new DeliveryException($this->trans('Canada Post not available'));
        }

        // pick  price from service already selected
        $selected = $session->get('canada-post-service');

        $priceQuote = $this->getCheapestPrice($priceQuotes, $selected);

        $orderPostage = new OrderPostage(
            $priceQuote['price'],
            $priceQuote['taxes']
        );

        return $orderPostage;
    }

    /**
     * Retrieve delivery address associated to the order or the default address of the customer
     *
     * @return null|\Thelia\Model\Address
     */
    public static function getCartDeliveryAddress(Request $request)
    {
        $address = null;
        $session = $request->getSession();

        if (null !== $customer = $session->getCustomerUser()) {
            if (null !== $session->getOrder()
                && null !== $session->getOrder()->getChoosenDeliveryAddress()
                && null !== $currentDeliveryAddress = AddressQuery::create()->findPk($session->getOrder()->getChoosenDeliveryAddress())
            ) {
                $address = $currentDeliveryAddress;
            } else {
                $address = $customer->getDefaultAddress();
            }
        }

        return $address;
    }

    protected function getCheapestPrice(array $priceQuotes, $selected)
    {
        $cheapest = null;

        foreach ($priceQuotes as $priceQuote) {

            if ($selected === $priceQuote['code']) {
                return $priceQuote;
            }

            if (null === $cheapest || $priceQuote['price'] < $cheapest['price']) {
                $cheapest = $priceQuote;
            }
        }

        return $cheapest;
    }

    public static function getRates($countryCode, $postalCode, $weight, Currency $currency)
    {
        $api = new GetRates();
        $response = $api->getRates($countryCode, $postalCode, $weight);

        $priceQuotes = $response->getPriceQuotes();

        // filter services
        $availableServices = CanadaPostServiceQuery::create()
            ->filterByVisible(1)
            ->select(CanadaPostServiceTableMap::CODE)
            ->find()
            ->toArray();

        for ($i = 0 ; $i < count($priceQuotes) ; $i++) {
            if (!in_array($priceQuotes[$i]['code'], $availableServices)) {
                unset($priceQuotes[$i]);
            }
        }

        // convert in the right currency
        if ("CAD" !== $currency->getCode()) {
            $canadianDollar = self::getCanadianDollarCurrency();
            $conversionRate = $currency->getRate() / $canadianDollar->getRate();

            foreach ($priceQuotes as &$priceQuote) {
                $priceQuote['price-cad'] = $priceQuote['price'];
                $priceQuote['taxes-cad'] = $priceQuote['taxes'];

                $priceQuote['price'] = round($priceQuote['price'] * $conversionRate, 2);
                $priceQuote['taxes'] = round($priceQuote['taxes'] * $conversionRate, 2);
            }
        }

        return $priceQuotes;
    }

    /**
     * @return null|Currency
     */
    public static function getCanadianDollarCurrency()
    {
        if (null === self::$canadianDollarCurrency) {
            self::$canadianDollarCurrency = CurrencyQuery::create()->findOneByCode('CAD');
        }

        return self::$canadianDollarCurrency;
    }

    /**
     *
     * This method return true if your delivery manages virtual product delivery.
     *
     * @return bool
     */
    public function handleVirtualProductDelivery()
    {
        return false;
    }

    protected function trans($id, $parameters = [], $locale = null)
    {
        if (null === $this->translator) {
            $this->translator = Translator::getInstance();
        }

        return $this->translator->trans($id, $parameters, self::MESSAGE_DOMAIN, $locale);
    }
}
