<?php
/*************************************************************************************/
/*      This file is part of the Thelia package.                                     */
/*                                                                                   */
/*      Copyright (c) OpenStudio                                                     */
/*      email : dev@thelia.net                                                       */
/*      web : http://www.thelia.net                                                  */
/*                                                                                   */
/*      For the full copyright and license information, please view the LICENSE.txt  */
/*      file that was distributed with this source code.                             */
/*************************************************************************************/


namespace CanadaPost\Api;

use CanadaPost\Api\Response\GetRatesResponseApi;
use Thelia\Model\Country;

/**
 * Class GetRates
 * @package CanadaPost\Api
 * @author Julien Chanséaume <julien@thelia.net>
 */
class GetRates extends BaseApi
{

    public function __construct()
    {
        parent::__construct(
            [
                'url' => 'https://soa-gw.canadapost.ca/rs/ship/price',
                'url-test' => 'https://ct.soa-gw.canadapost.ca/rs/ship/price',
                'method' => 'post',
                'curlHttpHeader' => [
                    'Content-Type: application/vnd.cpc.ship.rate-v3+xml',
                    'Accept: application/vnd.cpc.ship.rate-v3+xml',
                    'Accept-language: %locale%'
                ]
            ]
        );
    }

    /**
     * @param $countryCode
     * @param $postalCode
     * @param $weight
     *
     * @return Response\GetRatesResponseApi
     */
    public function getRates($countryCode, $postalCode, $weight)
    {
        $xmlRequest = $this->getXmlRequest($countryCode, $postalCode, $weight);

        $response = $this->doRequest($xmlRequest);

        return $response;
    }

    protected function getXmlRequest($countryCode, $postalCode, $weight)
    {
        $xmlRequest = [];
        $xmlRequest[] = '<?xml version="1.0" encoding="UTF-8"?>';
        $xmlRequest[] = '<mailing-scenario xmlns="http://www.canadapost.ca/ws/ship/rate-v3">';
        if ('counter' !== $this->options['quoteType']) {
            if ($this->options['customerNumber']) {
                $xmlRequest[] = sprintf('<customer-number>%s</customer-number>', $this->options['customerNumber']);
            }
            if ($this->options['contractId']) {
                $xmlRequest[] = sprintf('<contract-id>%s</contract-id>', $this->options['contractId']);
            }
        }
        $xmlRequest[] = sprintf('<quote-type>%s</quote-type>', $this->options['quoteType']);
        $xmlRequest[] = '<parcel-characteristics>';
        $xmlRequest[] = sprintf('<weight>%s</weight>', $weight);
        $xmlRequest[] = '</parcel-characteristics>';
        $xmlRequest[] = sprintf('<origin-postal-code>%s</origin-postal-code>', $this->options['origin']);
        $xmlRequest[] = '<destination>';

        // $countryCode = $country->getIsoalpha2();
        if ('CA' === $countryCode) {
            $xmlRequest[] = '<domestic>';
            $xmlRequest[] = sprintf('<postal-code>%s</postal-code>', $postalCode);
            $xmlRequest[] = '</domestic>';
        } elseif ('US' === $countryCode) {
            $xmlRequest[] = '<united-states>';
            $xmlRequest[] = sprintf('<zip-code>%s</zip-code>', $postalCode);
            $xmlRequest[] = '</united-states>';
        } else {
            $xmlRequest[] = '<international>';
            $xmlRequest[] = sprintf('<country-code>%s</country-code>', $countryCode);
            $xmlRequest[] = '</international>';
        }

        $xmlRequest[] = '</destination>';
        $xmlRequest[] = '</mailing-scenario>';

        return implode("\n", $xmlRequest);

    }

    protected function getResponseApi()
    {
        return new GetRatesResponseApi();
    }
}
