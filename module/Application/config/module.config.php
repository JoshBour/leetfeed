<?php
namespace Application;

use \Zend\InputFilter\InputFilter;

return array(
    'router' => array(
        'routes' => array(
            'home' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action'     => 'index',
                    ),
                ),
            ),
            'faq' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/faq',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action'     => 'faq',
                    ),
                ),
            ),
            'sitemap_direct' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/sitemap.xml',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action'     => 'sitemap',
                    ),
                ),
            ),
            'sitemap' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'    => '/sitemap[/:type[/:index]]',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action'     => 'sitemap',
                    ),
                ),
            ),
            'promote' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/promote',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action'     => 'promote',
                    ),
                ),
            ),
            'get-more-feeds' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/get-more-feeds/:category/:page',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action' => 'get-more-feeds',
                    ),
                ),
            ),
            'test' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/test',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action'     => 'test',
                    ),
                ),
            ),
            'contact' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/contact',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action'     => 'contact',
                    ),
                ),
            ),
        ),
    ),
    'service_manager' => array(
        'factories' => array(
            'contact_form' => 'Application\Factory\ContactFormFactory',
            'cache_service' => 'Zend\Cache\Service\StorageCacheFactory'
        ),
//        'abstract_factories' => array(
//            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
//            'Zend\Log\LoggerAbstractServiceFactory',
//        ),
        'aliases' => array(
            'translator' => 'MvcTranslator',
        ),
    ),
    'translator' => array(
        'locale' => 'en_US',
        'translation_file_patterns' => array(
            array(
                'type'     => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.mo',
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'Application\Controller\Index' => 'Application\Controller\IndexController'
        ),
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => array(
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
            'head'                    => __DIR__ . '/../view/partial/head.phtml',
            'footer'                  => __DIR__ . '/../view/partial/footer.phtml',
            'navigation'              => __DIR__ . '/../view/partial/navigation.phtml',
            'navigation_mobile'       => __DIR__ . '/../view/partial/navigation.mobile.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
        'strategies' => array(
            'ViewJsonStrategy'
        )
    ),
);
