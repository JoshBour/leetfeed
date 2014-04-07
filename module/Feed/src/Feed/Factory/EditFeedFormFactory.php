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
use Feed\Form\EditFeedFieldset;
use Feed\Form\EditFeedForm;
use Feed\Entity\Feed;

use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;

class EditFeedFormFactory implements FactoryInterface{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $entityManager = $serviceLocator->get('Doctrine\ORM\EntityManager');
        $fieldset = new EditFeedFieldset($serviceLocator->get('translator'));
        $form = new EditFeedForm();
        $hydrator = new DoctrineHydrator($entityManager, '\Feed\Entity\Feed');

        $fieldset->setUseAsBaseFieldset(true)
            ->setHydrator($hydrator)
            ->setObject(new Feed);

        $form->add($fieldset)
            ->setInputFilter(new InputFilter())
            ->setHydrator($hydrator);
        return $form;
    }

} 