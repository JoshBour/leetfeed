<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 16/3/2014
 * Time: 8:15 μμ
 */

namespace Feed\Factory;


use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\InputFilter\InputFilter;
use Feed\Form\AddFeedFieldset;
use Feed\Form\AddFeedForm;

class AddFeedFormFactory implements FactoryInterface{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $fieldset = new AddFeedFieldset($serviceLocator->get('translator'));
        $form = new AddFeedForm();

        $fieldset->setUseAsBaseFieldset(true);

        $form->add($fieldset)
            ->setInputFilter(new InputFilter());
        return $form;
    }

} 