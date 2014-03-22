<?php
namespace League;

return array(
    'service_manager' => array(
        'invokables' => array(
            'league_service' => 'League\Service\League'
        ),
        'factories' => array(
            'summoner_form' => 'League\Factory\SummonerFormFactory'
        )
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
    'api' => array(
        'key' => '0835a046-d4ff-487a-81c2-be090525c595'
    ),
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
    )
);
