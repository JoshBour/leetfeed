<?php
namespace Application\Form;

use Zend\Form\Form;

class ContactForm extends Form
{
    public function __construct()
    {
        parent::__construct('contactForm');

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
            'contact' => array('sender',
            'subject',
            'body')
        ));
    }
}