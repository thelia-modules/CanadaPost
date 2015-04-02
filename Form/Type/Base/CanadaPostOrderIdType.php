<?php
/**
* This class has been generated by TheliaStudio
* For more information, see https://github.com/thelia-modules/TheliaStudio
*/

namespace CanadaPost\Form\Type\Base;

use Thelia\Core\Form\Type\Field\AbstractIdType;
use CanadaPost\Model\CanadaPostOrderQuery;

/**
 * Class CanadaPostOrder
 * @package CanadaPost\Form\Base
 * @author TheliaStudio
 */
class CanadaPostOrderIdType extends AbstractIdType
{
    const TYPE_NAME = "canada_post_order_id";

    protected function getQuery()
    {
        return new CanadaPostOrderQuery();
    }

    public function getName()
    {
        return static::TYPE_NAME;
    }
}
