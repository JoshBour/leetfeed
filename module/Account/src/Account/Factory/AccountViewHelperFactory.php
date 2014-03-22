<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 16/3/2014
 * Time: 8:15 μμ
 */

namespace Account\Factory;

use Account\View\Helper\User;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AccountViewHelperFactory implements FactoryInterface{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {

        $helper = new User();
        $helper->setServiceManager($serviceLocator->getServiceLocator());
        return $helper;
    }

}

