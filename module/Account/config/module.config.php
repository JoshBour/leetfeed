<?php
namespace Account;

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
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/login[/redirect/:redirectUrl]',
                    'defaults' => array(
                        'controller' => 'Account\Controller\Account',
                        'action' => 'login',
                    ),
                ),
            ),
            'logout' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/logout',
                    'defaults' => array(
                        'controller' => 'Account\Controller\Account',
                        'action' => 'logout',
                    ),
                ),
            ),
            'register' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/register',
                    'defaults' => array(
                        'controller' => 'Account\Controller\Account',
                        'action' => 'register',
                    ),
                ),
            ),
            'account' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/account',
                    'defaults' => array(
                        'controller' => 'Account\Controller\Account',
                    ),
                ),
                'may_terminate' => false,
                'child_routes' => array(
                    'summoners' => array(
                        'type' => 'Zend\Mvc\Router\Http\Literal',
                        'options' => array(
                            'route' => '/summoners',
                            'defaults' => array(
                                'action' => 'summoners',
                            ),
                        ),
                    ),
                    'remove-summoner' => array(
                        'type' => 'Zend\Mvc\Router\Http\Literal',
                        'options' => array(
                            'route' => '/remove-summoner',
                            'defaults' => array(
                                'action' => 'remove-summoner',
                            ),
                        ),
                    ),
                )
            )
        ),
    ),
    'service_manager' => array(
        'invokables' => array(
            'authStorage' => 'Account\Model\AuthStorage',
            'account_service' => 'Account\Service\Account'
        ),
        'factories' => array(
            'Zend\Authentication\AuthenticationService' => 'Account\Factory\AuthFactory',
            'account_login_form' => 'Account\Factory\LoginFormFactory',
            'account_register_form' => 'Account\Factory\RegisterFormFactory',
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
            'account' => 'Account\Factory\AccountPluginFactory'
        )
    ),
    'view_helpers' => array(
        'factories' => array(
            'account' => 'Account\Factory\AccountViewHelperFactory'
        ),
        'invokables' => array(
            'mobile' => 'Account\View\Helper\Mobile'
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
