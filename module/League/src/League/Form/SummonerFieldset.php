<?php
namespace League\Form;

use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;

class SummonerFieldset extends Fieldset implements InputFilterProviderInterface
{
    const PLACEHOLDER_NAME = 'Enter your Summoner\'s name..';

    const LABEL_NAME = 'Name:';
    const LABEL_REGION = 'Region:';

    const ERROR_NAME_EMPTY = "The name can't be empty.";
    const ERROR_NAME_INVALID_LENGTH = "The name length is invalid.";

    /**
     * @var \Zend\I18n\Translator\Translator
     */
    private $translator;

    public function __construct($translator)
    {
        parent::__construct('summoner');

        $this->translator = $translator;

        $this->add(array(
            'name' => 'name',
            'type' => 'text',
            'options' => array(
                'label' => $this->translator->translate(self::LABEL_NAME)
            ),
            'attributes' => array(
                'placeholder' => $this->translator->translate(self::PLACEHOLDER_NAME)
            ),
        ));

        $valueOptions = array();
        foreach(\League\Service\League::$supportedRegions as $region){
            $valueOptions[$region] = strtoupper($region);
        }

        $this->add(array(
            'name' => 'region',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => $this->translator->translate(self::LABEL_REGION),
                'value_options' => $valueOptions
            ),
        ));
    }

    public function getInputFilterSpecification()
    {
        return array(
            'name' => array(
                'required' => true,
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                        'break_chain_on_failure' => true,
                        'options' => array(
                            'messages' => array(
                                \Zend\Validator\NotEmpty::IS_EMPTY => $this->translator->translate(self::ERROR_NAME_EMPTY)
                            )
                        )
                    ),
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'min' => 1,
                            'max' => 100,
                            'messages' => array(
                                \Zend\Validator\StringLength::INVALID => $this->translator->translate(self::ERROR_NAME_INVALID_LENGTH)
                            )
                        )
                    ),
                ),
                'filters' => array(
                    array('name' => 'StringTrim'),
                    array('name' => 'StripTags')
                )
            ),
            'region' => array(
                'required' => true,
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
     * @return SummonerFieldset
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