<?php

return [
    'propel' => [
        'database' => [
            'connections' => [
                'oauth' => [
                    'adapter'    => 'pgsql',
                    'classname'  => 'Propel\Runtime\Connection\ConnectionWrapper',
                    'dsn'        => 'pgsql:host=localhost;port=5432;dbname=oauth',
                    'user'       => 'postgres',
                    'password'   => 'rO0tuser',
                    'attributes' => [],
                    'settings'   => [
                        'charset' => 'utf8',
                        'queries' => [
                            'utf8' => "SET NAMES 'UTF8'",
                        ],
                    ],
                ],
            ],
        ],
        'runtime' => [
            'defaultConnection' => 'oauth',
            'connections' => ['oauth'],
        ],
        'generator' => [
            'defaultConnection' => 'oauth',
            'connections' => ['oauth'],
            'platformClass' => 'Propel\Generator\Platform\PgsqlPlatform',
        ],
        'paths' => [
            'projectDir' => __DIR__,
            'schemaDir' => __DIR__,
            'outputDir' => __DIR__,
            'phpDir' => __DIR__ . '/generated-classes',
            'phpConfDir' => __DIR__ . '/generated-conf',
            'sqlDir' => __DIR__ . '/generated-sql',
        ],
    ],
];
