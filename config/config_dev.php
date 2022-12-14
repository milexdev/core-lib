<?php

$loader->import('config.php');

if (file_exists(__DIR__.'/security_local.php')) {
    $loader->import('security_local.php');
} else {
    $loader->import('security.php');
}

//Twig Configuration
$container->loadFromExtension('twig', [
    'cache'            => false,
    'debug'            => '%kernel.debug%',
    'strict_variables' => true,
    'paths'            => [
        '%kernel.root_dir%/bundles' => 'bundles',
    ],
    'form_themes' => [
        // Can be found at bundles/CoreBundle/Resources/views/milex_form_layout.html.twig
        '@MilexCore/FormTheme/milex_form_layout.html.twig',
    ],
]);

$container->loadFromExtension('framework', [
    'router' => [
        'resource'            => '%kernel.root_dir%/config/routing_dev.php',
        'strict_requirements' => true,
    ],
    'profiler' => [
        'only_exceptions' => false,
    ],
]);

$container->loadFromExtension('web_profiler', [
    'toolbar'             => true,
    'intercept_redirects' => false,
]);

$container->loadFromExtension('monolog', [
    'channels' => [
        'milex',
        'chrome',
    ],
    'handlers' => [
        'main' => [
            'formatter' => 'milex.monolog.fulltrace.formatter',
            'type'      => 'rotating_file',
            'path'      => '%kernel.logs_dir%/%kernel.environment%.php',
            'level'     => 'debug',
            'channels'  => [
                '!milex',
            ],
            'max_files' => 7,
        ],
        'console' => [
            'type'   => 'console',
            'bubble' => false,
        ],
        'milex' => [
            'formatter' => 'milex.monolog.fulltrace.formatter',
            'type'      => 'rotating_file',
            'path'      => '%kernel.logs_dir%/milex_%kernel.environment%.php',
            'level'     => 'debug',
            'channels'  => [
                'milex',
            ],
            'max_files' => 7,
        ],
        'chrome' => [
            'type'     => 'chromephp',
            'level'    => 'debug',
            'channels' => [
                'chrome',
            ],
        ],
    ],
]);

// Allow overriding config without a requiring a full bundle or hacks
if (file_exists(__DIR__.'/config_override.php')) {
    $loader->import('config_override.php');
}

// Allow local settings without committing to git such as swift mailer delivery address overrides
if (file_exists(__DIR__.'/config_local.php')) {
    $loader->import('config_local.php');
}
