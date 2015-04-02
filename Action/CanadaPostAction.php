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


namespace CanadaPost\Action;

use CanadaPost\CanadaPost;
use CanadaPost\Model\Base\CanadaPostServiceQuery;
use CanadaPost\Model\CanadaPostOrder;
use CanadaPost\Model\CanadaPostOrderQuery;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Thelia\Core\Event\Order\OrderEvent;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Core\HttpFoundation\Request;
use Thelia\Model\AddressQuery;
use Thelia\Model\OrderPostage;

/**
 * Class CanadaPostAction
 * @package CanadaPost\Action
 * @author Julien Chanséaume <julien@thelia.net>
 */
class CanadaPostAction implements EventSubscriberInterface
{
    protected $request;

    protected $module;

    public function __construct(Request $request, CanadaPost $module)
    {
        $this->request = $request;
        $this->module = $module;
    }

    public function getRequest()
    {
        return $this->request;
    }

    /*
    public function setModuleCanadaPost(OrderEvent $event)
    {
        if ($event->getDeliveryModule() == CanadaPost::getModuleId()) {

            $request = $this->getRequest();
            $order = $event->getOrder();

            $serviceCode = $request->get('canada-post-service');
            $serviceOptions = $request->get('canada-post-options');

            $request->getSession()->set('canada-post-service', $serviceCode);
            $request->getSession()->set('canada-post-options', $serviceOptions);

            $canadaPostOrder = CanadaPostOrderQuery::create()
                ->filterByAddressId($order->getId())
                ->filterByOrderAddressId(null)
                ->findOne();

            if (null === $canadaPostOrder) {
                $canadaPostOrder = new CanadaPostOrder();
                $canadaPostOrder->setAddressId($event->getDeliveryAddress());
            }

            $canadaPostOrder->setOptions($serviceOptions);
            $canadaPostOrder->setService($serviceCode);
            $canadaPostOrder->save();
        }
    }
    */

    public function setModuleCanadaPost(OrderEvent $event)
    {
        if ($event->getDeliveryModule() == CanadaPost::getModuleId()) {

            $request = $this->getRequest();
            $order = $event->getOrder();


            $serviceCode = $request->get('canada-post-service');
            $serviceOptions = $request->get('canada-post-options');
            $service = CanadaPostServiceQuery::create()->findOneByCode($serviceCode);
            $serviceId = $service->getId();

            $request->getSession()->set('canada-post-service', $serviceCode);
            $request->getSession()->set('canada-post-options', $serviceOptions);

            $canadaPostOrder = CanadaPostOrderQuery::create()
                ->filterByAddressId($order->getId())
                ->filterByOrderAddressId(null)
                ->findOne();

            if (null === $canadaPostOrder) {
                $canadaPostOrder = new CanadaPostOrder();
                $canadaPostOrder->setAddressId($event->getDeliveryAddress());
            }

            $canadaPostOrder->setOptions($serviceOptions);
            $canadaPostOrder->setServiceId($serviceId);
            $canadaPostOrder->save();

            $address = AddressQuery::create()->findPk($event->getDeliveryAddress());

            $postage = OrderPostage::loadFromPostage(
                $this->module->getPostage($address->getCountry())
            );

            // Refresh the postage
            $event->setPostage($postage->getAmount());
            $event->setPostageTax($postage->getAmountTax());
            $event->setPostageTaxRuleTitle($postage->getTaxRuleTitle());

        }
    }

    public function setOrderDelivery(OrderEvent $event)
    {
        if ($event->getDeliveryModule() == CanadaPost::getModuleId()) {

            $request = $this->getRequest();
            $order = $event->getOrder();

            $serviceCode = $request->getSession()->get('canada-post-service');
            $serviceOptions = $request->getSession()->get('canada-post-options');
            $service = CanadaPostServiceQuery::create()->findOneByCode($serviceCode);
            $serviceId = $service->getId();

            $canadaPostOrder = CanadaPostOrderQuery::create()
                ->filterByAddressId($order->getId())
                ->filterByOrderAddressId(null)
                ->findOne();

            if (null === $canadaPostOrder) {
                $canadaPostOrder = new CanadaPostOrder();

            }

            $canadaPostOrder->setOptions($serviceOptions);
            $canadaPostOrder->setServiceId($serviceId);
            $canadaPostOrder->save();
        }
    }


    public static function getSubscribedEvents()
    {
        return array(
            TheliaEvents::ORDER_SET_DELIVERY_MODULE => array('setModuleCanadaPost', 64),
            // TheliaEvents::ORDER_SET_POSTAGE => array('setOrderPostage', 64),
            TheliaEvents::ORDER_BEFORE_PAYMENT => array('setOrderDelivery', 256)
        );
    }
}
