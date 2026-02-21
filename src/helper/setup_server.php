<?php

declare(strict_types=1);

require_once('../../vendor/autoload.php');
require_once('../repository/ClientRepository.php');
require_once('../repository/ScopeRepository.php');
require_once('../repository/AccessTokenRepository.php');
require_once('../repository/AuthCodeRepository.php');
require_once('../repository/RefreshTokenRepository.php');
require_once('../middleware/CorsMiddleware.php');

use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Grant\AuthCodeGrant;
use Slim\App;

$privateKeyPath = '/home/tseliso/workspace/config/oauth.private.key';
$encryptionKey = 'Gx5M5Nr2L2vir1VKg3DkOZ1ywYBosKBfrcpc9vwiTP0=';

function getServer($privateKeyPath, $encryptionKey) {
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
                new DateInterval('PT10M')
            ),
            new DateInterval('PT1H')
        );

    return $server;
}

$server = getServer($privateKeyPath, $encryptionKey);

$app = new App([
    'settings' => [
        'displayErrorDetails' => true,
    ],
]);

$app->add(new CorsMiddleware(['http://localhost:8081']));

?>