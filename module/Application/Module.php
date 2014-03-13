<?php
namespace Application;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
        if (preg_match('/ipad|iphone|itouch|android|blackberry|iemobile/', strtolower($_SERVER['HTTP_USER_AGENT']))) {
            $eventManager -> attach('dispatch', array($this, 'isMobile'), 5);
        }
    }

    public function isMobile(MvcEvent $e) {
      #  $templateStack = $e->getTarget()->getServiceManager()->get('Zend\View\Resolver\TemplatePathStack');
      #  $templateStack->setDefaultSuffix('mobile.phtml');
        $viewModel = $e->getViewModel();
        $viewModel->setTemplate('layout/mobile');
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
}
