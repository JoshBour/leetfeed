<?php
namespace Account\Form;

use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;

class LoginFieldset extends Fieldset implements InputFilterProviderInterface{

    const PLACEHOLDER_USERNAME = 'Enter your username..';
    const PLACEHOLDER_PASSWORD = 'Enter your password..';

    const LABEL_USERNAME = 'Username:';
    const LABEL_PASSWORD = 'Password:';
    const LABEL_REMEMBER_ME = 'Remember Me:';

    const ERROR_USERNAME_EMPTY = "The username can't be empty.";
    const ERROR_USERNAME_INVALID = "The username is invalid.";
    const ERROR_PASSWORD_EMPTY = "The password can't be empty.";
    const ERROR_PASSWORD_INVALID = "The password is invalid.";

    /**
     * @var \Zend\I18n\Translator\Translator
     */
    private $translator;

    public function __construct($translator){
        parent::__construct('account');

        $this->translator = $translator;

        $this->add(array(
            'name' => 'username',
            'type' => 'text',
            'options' => array(
                'label' => $this->translator->translate(self::LABEL_USERNAME)
            ),
            'attributes' => array(
                'placeholder' => $this->translator->translate(self::PLACEHOLDER_USERNAME)
            ),
        ));

        $this->add(array(
            'name' => 'password',
            'type' => 'password',
            'options' => array(
                'label' => $this->translator->translate(self::LABEL_PASSWORD)
            ),
            'attributes' => array(
                'placeholder' => $this->translator->translate(self::PLACEHOLDER_PASSWORD)
            ),
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Checkbox',
            'name' => 'remember',
            'attributes' => array(
                'value' => "1"
            ),
            'options' => array(
                'label' => $this->translator->translate(self::LABEL_REMEMBER_ME),
            ),
        ));
    }

    public function getInputFilterSpecification(){
        return array(
            'username' => array(
                'required' => true,
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                        'break_chain_on_failure' => true,
                        'options' => array(
                            'messages' => array(
                                \Zend\Validator\NotEmpty::IS_EMPTY => $this->translator->translate(self::ERROR_USERNAME_EMPTY)
                            )
                        )
                    ),
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'min' => 4,
                            'max' => 15,
                            'messages' => array(
                                \Zend\Validator\StringLength::INVALID => $this->translator->translate(self::ERROR_USERNAME_INVALID)
                            )
                        )
                    ),
                ),
                'filters' => array(
                    array('name' => 'StringTrim'),
                    array('name' => 'StripTags')
                )
            ),
            'password' => array(
                'required' => true,
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                        'break_chain_on_failure' => true,
                        'options' => array(
                            'messages' => array(
                                \Zend\Validator\NotEmpty::IS_EMPTY => $this->translator->translate(self::ERROR_PASSWORD_EMPTY)
                            )
                        )
                    ),
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'min' => 4,
                            'max' => 15,
                            'messages' => array(
                                \Zend\Validator\StringLength::INVALID => $this->translator->translate(self::ERROR_PASSWORD_INVALID)
                            )
                        )
                    ),
                ),
                'filters' => array(
                    array('name' => 'StringTrim'),
                    array('name' => 'StripTags')
                )
            )
        );
    }

    /**
     * Set the zend translator.
     *
     * @param \Zend\I18n\Translator\Translator $translator
     * @return LoginFieldset
     */
    public function setTranslator($translator)
    {
        $this->translator = $translator;
        return $this;
    }

    /**
     * Get the zend translator.
     *
     * @return \Zend\I18n\Translator\Translator
     */
    public function getTranslator()
    {
        return $this->translator;
    }


}