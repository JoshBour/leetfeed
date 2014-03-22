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

class AuthFactory implements FactoryInterface{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $authService = $serviceLocator->get('doctrine.authenticationservice.orm_default');
        $authService->setStorage($serviceLocator->get('AuthStorage'));
        return $authService;
    }

} 