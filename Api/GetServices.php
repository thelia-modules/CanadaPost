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

use CanadaPost\Api\Response\GetServicesResponseApi;
use Thelia\Model\Country;

/**
 * Class GetRates
 * @package CanadaPost\Api
 * @author Julien Chanséaume <julien@thelia.net>
 */
class GetServices extends BaseApi
{

    public function __construct()
    {
        parent::__construct(
            [
                'url' => 'https://soa-gw.canadapost.ca/rs/ship/service',
                'url-test' => 'https://ct.soa-gw.canadapost.ca/rs/ship/service',
                'method' => 'get',
                'curlHttpHeader' => [
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
     * @return Response\GetServicesResponseApi
     */
    public function getServices()
    {
        $response = $this->doRequest();

        return $response;
    }

    protected function lazyBuildUrl($url)
    {
        $url .= '?';

        if ($this->options['contractId']) {
            $url .= "contract=" . $this->options['contractId'];
        }

        if ($this->options['origin']) {
            $url .= "origpc=" . $this->options['origin'];
        }

        return $url;
    }

    protected function getResponseApi()
    {
        return new GetServicesResponseApi();
    }
}
