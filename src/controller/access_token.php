<?php

use Laminas\Diactoros\Stream;
use League\OAuth2\Server\Exception\OAuthServerException;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

$logger = new Logger('name');
$logger->pushHandler(new StreamHandler('php://stdout', Logger::DEBUG));

$app->post('/access_token', function (ServerRequestInterface $request, ResponseInterface $response) use ($server, $logger) {
    $logger->info('Access token request received');

    try {
        return $server->respondToAccessTokenRequest($request, $response);
    } catch (OAuthServerException $exception) {
        return $exception->generateHttpResponse($response);
    } catch (Exception $exception) {
        $logger->error('Error processing access token request: ' . $exception->getTraceAsString());
        $body = new Stream('php://temp', 'r+');
        $body->write($exception->getMessage());

        return $response->withStatus(500)->withBody($body);
    }
});

?>