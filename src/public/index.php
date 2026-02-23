<?php

session_start();

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../helper/setup_server.php';
require_once __DIR__ . '/../controller/auth_code.php';
require_once __DIR__ . '/../controller/access_token.php';
require_once __DIR__ . '/../controller/login.php';

$app->get('/', function (Request $request, Response $response, array $args) {
    $response->getBody()->write("Welcome to the OAuth Server!");

    return $response;
});

$app->run();

?>