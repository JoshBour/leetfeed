<?php
namespace Feed\Form;

use Zend\Form\Form;

class AddFeedForm extends Form
{
    public function __construct()
    {
        parent::__construct('addFeedForm');

        $this->setAttributes(array(
            'method' => 'post',
            'class' => 'standardForm'
        ));

        $this->add(array(
            'name' => 'security',
            'type' => 'Zend\Form\Element\Csrf'
        ));

        $this->add(array(
            'name' => 'submit',
            'type' => 'submit'
        ));

        $this->setValidationGroup(array(
            'security',
            'feed' => array(
                'title',
                'url',
            )
        ));
    }
}