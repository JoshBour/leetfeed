<?php
namespace Account;

use \Zend\InputFilter\InputFilter;
use Zend\ServiceManager\ServiceManager;

use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;

return array(
    'doctrine' => array(
        'driver' => array(
            'entity' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'paths' => array(__DIR__ . '/../src/' . __NAMESPACE__ . '/Entity'),
            ),
            'orm_default' => array(
                'drivers' => array(
                    __NAMESPACE__ . '\Entity' => 'entity',
                ),
            ),
        ),
        'authentication' => array(
            'orm_default' => array(
                'object_manager' => 'Doctrine\ORM\EntityManager',
                'identity_class' => 'Account\Entity\Account',
                'identity_property' => 'username',
                'credential_property' => 'password',
                'credential_callable' => 'Account\Entity\Account::hashPassword'
            ),
        ),
    ),
    'router' => array(
        'routes' => array(
            'login' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/login',
                    'defaults' => array(
                        'controller' => 'Account\Controller\Account',
                        'action'     => 'login',
                    ),
                ),
            ),
            'logout' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/logout',
                    'defaults' => array(
                        'controller' => 'Account\Controller\Account',
                        'action'     => 'logout',
                    ),
                ),
            ),
            'register' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/register',
                    'defaults' => array(
                        'controller' => 'Account\Controller\Account',
                        'action'     => 'register',
                    ),
                ),
            ),
        ),
    ),
    'service_manager' => array(
        'invokables' => array(
          'authStorage'  => 'Account\Model\AuthStorage',
          'account_service' => 'Account\Service\Account'
        ),
        'factories' => array(
            'Zend\Authentication\AuthenticationService' => function(ServiceManager $sm){
                    $authService = $sm->get('doctrine.authenticationservice.orm_default');
                    $authService->setStorage($sm->get('AuthStorage'));
                    return $authService;
                }
        ,
            'account_login_form' => function(ServiceManager $sm){
                    $entityManager = $sm->get('Doctrine\ORM\EntityManager');
                    $fieldset = new Form\LoginFieldset($sm->get('translator'));
                    $form = new Form\LoginForm();
                    $hydrator = new DoctrineHydrator($entityManager, '\Account\Entity\Account');

                    $fieldset->setUseAsBaseFieldset(true)
                        ->setHydrator($hydrator)
                        ->setObject(new Entity\Account);

                    $form->add($fieldset)
                        ->setInputFilter(new InputFilter())
                        ->setHydrator($hydrator);

                    return $form;
                },
            'account_register_form' => function(ServiceManager $sm){
                    $entityManager = $sm->get('Doctrine\ORM\EntityManager');
                    $fieldset = new Form\RegisterFieldset($sm->get('translator'));
                    $form = new Form\RegisterForm();
                    $hydrator = new DoctrineHydrator($entityManager, '\Account\Entity\Account');

                    $fieldset->setAccountRepository($entityManager->getRepository('\Account\Entity\Account'))
                        ->setUseAsBaseFieldset(true)
                        ->setHydrator($hydrator)
                        ->setObject(new Entity\Account);

                    $form->add($fieldset)
                        ->setInputFilter(new InputFilter())
                        ->setHydrator($hydrator);
                    return $form;
                },
        ),
        'aliases' => array(
            'auth_service' => 'Zend\Authentication\AuthenticationService'
        )
    ),
    'controllers' => array(
        'invokables' => array(
            'Account\Controller\Account' => 'Account\Controller\AccountController'
        ),
        'aliases' => array(
            'account_controller' => 'Account\Controller\Account'
        )
    ),
    'controller_plugins' => array(
        'factories' => array(
            'account' => function($sm){
                    $plugin = new Plugin\ActiveAccount();
                    $plugin->setServiceManager($sm->getServiceLocator());
                    return $plugin;
                }
        )
    ),
    'view_helpers' => array(
        'factories' => array(
            'account' => function($sm){
                    $helper = new View\Helper\User();
                    $helper->setServiceManager($sm->getServiceLocator());
                    return $helper;
                }
        )
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
        'strategies' => array(
            'ViewJsonStrategy'
        )
    ),
);
