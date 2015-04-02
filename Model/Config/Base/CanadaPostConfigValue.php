<?php
/*************************************************************************************/
/* This file is part of the Thelia package.                                          */
/*                                                                                   */
/* Copyright (c) OpenStudio                                                          */
/* email : dev@thelia.net                                                            */
/* web : http://www.thelia.net                                                       */
/*                                                                                   */
/* For the full copyright and license information, please view the LICENSE.txt       */
/* file that was distributed with this source code.                                  */
/*************************************************************************************/

namespace CanadaPost\Model\Config\Base;

/**
 * Class CanadaPostConfigValue
 * @package CanadaPost\Model\Config\Base
 */
class CanadaPostConfigValue
{
    const ENABLED = "enabled";
    const MODE_PRODUCTION = "mode_production";
    const CUSTOMER_NUMBER = "customer_number";
    const USERNAME = "username";
    const PASSWORD = "password";
    const TEST_USERNAME = "test_username";
    const TEST_PASSWORD = "test_password";
    const QUOTE_TYPE_COMMERCIAL = "quote_type_commercial";
    const CONTRACT_ID = "contract_id";
    const INSURANCE = "insurance";
    const ORIGIN_POSTALCODE = "origin_postalcode";
    const DISALLOWED_SERVICES = "disallowed_services";
    const TRACKING_URL = "tracking_url";
}

