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
use CanadaPost\Model\Config\CanadaPostConfigValue;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Thelia\Core\Event\Order\OrderEvent;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Mailer\MailerFactory;

/**
 * Class SendMailAction
 * @package CanadaPost\Action
 * @author Julien Chanséaume <julien@thelia.net>
 */
class SendMailAction implements EventSubscriberInterface
{

    protected $mailer;

    public function __construct(MailerFactory $mailer)
    {
        $this->mailer = $mailer;
    }

    public function updateStatus(OrderEvent $event)
    {
        $order = $event->getOrder();

        if ($order->isSent() && $order->getDeliveryModuleId() == CanadaPost::getModuleId()) {

            $customer = $order->getCustomer();

            $trackingRef = $order->getDeliveryRef();
            $trackingUrl = CanadaPost::getConfigValue(CanadaPostConfigValue::TRACKING_URL);

            if (!empty($trackingUrl) && !empty($trackingRef)) {
                $trackingUrl = str_replace('%tracking-number%', $trackingRef, $trackingUrl);
                $trackingUrl = str_replace('%locale%', $customer->getCustomerLang()->getLocale(), $trackingUrl);
            } else {
                $trackingUrl = null;
            }

            $this->mailer->sendEmailToCustomer(
                'mail_canada_post',
                $customer,
                [
                    'customer_id' => $customer->getId(),
                    'order_id' => $order->getId(),
                    'order_ref' => $order->getRef(),
                    'order_date' => $order->getCreatedAt(),
                    'update_date' => $order->getUpdatedAt(),
                    'tracking_url' => $trackingUrl
                ]
            );
        }
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2'))
     *
     * @return array The event names to listen to
     *
     * @api
     */
    public static function getSubscribedEvents()
    {
        return array(
            TheliaEvents::ORDER_UPDATE_STATUS => array("updateStatus", 128),
        );
    }
}
