<?php

if (file_exists(__DIR__ . '/.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
}

$dbHost = getenv('DB_HOST') ?: 'localhost';
$dbPort = getenv('DB_PORT') ?: '5432';
$dbName = getenv('DB_NAME') ?: 'oauth';
$dbUser = getenv('DB_USER') ?: 'postgres';
$dbPassword = getenv('DB_PASSWORD') ?: '';

return [
    'propel' => [
        'database' => [
            'connections' => [
                'oauth' => [
                    'adapter'    => 'pgsql',
                    'classname'  => 'Propel\Runtime\Connection\ConnectionWrapper',
                    'dsn'        => "pgsql:host={$dbHost};port={$dbPort};dbname={$dbName}",
                    'user'       => $dbUser,
                    'password'   => $dbPassword,
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
