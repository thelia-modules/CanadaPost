<?php
/**
* This class has been generated by TheliaStudio
* For more information, see https://github.com/thelia-modules/TheliaStudio
*/

namespace CanadaPost\Event\Base;

use CanadaPost\Event\CanadaPostEvents as ChildCanadaPostEvents;

/*
 * Class CanadaPostServiceEvents
 * @package CanadaPost\Event\Base
 * @author TheliaStudio
 */
class CanadaPostServiceEvents
{
    const CREATE = ChildCanadaPostEvents::CANADA_POST_SERVICE_CREATE;
    const UPDATE = ChildCanadaPostEvents::CANADA_POST_SERVICE_UPDATE;
    const DELETE = ChildCanadaPostEvents::CANADA_POST_SERVICE_DELETE;
    const TOGGLE_VISIBILITY = ChildCanadaPostEvents::CANADA_POST_SERVICE_TOGGLE_VISIBILITY;
}
