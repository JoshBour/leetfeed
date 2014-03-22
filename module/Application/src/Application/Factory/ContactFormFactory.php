<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 16/3/2014
 * Time: 8:15 μμ
 */

namespace Application\Factory;

use Application\Form\ContactFieldset;
use Application\Form\ContactForm;
use Zend\InputFilter\InputFilter;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ContactFormFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $fieldset = new ContactFieldset($serviceLocator->get('translator'));
        $form = new ContactForm();

        $form->add($fieldset)
            ->setInputFilter(new InputFilter());
        return $form;
    }

}

