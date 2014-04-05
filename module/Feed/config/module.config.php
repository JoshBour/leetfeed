<?php
namespace Feed;

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
    ),
    'router' => array(
        'routes' => array(
            'history' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/history[/:sort]',
                    'defaults' => array(
                        'controller' => 'Feed\Controller\Feed',
                        'action' => 'history',
                        'sort' => 'now'
                    ),
                ),
            ),
            'random' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/random',
                    'defaults' => array(
                        'controller' => 'Feed\Controller\Feed',
                        'action' => 'random',
                    ),
                ),
            ),
            'famous' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/famous',
                    'defaults' => array(
                        'controller' => 'Feed\Controller\Feed',
                        'action' => 'famous',
                    ),
                ),
            ),
            'leet' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/leet',
                    'defaults' => array(
                        'controller' => 'Feed\Controller\Feed',
                        'action' => 'leet',
                    ),
                ),
            ),
            'improve' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/improve[/:summonerId]',
                    'defaults' => array(
                        'controller' => 'Feed\Controller\Feed',
                        'action' => 'improve',
                    ),
                ),
            ),
            'feed' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/feed',
                    'defaults' => array(
                        'controller' => 'Feed\Controller\Feed'
                    )
                ),
                'may_terminate' => false,
                'child_routes' => array(
                    'add-to-watched' => array(
                        'type' => 'Zend\Mvc\Router\Http\Segment',
                        'options' => array(
                            'route' => '/add-to-watched/:feedId',
                            'defaults' => array(
                                'action' => 'add-to-watched',
                            ),
                            'constraints' => array(
                                'feedId' => '[0-9]+'
                            )
                        ),
                    ),
                    'get-random-feed' => array(
                        'type' => 'Zend\Mvc\Router\Http\Literal',
                        'options' => array(
                            'route' => '/get-random-feed',
                            'defaults' => array(
                                'action' => 'get-random-feed',
                            ),
                        ),
                    ),
                    'generate' => array(
                        'type' => 'Zend\Mvc\Router\Http\Segment',
                        'options' => array(
                            'route' => '/generate[/:nextToken]',
                            'defaults' => array(
                                'action' => 'generate',
                                'nextToken' => null,
                            ),
                        ),
                    ),
                    'view' => array(
                        'type' => 'Zend\Mvc\Router\Http\Segment',
                        'options' => array(
                            'route' => '/:feedId',
                            'defaults' => array(
                                'action' => 'view',
                            ),
                            'constraints' => array(
                                'feedId' => '[0-9]+',
                            ),
                        ),
                    ),
                    'remove' => array(
                        'type' => 'Zend\Mvc\Router\Http\Segment',
                        'options' => array(
                            'route' => '/remove/:feedId/:type',
                            'defaults' => array(
                                'action' => 'remove',
                            ),
                            'constraints' => array(
                                'feedId' => '[0-9]+',
                                'type' => 'delete|ignore'
                            ),
                        ),
                    ),
                    'add' => array(
                        'type' => 'Zend\Mvc\Router\Http\Segment',
                        'options' => array(
                            'route' => '/add/[:feedId[/:type]]',
                            'defaults' => array(
                                'action' => 'add',
                                'type' => 'new'
                            ),
                            'constraints' => array(
                                'feedId' => '[0-9]+',
                                'type' => 'exists|new'
                            ),
                        ),
                    ),
                    'rate' => array(
                        'type' => 'Zend\Mvc\Router\Http\Segment',
                        'options' => array(
                            'route' => '/rate/:rating/id/:id',
                            'defaults' => array(
                                'action' => 'rate',
                            ),
                            'constraints' => array(
                                'id' => '[0-9]+',
                                'rating' => 'thumbUp|thumbDown'
                            ),
                        ),
                    ),
                    'get-youtuber-feeds' => array(
                        'type' => 'Zend\Mvc\Router\Http\Segment',
                        'options' => array(
                            'route' => '/get-youtuber-feeds/:youtuberName[/:returnFeeds[/:nextToken]]',
                            'defaults' => array(
                                'action' => 'get-youtuber-feeds',
                                'nextToken' => null,
                                'returnFeeds' => null,
                            ),
                        ),
                    ),
                )
            ),
            'comment' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/comment',
                    'defaults' => array(
                        'controller' => 'Feed\Controller\Comment',
                    ),
                ),
                'may_terminate' => false,
                'child_routes' => array(
                    'add' => array(
                        'type' => 'Zend\Mvc\Router\Http\Literal',
                        'options' => array(
                            'route' => '/add',
                            'defaults' => array(
                                'action' => 'add',
                            ),
                        ),
                    ),
                    'remove' => array(
                        'type' => 'Zend\Mvc\Router\Http\Literal',
                        'options' => array(
                            'route' => '/remove',
                            'defaults' => array(
                                'action' => 'remove',
                            ),
                        ),
                    ),
                    'list' => array(
                        'type' => 'Zend\Mvc\Router\Http\Segment',
                        'options' => array(
                            'route' => '/list/:feedId',
                            'defaults' => array(
                                'action' => 'list',
                            ),
                            'constraints' => array(
                                'feedId' => "[0-9]+"
                            )
                        ),
                    ),
                )
            ),
        ),
    ),
    'service_manager' => array(
        'invokables' => array(
            'feed_service' => 'Feed\Service\Feed',
            'comment_service' => 'Feed\Service\Comment',
            'generator' => 'Feed\Model\FeedGenerator',
        ),
        'factories' => array(
            'add_feed_form' => 'Feed\Factory\AddFeedFormFactory'
        )
    ),
    'controllers' => array(
        'invokables' => array(
            'Feed\Controller\Feed' => 'Feed\Controller\FeedController',
            'Feed\Controller\Comment' => 'Feed\Controller\CommentController'
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
        'strategies' => array(
            'ViewJsonStrategy'
        ),
        'template_map' => array(
            'feed' => __DIR__ . '/../view/feed/partial/feed.phtml',
            'improve_unregistered' => __DIR__ . '/../view/feed/feed/improve.unregistered.phtml',
            'comment' => __DIR__ . '/../view/feed/partial/comment.phtml'
        ),
    ),
);
