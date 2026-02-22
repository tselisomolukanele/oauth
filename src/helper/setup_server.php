<?php

declare(strict_types=1);

require_once __DIR__ . '/../../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../..');
$dotenv->load();

require_once __DIR__ . '/../../generated-conf/config.php';
require_once __DIR__ . '/../repository/ClientRepository.php';
require_once(__DIR__ . '/../repository/ScopeRepository.php');
require_once(__DIR__ . '/../repository/AccessTokenRepository.php');
require_once(__DIR__ . '/../repository/AuthCodeRepository.php');
require_once(__DIR__ . '/../repository/RefreshTokenRepository.php');
require_once(__DIR__ . '/../middleware/CorsMiddleware.php');

use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Grant\AuthCodeGrant;
use Slim\App;

// Override Propel connection with env-based config (so .env is used at runtime)
/** @var \Propel\Runtime\ServiceContainer\StandardServiceContainer $serviceContainer */
$serviceContainer = \Propel\Runtime\Propel::getServiceContainer();
$manager = new \Propel\Runtime\Connection\ConnectionManagerSingle('oauth');
$manager->setConfiguration([
    'classname' => 'Propel\Runtime\Connection\ConnectionWrapper',
    'dsn' => sprintf(
        'pgsql:host=%s;port=%s;dbname=%s',
        $_ENV['DB_HOST'] ?? 'localhost',
        $_ENV['DB_PORT'] ?? '5432',
        $_ENV['DB_NAME'] ?? 'oauth'
    ),
    'user' => $_ENV['DB_USER'] ?? 'postgres',
    'password' => $_ENV['DB_PASSWORD'] ?? '',
    'attributes' => [
        'ATTR_EMULATE_PREPARES' => false,
        'ATTR_TIMEOUT' => 30,
    ],
    'settings' => [
        'charset' => 'utf8',
        'queries' => ['utf8' => "SET NAMES 'UTF8'"],
    ],
    'model_paths' => ['src', 'vendor'],
]);
$serviceContainer->setConnectionManager($manager);

$privateKeyPath = $_ENV['OAUTH_PRIVATE_KEY_PATH'] ?? '';
$encryptionKey = $_ENV['OAUTH_ENCRYPTION_KEY'] ?? '';
$authCodeTtl = $_ENV['AUTH_CODE_TTL'] ?? 'PT10M';
$accessTokenTtl = $_ENV['ACCESS_TOKEN_TTL'] ?? 'PT1H';

function getServer(string $privateKeyPath, string $encryptionKey, string $authCodeTtl, string $accessTokenTtl) {
    $clientRepository = new ClientRepository();
    $scopeRepository = new ScopeRepository();
    $accessTokenRepository = new AccessTokenRepository();
    $authCodeRepository = new AuthCodeRepository();
    $refreshTokenRepository = new RefreshTokenRepository();

    $server = new AuthorizationServer(
        $clientRepository,
        $accessTokenRepository,
        $scopeRepository,
        $privateKeyPath,
        $encryptionKey
    );

    $server->enableGrantType(
        new AuthCodeGrant(
            $authCodeRepository,
            $refreshTokenRepository,
            new DateInterval($authCodeTtl)
        ),
        new DateInterval($accessTokenTtl)
    );

    return $server;
}

$server = getServer($privateKeyPath, $encryptionKey, $authCodeTtl, $accessTokenTtl);

$app = new App([
    'settings' => [
        'displayErrorDetails' => ($_ENV['APP_DEBUG'] ?? 'false') === 'true',
    ],
]);

$corsOrigins = array_map('trim', explode(',', $_ENV['CORS_ALLOWED_ORIGINS'] ?? ''));
$app->add(new CorsMiddleware($corsOrigins));

?>