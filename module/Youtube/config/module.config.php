<?php
namespace Youtube;

use \Zend\InputFilter\InputFilter;

return array(
    'service_manager' => array(
        'invokables' => array(
            'youtube_service' => 'Youtube\Service\Youtube'
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
);
