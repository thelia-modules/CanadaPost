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


namespace CanadaPost\Loop;

use CanadaPost\CanadaPost;
use Thelia\Core\Template\Element\ArraySearchLoopInterface;
use Thelia\Core\Template\Element\BaseLoop;
use Thelia\Core\Template\Element\LoopResult;
use Thelia\Core\Template\Element\LoopResultRow;
use Thelia\Core\Template\Loop\Argument\Argument;
use Thelia\Core\Template\Loop\Argument\ArgumentCollection;

/**
 * Class Rates
 * @package CanadaPost\Loop
 * @author Julien Chanséaume <julien@thelia.net>
 */
class Rates extends BaseLoop implements ArraySearchLoopInterface
{
    /* set countable to false since we need to preserve keys */
    protected $countable = false;

    /**
     * @return ArgumentCollection
     */
    protected function getArgDefinitions()
    {
        return new ArgumentCollection(
            Argument::createAnyTypeArgument('country', null, true),
            Argument::createAnyTypeArgument('postal_code', null, false),
            Argument::createAnyTypeArgument('weight', null, true)
        );
    }

    public function buildArray()
    {
        $country = $this->getArgValue('country');
        $postalCode = $this->getArgValue('postal_code');
        $weight = $this->getArgValue('weight');

        $priceQuotes = CanadaPost::getRates(
            $country,
            $postalCode,
            $weight,
            $this->request->getSession()->getCurrency()
        );

        return $priceQuotes;
    }

    public function parseResults(LoopResult $loopResult)
    {
        foreach ($loopResult->getResultDataCollection() as $priceQuote) {
            $loopResultRow = new LoopResultRow();
            $loopResultRow
                ->set('NAME', $priceQuote['name'])
                ->set('CODE', $priceQuote['code'])
                ->set('PRICE', $priceQuote['price'])
                ->set('TRANSIT_TIME', $priceQuote['transit-time'])
                ->set('DELIVERY_DATE', $priceQuote['delivery-date']);

            $loopResult->addRow($loopResultRow);
        }

        return $loopResult;
    }
}
