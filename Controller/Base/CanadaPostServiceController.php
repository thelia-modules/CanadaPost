<?php
/**ca
* This class has been generated by TheliaStudio
* For more information, see https://github.com/thelia-modules/TheliaStudio
*/

namespace CanadaPost\Controller\Base;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Thelia\Controller\Admin\AbstractCrudController;
use Thelia\Core\Security\Resource\AdminResources;
use Thelia\Tools\URL;
use CanadaPost\Event\CanadaPostServiceEvent;
use CanadaPost\Event\CanadaPostServiceEvents;
use CanadaPost\Model\CanadaPostServiceQuery;
use Thelia\Core\Event\ToggleVisibilityEvent;

/**
 * Class CanadaPostServiceController
 * @package CanadaPost\Controller\Base
 * @author TheliaStudio
 */
class CanadaPostServiceController extends AbstractCrudController
{
    public function __construct()
    {
        parent::__construct(
            "canada_post_service",
            "id",
            "order",
            AdminResources::MODULE,
            CanadaPostServiceEvents::CREATE,
            CanadaPostServiceEvents::UPDATE,
            CanadaPostServiceEvents::DELETE,
            CanadaPostServiceEvents::TOGGLE_VISIBILITY,
            null,
            "CanadaPost"
        );
    }

    /**
     * Return the creation form for this object
     */
    protected function getCreationForm()
    {
        return $this->createForm("canada_post_service.create");
    }

    /**
     * Return the update form for this object
     */
    protected function getUpdateForm($data = array())
    {
        if (!is_array($data)) {
            $data = array();
        }

        return $this->createForm("canada_post_service.update", "form", $data);
    }

    /**
     * Hydrate the update form for this object, before passing it to the update template
     *
     * @param mixed $object
     */
    protected function hydrateObjectForm($object)
    {
        $data = array(
            "id" => $object->getId(),
            "visible" => (bool) $object->getVisible(),
            "code" => $object->getCode(),
            "title" => $object->getTitle(),
            "chapo" => $object->getChapo(),
        );

        return $this->getUpdateForm($data);
    }

    /**
     * Creates the creation event with the provided form data
     *
     * @param mixed $formData
     * @return \Thelia\Core\Event\ActionEvent
     */
    protected function getCreationEvent($formData)
    {
        $event = new CanadaPostServiceEvent();

        $event->setVisible($formData["visible"]);
        $event->setCode($formData["code"]);
        $event->setTitle($formData["title"]);
        $event->setChapo($formData["chapo"]);

        return $event;
    }

    /**
     * Creates the update event with the provided form data
     *
     * @param mixed $formData
     * @return \Thelia\Core\Event\ActionEvent
     */
    protected function getUpdateEvent($formData)
    {
        $event = new CanadaPostServiceEvent();

        $event->setId($formData["id"]);
        $event->setVisible($formData["visible"]);
        $event->setCode($formData["code"]);
        $event->setTitle($formData["title"]);
        $event->setChapo($formData["chapo"]);

        return $event;
    }

    /**
     * Creates the delete event with the provided form data
     */
    protected function getDeleteEvent()
    {
        $event = new CanadaPostServiceEvent();

        $event->setId($this->getRequest()->request->get("canada_post_service_id"));

        return $event;
    }

    /**
     * Return true if the event contains the object, e.g. the action has updated the object in the event.
     *
     * @param mixed $event
     */
    protected function eventContainsObject($event)
    {
        return null !== $this->getObjectFromEvent($event);
    }

    /**
     * Get the created object from an event.
     *
     * @param mixed $event
     */
    protected function getObjectFromEvent($event)
    {
        return $event->getCanadaPostService();
    }

    /**
     * Load an existing object from the database
     */
    protected function getExistingObject()
    {
        return CanadaPostServiceQuery::create()
            ->findPk($this->getRequest()->query->get("canada_post_service_id"))
        ;
    }

    /**
     * Returns the object label form the object event (name, title, etc.)
     *
     * @param mixed $object
     */
    protected function getObjectLabel($object)
    {
        return $object->getTitle();
    }

    /**
     * Returns the object ID from the object
     *
     * @param mixed $object
     */
    protected function getObjectId($object)
    {
        return $object->getId();
    }

    /**
     * Render the main list template
     *
     * @param mixed $currentOrder , if any, null otherwise.
     */
    protected function renderListTemplate($currentOrder)
    {
        $this->getParser()
            ->assign("order", $currentOrder)
        ;

        return $this->render("canada-post-services");
    }

    /**
     * Render the edition template
     */
    protected function renderEditionTemplate()
    {
        $this->getParserContext()
            ->set(
                "canada_post_service_id",
                $this->getRequest()->query->get("canada_post_service_id")
            )
        ;

        return $this->render("canada-post-service-edit");
    }

    /**
     * Must return a RedirectResponse instance
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function redirectToEditionTemplate()
    {
        $id = $this->getRequest()->query->get("canada_post_service_id");

        return new RedirectResponse(
            URL::getInstance()->absoluteUrl(
                "/admin/module/CanadaPost/canada_post_service/edit",
                [
                    "canada_post_service_id" => $id,
                ]
            )
        );
    }

    /**
     * Must return a RedirectResponse instance
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function redirectToListTemplate()
    {
        return new RedirectResponse(
            URL::getInstance()->absoluteUrl("/admin/module/CanadaPost/canada_post_service")
        );
    }

    protected function createToggleVisibilityEvent()
    {
        return new ToggleVisibilityEvent($this->getRequest()->query->get("canada_post_service_id"));
    }
}
