<?php
/**
* This class has been generated by TheliaStudio
* For more information, see https://github.com/thelia-modules/TheliaStudio
*/

namespace CanadaPost\Form;

use CanadaPost\CanadaPost;
use CanadaPost\Form\Base\CanadaPostConfigForm as BaseCanadaPostConfigForm;

/**
 * Class CanadaPostConfigForm
 * @package CanadaPost\Form\Base
 */
class CanadaPostConfigForm extends BaseCanadaPostConfigForm
{
    public function getTranslationKeys()
    {
        return array(
            "enabled" => $this->translator->trans("Enabled", [], CanadaPost::MESSAGE_DOMAIN),
            "mode_production" => $this->translator->trans("Use the production mode ?", [], CanadaPost::MESSAGE_DOMAIN),
            "customer_number" => $this->translator->trans("Customer number", [], CanadaPost::MESSAGE_DOMAIN),
            "username" => $this->translator->trans("Username", [], CanadaPost::MESSAGE_DOMAIN),
            "password" => $this->translator->trans("Password", [], CanadaPost::MESSAGE_DOMAIN),
            "test_username" => $this->translator->trans("Test username", [], CanadaPost::MESSAGE_DOMAIN),
            "test_password" => $this->translator->trans("Test password", [], CanadaPost::MESSAGE_DOMAIN),
            "quote_type_commercial" => $this->translator->trans("Commercial quote type ?", [], CanadaPost::MESSAGE_DOMAIN),
            "contract_id" => $this->translator->trans("Contract id", [], CanadaPost::MESSAGE_DOMAIN),
            "insurance" => $this->translator->trans("Insurance", [], CanadaPost::MESSAGE_DOMAIN),
            "origin_postalcode" => $this->translator->trans("Origin Zip/Postal Code", [], CanadaPost::MESSAGE_DOMAIN),
            "disallowed_services" => $this->translator->trans("Disallowed Shipping Methods", [], CanadaPost::MESSAGE_DOMAIN),
            "tracking_url" => $this->translator->trans("Tracking URL", [], CanadaPost::MESSAGE_DOMAIN),
            "help.enabled" => $this->translator->trans("Do you want to activate Canada Post", [], CanadaPost::MESSAGE_DOMAIN),
            "help.mode_production" => $this->translator->trans("if not checked the test mode will be used", [], CanadaPost::MESSAGE_DOMAIN),
            "help.customer_number" => $this->translator->trans("Your Canada Post Customer Number", [], CanadaPost::MESSAGE_DOMAIN),
            "help.username" => $this->translator->trans("Your Canada Post API Username in Production mode", [], CanadaPost::MESSAGE_DOMAIN),
            "help.password" => $this->translator->trans("Your Canada Post API password in Production mode", [], CanadaPost::MESSAGE_DOMAIN),
            "help.test_username" => $this->translator->trans("Your Canada Post API Username in Test mode", [], CanadaPost::MESSAGE_DOMAIN),
            "help.test_password" => $this->translator->trans("Your Canada Post API password in Test mode", [], CanadaPost::MESSAGE_DOMAIN),
            "help.quote_type_commercial" => $this->translator->trans("If checked you should also provide the Contract Id. If not checked, counter will be used and regular prices will be displayed.", [], CanadaPost::MESSAGE_DOMAIN),
            "help.contract_id" => $this->translator->trans("For commercial/contracted rates only", [], CanadaPost::MESSAGE_DOMAIN),
            "help.origin_postalcode" => $this->translator->trans("from which the parcel will be sent", [], CanadaPost::MESSAGE_DOMAIN),
            "help.disallowed_services" => $this->translator->trans("The Canada Post services not to be offered", [], CanadaPost::MESSAGE_DOMAIN),
            "help.tracking_url" => $this->translator->trans("The URL to access to a parcel’s delivery status", [], CanadaPost::MESSAGE_DOMAIN),
        );
    }
}
