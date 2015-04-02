<?php
/**
* This class has been generated by TheliaStudio
* For more information, see https://github.com/thelia-modules/TheliaStudio
*/

namespace CanadaPost\Form\Base;

use CanadaPost\Form\CanadaPostOrderCreateForm as ChildCanadaPostOrderCreateForm;
use CanadaPost\Form\Type\CanadaPostOrderIdType;

/**
 * Class CanadaPostOrderForm
 * @package CanadaPost\Form
 * @author TheliaStudio
 */
class CanadaPostOrderUpdateForm extends ChildCanadaPostOrderCreateForm
{
    const FORM_NAME = "canada_post_order_update";

    public function buildForm()
    {
        parent::buildForm();

        $this->formBuilder
            ->add("id", CanadaPostOrderIdType::TYPE_NAME)
        ;
    }
}
