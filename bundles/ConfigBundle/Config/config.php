<?php

return [
    'routes' => [
        'main' => [
            'milex_config_action' => [
                'path'       => '/config/{objectAction}/{objectId}',
                'controller' => 'MauticConfigBundle:Config:execute',
            ],
            'milex_sysinfo_index' => [
                'path'       => '/sysinfo',
                'controller' => 'MauticConfigBundle:Sysinfo:index',
            ],
        ],
    ],

    'menu' => [
        'admin' => [
            'milex.config.menu.index' => [
                'route'           => 'milex_config_action',
                'routeParameters' => ['objectAction' => 'edit'],
                'iconClass'       => 'fa-cogs',
                'id'              => 'milex_config_index',
                'access'          => 'admin',
            ],
            'milex.sysinfo.menu.index' => [
                'route'     => 'milex_sysinfo_index',
                'iconClass' => 'fa-life-ring',
                'id'        => 'milex_sysinfo_index',
                'access'    => 'admin',
                'checks'    => [
                    'parameters' => [
                        'sysinfo_disabled' => false,
                    ],
                ],
            ],
        ],
    ],

    'services' => [
        'events' => [
            'milex.config.subscriber' => [
                'class'     => \Mautic\ConfigBundle\EventListener\ConfigSubscriber::class,
                'arguments' => [
                    'milex.config.config_change_logger',
                ],
            ],
        ],

        'forms' => [
            'milex.form.type.config' => [
                'class'     => \Mautic\ConfigBundle\Form\Type\ConfigType::class,
                'arguments' => [
                    'milex.config.form.restriction_helper',
                    'milex.config.form.escape_transformer',
                ],
            ],
        ],
        'models' => [
            'milex.config.model.sysinfo' => [
                'class'     => \Mautic\ConfigBundle\Model\SysinfoModel::class,
                'arguments' => [
                    'milex.helper.paths',
                    'milex.helper.core_parameters',
                    'translator',
                    'doctrine.dbal.default_connection',
                    'milex.install.service',
                    'milex.install.configurator.step.check',
                ],
            ],
        ],
        'others' => [
            'milex.config.mapper' => [
                'class'     => \Mautic\ConfigBundle\Mapper\ConfigMapper::class,
                'arguments' => [
                    'milex.helper.core_parameters',
                ],
            ],
            'milex.config.form.restriction_helper' => [
                'class'     => \Mautic\ConfigBundle\Form\Helper\RestrictionHelper::class,
                'arguments' => [
                    'translator',
                    '%milex.security.restrictedConfigFields%',
                    '%milex.security.restrictedConfigFields.displayMode%',
                ],
            ],
            'milex.config.config_change_logger' => [
                'class'     => \Mautic\ConfigBundle\Service\ConfigChangeLogger::class,
                'arguments' => [
                    'milex.helper.ip_lookup',
                    'milex.core.model.auditlog',
                ],
            ],
            'milex.config.form.escape_transformer' => [
                'class'     => \Mautic\ConfigBundle\Form\Type\EscapeTransformer::class,
                'arguments' => [
                    '%milex.config_allowed_parameters%',
                ],
            ],
        ],
    ],

    'parameters' => [
        'config_allowed_parameters' => [
            'kernel.root_dir',
            'kernel.project_dir',
            'kernel.logs_dir',
        ],
    ],
];
