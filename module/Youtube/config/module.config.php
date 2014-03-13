<?php
namespace Feed;

use \Zend\InputFilter\InputFilter;

return array(
    'router' => array(
        'routes' => array(
            'rate' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'    => '/feed/rate/:rating/id/:id',
                    'defaults' => array(
                        'controller' => 'Feed\Controller\Feed',
                        'action'     => 'rate',
                    ),
                    'constraints' => array(
                        'id' => '[0-9]+',
                        'rating' => 'thumbUp|thumbDown'
                    ),
                ),
            ),
            'view' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'    => '/feed/:feedId',
                    'defaults' => array(
                        'controller' => 'Feed\Controller\Feed',
                        'action'     => 'view',
                    ),
                    'constraints' => array(
                        'feedId' => '[0-9]+',
                    ),
                ),
            ),
            'history' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/history',
                    'defaults' => array(
                        'controller' => 'Feed\Controller\Feed',
                        'action'     => 'history',
                    ),
                ),
            ),
            'famous' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/famous',
                    'defaults' => array(
                        'controller' => 'Feed\Controller\Feed',
                        'action'     => 'famous',
                    ),
                ),
            ),
            'leet' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/leet',
                    'defaults' => array(
                        'controller' => 'Feed\Controller\Feed',
                        'action'     => 'leet',
                    ),
                ),
            ),
            'get-youtuber-feeds' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'    => '/get-youtuber-feeds/:youtuberName',
                    'defaults' => array(
                        'controller' => 'Feed\Controller\Feed',
                        'action'     => 'get-youtuber-feeds',
                    ),
                ),
            ),
        ),
    ),
    'service_manager' => array(
        'invokables' => array(
                'feed_service' => 'Feed\Service\Feed',
                'generator' => 'Feed\Model\FeedGenerator',
                'youtube_service' => 'Feed\Service\Youtube',
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'Feed\Controller\Feed' => 'Feed\Controller\FeedController'
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
            'feed' => __DIR__ . '/../view/feed/partial/feed.phtml'
        ),
    ),
);
