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
//            'about' => array(
//                'type' => 'Zend\Mvc\Router\Http\Literal',
//                'options' => array(
//                    'route'    => '/about',
//                    'defaults' => array(
//                        'controller' => 'Application\Controller\Index',
//                        'action'     => 'about',
//                    ),
//                ),
//            ),
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
            'contact_form' => function ($sm) {
                    $fieldset = new Form\ContactFieldset($sm->get('translator'));
                    $form = new Form\ContactForm();

                    $form->add($fieldset)
                        ->setInputFilter(new InputFilter());
                    return $form;
                },
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
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
        'strategies' => array(
            'ViewJsonStrategy'
        )
    ),
);
