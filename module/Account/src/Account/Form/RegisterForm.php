<?php
namespace Account\Form;

use Zend\Form\Form;

class RegisterForm extends Form
{
    public function __construct()
    {
        parent::__construct('registerForm');

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
          #  'security',
            'account' => array(
                'username',
                'password',
                'email'
            )
        ));
    }
}