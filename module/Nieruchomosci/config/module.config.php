<?php

namespace Nieruchomosci;

use Nieruchomosci\Controller\KoszykController;
use Nieruchomosci\Model\Koszyk;
use Nieruchomosci\Controller\OfertyController;
use Nieruchomosci\Model\Oferta;
use Laminas\Mvc\Controller\LazyControllerAbstractFactory;
use Laminas\Navigation\Service\DefaultNavigationFactory;
use Laminas\Router\Http\Literal;
use Laminas\Router\Http\Segment;
use Laminas\ServiceManager\Factory\InvokableFactory;

return [
    'router' => [
        'routes' => [
            'nieruchomosci' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/',
                    'defaults' => [
                        'controller' => OfertyController::class,
                        'action' => 'lista',
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'oferty' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => 'oferty[/:action[/:id]]',
                        ],
                    ],
                    'koszyk' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => 'koszyk[/:action[/:id]]',
                            'defaults' => [
                                'controller' => KoszykController::class,
                                'action' => 'lista',
                            ],
                        ],
                    ],
                ],
            ]
        ]
    ],
    'controllers' => [
        'factories' => [
            OfertyController::class => LazyControllerAbstractFactory::class,
            KoszykController::class => LazyControllerAbstractFactory::class,
        ],
    ],
    'service_manager' => [
        'factories' => [
            'navigation' => DefaultNavigationFactory::class,
            Oferta::class => InvokableFactory::class,
            Koszyk::class => InvokableFactory::class,
        ],
    ],
    'view_manager' => [
        'template_map' => [
            'layout/nieruchomosci' => __DIR__ . '/../view/layout/nieruchomosci.phtml',
            'menu' => __DIR__ . '/../view/partial/menu.phtml',
        ],
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
    'navigation' => [
        'default' => [
            [
                'label' => 'Oferty',
                'route' => 'nieruchomosci/oferty',
                'action' => 'lista'
            ],
            [
                'label' => 'Koszyk',
                'route' => 'nieruchomosci/koszyk',
                'action' => 'lista'
            ],
        ],
    ],
];
