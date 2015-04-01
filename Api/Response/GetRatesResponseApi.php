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
 * Class GetRatesResponseApi
 * @package CanadaPost\Api\Response
 * @author Julien Chanséaume <julien@thelia.net>
 */
class GetRatesResponseApi extends ResponseApi
{
    public function getPriceQuotes()
    {
        if (null === $this->xmlResponse || !empty($this->errors)) {
            throw new \RuntimeException("Price quote data are not available");
        }

        $priceQuotes = [];

        if ($this->xmlResponse->{'price-quotes'}) {
            $xmlPriceQuotes = $this->xmlResponse->{'price-quotes'}->children('http://www.canadapost.ca/ws/ship/rate-v3');
            if ($xmlPriceQuotes->{'price-quote'}) {
                foreach ($xmlPriceQuotes as $xmlPriceQuote) {
                    $priceQuote = [
                        'name' => (string) $xmlPriceQuote->{'service-name'},
                        'code' => (string) $xmlPriceQuote->{'service-code'},
                        'price' => (float) $xmlPriceQuote->{'price-details'}->{'due'},
                        'taxes' => $this->getTaxes($xmlPriceQuote->{'price-details'}->{'taxes'}),
                        'transit-time' => (int) $xmlPriceQuote->{'service-standard'}->{'expected-transit-time'},
                        'delivery-date' => (string) $xmlPriceQuote->{'service-standard'}->{'expected-delivery-date'}
                    ];
                    $priceQuotes[] = $priceQuote;
                }
            }
        }

        return $priceQuotes;
    }

    protected function getTaxes($xmlTaxes)
    {
        return (float) $xmlTaxes->{'gst'} + (float) $xmlTaxes->{'pst'} + (float) $xmlTaxes->{'hst'};
    }
}
