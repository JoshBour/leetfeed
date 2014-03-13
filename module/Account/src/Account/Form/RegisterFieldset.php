<?php
namespace Account\Form;

use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;

class RegisterFieldset extends Fieldset implements InputFilterProviderInterface
{
    const PLACEHOLDER_USERNAME = 'Enter your username..';
    const PLACEHOLDER_PASSWORD = 'Enter your password..';
    const PLACEHOLDER_EMAIL = 'Enter your email..';

    const LABEL_USERNAME = 'Username:';
    const LABEL_PASSWORD = 'Password:';
    const LABEL_EMAIL = 'Email:';

    const ERROR_USERNAME_EMPTY = "The username can't be empty.";
    const ERROR_USERNAME_INVALID_LENGTH = "The username length must be between between 4-15 characters long.";
    const ERROR_USERNAME_EXISTS = "The username already exists, please try another one.";
    const ERROR_USERNAME_INVALID_PATTERN = "The name can only contain letters, numbers, underscores and no spaces between.";
    const ERROR_PASSWORD_EMPTY = "The password can't be empty.";
    const ERROR_PASSWORD_INVALID_LENGTH = "The username length must be between between 4-15 characters long.";
    const ERROR_EMAIL_EMPTY = "The email can't be empty.";
    const ERROR_EMAIL_INVALID = "The email is invalid.";
    const ERROR_EMAIL_EXISTS = "The email already exists, please try another one.";

    /**
     * @var \Doctrine\ORM\EntityRepository
     */
    private $accountRepository;

    /**
     * @var \Zend\I18n\Translator\Translator
     */
    private $translator;

    public function __construct($translator)
    {
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
            'name' => 'email',
            'type' => 'email',
            'options' => array(
                'label' => $this->translator->translate(self::LABEL_EMAIL),
            ),
            'attributes' => array(
                'placeholder' => $this->translator->translate(self::PLACEHOLDER_EMAIL)
            ),
        ));

    }

    public function getInputFilterSpecification()
    {
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
                                \Zend\Validator\StringLength::INVALID => $this->translator->translate(self::ERROR_USERNAME_INVALID_LENGTH)
                            )
                        )
                    ),
                    array(
                        'name' => 'DoctrineModule\Validator\NoObjectExists',
                        'options' => array(
                            'object_repository' => $this->accountRepository,
                            'fields' => 'username',
                            'messages' => array(
                                'objectFound' => $this->translator->translate(self::ERROR_USERNAME_EXISTS)
                            )
                        )
                    ),
                    array(
                        'name' => 'regex',
                        'options' => array(
                            'pattern' => '/^[a-zA-Z0-9_]{4,16}$/',
                            'messages' => array(
                                \Zend\Validator\Regex::NOT_MATCH => $this->translator->translate(self::ERROR_USERNAME_INVALID_PATTERN)
                            )
                        )
                    )
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
                        'break_chain_on_failure' => true,
                        'options' => array(
                            'min' => 4,
                            'max' => 15,
                            'messages' => array(
                                \Zend\Validator\StringLength::INVALID => $this->translator->translate(self::ERROR_PASSWORD_INVALID_LENGTH)
                            )
                        )
                    ),
                ),
                'filters' => array(
                    array('name' => 'StringTrim'),
                    array('name' => 'StripTags')
                )
            ),
            'email' => array(
                'required' => true,
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                        'break_chain_on_failure' => true,
                        'options' => array(
                            'messages' => array(
                                \Zend\Validator\NotEmpty::IS_EMPTY => $this->translator->translate(self::ERROR_EMAIL_EMPTY)
                            )
                        )
                    ),
                    array(
                        'name' => 'EmailAddress',
                        'break_chain_on_failure' => true,
                        'options' => array(
                            'messages' => array(
                                \Zend\Validator\EmailAddress::INVALID_FORMAT => $this->translator->translate(self::ERROR_EMAIL_INVALID),
                            )
                        )
                    ),
                    array(
                        'name' => 'DoctrineModule\Validator\NoObjectExists',
                        'options' => array(
                            'object_repository' => $this->accountRepository,
                            'fields' => 'email',
                            'messages' => array(
                                'objectFound' => $this->translator->translate(self::ERROR_EMAIL_EXISTS)
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
     * Set the account repository.
     *
     * @param \Doctrine\ORM\EntityRepository $accountRepository
     * @return RegisterFieldset
     */
    public function setAccountRepository($accountRepository)
    {
        $this->accountRepository = $accountRepository;
        return $this;
    }

    /**
     * Get the account repository.
     *
     * @return \Doctrine\ORM\EntityRepository
     */
    public function getAccountRepository()
    {
        return $this->accountRepository;
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