<?php
namespace Application\Form;

use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;

class ContactFieldset extends Fieldset implements InputFilterProviderInterface
{
    const PLACEHOLDER_SENDER = 'Enter your email..';
    const PLACEHOLDER_SUBJECT = 'Enter the subject..';
    const PLACEHOLDER_BODY = 'Enter your message..';

    const LABEL_SENDER = 'From:';
    const LABEL_SUBJECT = 'Subject:';
    const LABEL_BODY = 'Body:';

    const ERROR_SUBJECT_EMPTY = "The subject can't be empty.";
    const ERROR_SUBJECT_INVALID_LENGTH = "The subject length must be between between 10-30 characters long.";
    const ERROR_BODY_EMPTY = "The body can't be empty.";
    const ERROR_BODY_INVALID_LENGTH = "The body length must be between between 20-150 characters long.";
    const ERROR_SENDER_EMPTY = "The sender email can't be empty.";
    const ERROR_SENDER_INVALID = "The sender email is invalid.";

    /**
     * @var \Zend\I18n\Translator\Translator
     */
    private $translator;

    public function __construct($translator)
    {
        parent::__construct('contact');

        $this->translator = $translator;

        $this->add(array(
            'name' => 'subject',
            'type' => 'text',
            'options' => array(
                'label' => $this->translator->translate(self::LABEL_SUBJECT)
            ),
            'attributes' => array(
                'placeholder' => $this->translator->translate(self::PLACEHOLDER_SUBJECT)
            ),
        ));

        $this->add(array(
            'name' => 'body',
            'type' => 'text',
            'options' => array(
                'label' => $this->translator->translate(self::LABEL_BODY),
            ),
            'attributes' => array(
                'placeholder' => $this->translator->translate(self::PLACEHOLDER_BODY)
            ),
        ));

        $this->add(array(
            'name' => 'sender',
            'type' => 'email',
            'options' => array(
                'label' => $this->translator->translate(self::LABEL_SENDER)
            ),
            'attributes' => array(
                'placeholder' => $this->translator->translate(self::PLACEHOLDER_SENDER)
            ),
        ));
    }

    public function getInputFilterSpecification()
    {
        return array(
            'subject' => array(
                'required' => true,
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                        'break_chain_on_failure' => true,
                        'options' => array(
                            'messages' => array(
                                \Zend\Validator\NotEmpty::IS_EMPTY => $this->translator->translate(self::ERROR_SUBJECT_EMPTY)
                            )
                        )
                    ),
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'min' => 10,
                            'max' => 30,
                            'messages' => array(
                                \Zend\Validator\StringLength::INVALID => $this->translator->translate(self::ERROR_SUBJECT_INVALID_LENGTH)
                            )
                        )
                    ),
                ),
                'filters' => array(
                    array('name' => 'StringTrim'),
                    array('name' => 'StripTags')
                )
            ),
            'body' => array(
                'required' => true,
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                        'break_chain_on_failure' => true,
                        'options' => array(
                            'messages' => array(
                                \Zend\Validator\NotEmpty::IS_EMPTY => $this->translator->translate(self::ERROR_BODY_EMPTY)
                            )
                        )
                    ),
                    array(
                        'name' => 'StringLength',
                        'break_chain_on_failure' => true,
                        'options' => array(
                            'min' => 20,
                            'max' => 150,
                            'messages' => array(
                                \Zend\Validator\StringLength::INVALID => $this->translator->translate(self::ERROR_BODY_INVALID_LENGTH)
                            )
                        )
                    ),
                ),
                'filters' => array(
                    array('name' => 'StringTrim'),
                    array('name' => 'StripTags')
                )
            ),
            'sender' => array(
                'required' => true,
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                        'break_chain_on_failure' => true,
                        'options' => array(
                            'messages' => array(
                                \Zend\Validator\NotEmpty::IS_EMPTY => $this->translator->translate(self::ERROR_SENDER_EMPTY)
                            )
                        )
                    ),
                    array(
                        'name' => 'EmailAddress',
                        'break_chain_on_failure' => true,
                        'options' => array(
                            'messages' => array(
                                \Zend\Validator\EmailAddress::INVALID_FORMAT => $this->translator->translate(self::ERROR_SENDER_INVALID),
                            )
                        )
                    ),
                ),
                'filters' => array(
                    array('name' => 'StringTrim'),
                    array('name' => 'StripTags')
                )
            ),
        );
    }

    /**
     * Set the zend translator.
     *
     * @param \Zend\I18n\Translator\Translator $translator
     * @return RegisterFieldset
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