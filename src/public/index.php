<?php

session_start();

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require_once('../../vendor/autoload.php');
require_once('../helper/setup_server.php');
require_once('../controller/auth_code.php');
require_once('../controller/access_token.php');
require_once('../controller/login.php');

$app->get('/', function (Request $request, Response $response, array $args) {
    $response->getBody()->write("Welcome to the OAuth Server!");

    return $response;
});

$app->run();

?>