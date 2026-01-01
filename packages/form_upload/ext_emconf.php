<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Form Upload',
    'description' => '',
    'category' => 'templates',
    'constraints' => [
        'depends' => [
            'bootstrap_package' => '15.0.0-15.99.99',
        ],
        'conflicts' => [
        ],
    ],
    'autoload' => [
        'psr-4' => [
            'BrezoItGmbh\\FormUpload\\' => 'Classes',
        ],
    ],
    'state' => 'stable',
    'uploadfolder' => 0,
    'createDirs' => '',
    'clearCacheOnLoad' => 1,
    'author' => 'Maik Preuss',
    'author_email' => 'maik.preuss@gmx.de',
    'author_company' => 'brezo IT GmbH',
    'version' => '1.0.0',
];
