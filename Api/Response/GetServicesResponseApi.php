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


namespace CanadaPost\Api\Response;

/**
 * Class GetServicesResponseApi
 * @package CanadaPost\Api\Response
 * @author Julien Chanséaume <julien@thelia.net>
 */
class GetServicesResponseApi extends ResponseApi
{
    public function getServices()
    {
        if (null === $this->xmlResponse || !empty($this->errors)) {
            throw new \RuntimeException("Services are not available");
        }

        $services = [];

        if ($this->xmlResponse->{'services'}) {
            $xmlServices = $this->xmlResponse->{'services'};
            if ($xmlServices->{'service'}) {
                foreach ($xmlServices->{'service'} as $xmlService) {
                    $service = [
                        'name' => (string) $xmlService->{'service-name'},
                        'code' => (string) $xmlService->{'service-code'},
                    ];
                    $services[] = $service;
                }
            }
        }

        return $services;
    }

    protected function getTaxes($xmlTaxes)
    {
        return (float) $xmlTaxes->{'gst'} + (float) $xmlTaxes->{'pst'} + (float) $xmlTaxes->{'hst'};
    }
}
