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


namespace CanadaPost\Hook;

use CanadaPost\CanadaPost;
use Thelia\Core\Event\Hook\HookRenderEvent;
use Thelia\Core\Hook\BaseHook;
use Thelia\Model\OrderQuery;

/**
 * Class CanadaPostHook
 * @package CanadaPost\Hook
 * @author Julien Chanséaume <julien@thelia.net>
 */
class CanadaPostHook extends BaseHook
{
    public function onOrderDeliveryExtra(HookRenderEvent $event)
    {
        $address = CanadaPost::getCartDeliveryAddress($this->getRequest());
        if (null === $address) {
            return;
        }

        $countryCode = $address->getCountry()->getIsoalpha2();
        $cartWeight = $this->getCart()->getWeight();

        $isModuleSelected = false;
        if (null !== $order = $this->getOrder()) {
            $isModuleSelected = (CanadaPost::getModuleId() === $this->getOrder()->getDeliveryModuleId());
        }

        $serviceSelected = null;
        if ($isModuleSelected) {
            $serviceSelected = $this->getSession()->get('canada-post-service');
        }

        $event->add(
            $this->render(
                'order-delivery-extra.html',
                [
                    'country_code' => $countryCode,
                    'postalCode' => $address->getZipcode(),
                    'weight' => $cartWeight,
                    'is_module_selected' => $isModuleSelected,
                    'service_selected' => $serviceSelected
                ]
            )
        );
    }

    public function onOrderDeliveryJavascriptInitialization(HookRenderEvent $event)
    {
        $event->add(
            $this->render(
                'order-delivery-javascript-initialization.html',
                [
                    'canada_post_id' => CanadaPost::getModuleId()
                ]
            )
        );
    }


    public function onOrderServiceInformation(HookRenderEvent $event)
    {
        $orderId = $event->getArgument('order_id');
        // in pdf argument is just order, not order_id
        if (null === $orderId) {
            $orderId = $event->getArgument('order');
        }

        if (null !== $orderId) {
            $order = OrderQuery::create()->findPk($orderId);

            if (null !== $order && $order->getDeliveryModuleId() == CanadaPost::getModuleId()) {
                $event->add(
                    $this->render(
                        'order-service-information.html',
                        [
                            'order_address_id' => $order->getDeliveryOrderAddressId()
                        ]
                    )
                );
            }
        }
    }
}
