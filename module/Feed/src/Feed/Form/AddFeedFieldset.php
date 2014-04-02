<?php
namespace Feed\Form;

use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;

class AddFeedFieldset extends Fieldset implements InputFilterProviderInterface
{
    const PLACEHOLDER_TITLE = 'Enter the title..';
    const PLACEHOLDER_URL = 'Enter the url..';

    const LABEL_TITLE = 'Title (Optional):';
    const LABEL_URL = 'Url:';

    const ERROR_TITLE_EMPTY = "The title can't be empty.";
    const ERROR_TITLE_INVALID_LENGTH = "The title length must be between between 4-15 characters long.";
    const ERROR_URL_EMPTY = "The url can't be empty.";
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
        parent::__construct('feed');

        $this->translator = $translator;

        $this->add(array(
            'name' => 'title',
            'type' => 'text',
            'options' => array(
                'label' => $this->translator->translate(self::LABEL_TITLE)
            ),
            'attributes' => array(
                'placeholder' => $this->translator->translate(self::PLACEHOLDER_TITLE)
            ),
        ));

        $this->add(array(
            'name' => 'url',
            'type' => 'text',
            'options' => array(
                'label' => $this->translator->translate(self::LABEL_URL)
            ),
            'attributes' => array(
                'placeholder' => $this->translator->translate(self::PLACEHOLDER_URL)
            ),
        ));
    }

    public function getInputFilterSpecification()
    {
        return array(
            'title' => array(
                'required' => false,
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                        'break_chain_on_failure' => true,
                        'options' => array(
                            'messages' => array(
                                \Zend\Validator\NotEmpty::IS_EMPTY => $this->translator->translate(self::ERROR_TITLE_EMPTY)
                            )
                        )
                    ),
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'min' => 4,
                            'max' => 15,
                            'messages' => array(
                                \Zend\Validator\StringLength::INVALID => $this->translator->translate(self::ERROR_TITLE_INVALID_LENGTH)
                            )
                        )
                    ),
                ),
                'filters' => array(
                    array('name' => 'StringTrim'),
                    array('name' => 'StripTags')
                )
            ),
            'url' => array(
                'required' => true,
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                        'break_chain_on_failure' => true,
                        'options' => array(
                            'messages' => array(
                                \Zend\Validator\NotEmpty::IS_EMPTY => $this->translator->translate(self::ERROR_URL_EMPTY)
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
     * @return this
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