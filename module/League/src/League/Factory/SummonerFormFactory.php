<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 16/3/2014
 * Time: 8:15 μμ
 */

namespace League\Factory;

use League\Form\SummonerForm;
use League\Form\SummonerFieldset;
use Zend\InputFilter\InputFilter;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class SummonerFormFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $fieldset = new SummonerFieldset($serviceLocator->get('translator'));
        $form = new SummonerForm();

        $form->add($fieldset)
            ->setInputFilter(new InputFilter());
        return $form;
    }

}

