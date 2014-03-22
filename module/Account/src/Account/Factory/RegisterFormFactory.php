<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 16/3/2014
 * Time: 8:15 μμ
 */

namespace Account\Factory;


use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\InputFilter\InputFilter;
use Account\Form\RegisterFieldset;
use Account\Form\RegisterForm;
use Account\Entity\Account;

use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;

class RegisterFormFactory implements FactoryInterface{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $entityManager = $serviceLocator->get('Doctrine\ORM\EntityManager');
        $fieldset = new RegisterFieldset($serviceLocator->get('translator'));
        $form = new RegisterForm();
        $hydrator = new DoctrineHydrator($entityManager, '\Account\Entity\Account');

        $fieldset->setAccountRepository($entityManager->getRepository('\Account\Entity\Account'))
            ->setUseAsBaseFieldset(true)
            ->setHydrator($hydrator)
            ->setObject(new Account);

        $form->add($fieldset)
            ->setInputFilter(new InputFilter())
            ->setHydrator($hydrator);
        return $form;
    }

} 