<?php


use Eoko\ODM\Fixture\Controller\FixtureController;
use Eoko\ODM\Fixture\Executor\Executor;
use Eoko\ODM\Fixture\Factory\ExecutorFactory;

return [
    'service_manager' => [
        'factories' => [
            Executor::class => ExecutorFactory::class
        ],
    ],

    'controllers' => array(
        'invokables' => [
            FixtureController::class => FixtureController::class,
        ],
    ),

    'console' => [
        'router' => [
            'routes' => [
                'eoko-odm-fixtures-tables' => [
                    'options' => [
                        'route' => 'eoko fixture load [--one=]',
                        'defaults' => [
                            'controller' => FixtureController::class,
                            'action' => 'load'
                        ],
                    ],
                ],
                'eoko-odm-fixtures-entities' => [
                    'options' => [
                        'route' => 'eoko fixture unload [--one=]',
                        'defaults' => [
                            'controller' => FixtureController::class,
                            'action' => 'unload'
                        ],
                    ],
                ],
            ],
        ],
    ],
];