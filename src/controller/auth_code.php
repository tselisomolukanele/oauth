<?php

require_once('../../vendor/autoload.php');
require_once('../entity/UserEntity.php');

use Laminas\Diactoros\Stream;
use League\OAuth2\Server\Exception\OAuthServerException;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

$logger = new Logger('name');
$logger->pushHandler(new StreamHandler('php://stdout', Logger::DEBUG));

$app->get('/authorize', function (ServerRequestInterface $request, ResponseInterface $response) use ($server, $logger) {

    $logger->info('Authorize request received');
    $logger->info('User ID: ' . $_SESSION['user_id']);  

    // If the user is not logged in, validate and store the authorization
    // request, then show the login screen.
    if (empty($_SESSION['user_id'])) {
        $logger->info('User not logged in, redirecting to login');
        try {
            $authRequest = $server->validateAuthorizationRequest($request);
            $_SESSION['authRequest'] = serialize($authRequest);

            // Redirect to the login route which will show the login form.
            return $response
                ->withStatus(302)
                ->withHeader('Location', '/login');
        } catch (OAuthServerException $exception) {
            return $exception->generateHttpResponse($response);
        } catch (Exception $exception) {
            $body = new Stream('php://temp', 'r+');
            $body->write($exception->getMessage());

            return $response->withStatus(500)->withBody($body);
        }
    }

    // User is already logged in; continue the normal authorization flow.
    try {
        $authRequest = $server->validateAuthorizationRequest($request);

        $authRequest->setUser(new UserEntity((string) $_SESSION['user_id']));
        $authRequest->setAuthorizationApproved(true);

        return $server->completeAuthorizationRequest($authRequest, $response);
    } catch (OAuthServerException $exception) {
        return $exception->generateHttpResponse($response);
    } catch (Exception $exception) {
        $logger->error('Error: ' . $exception->getMessage());
        $body = new Stream('php://temp', 'r+');
        $body->write($exception->getMessage());

        return $response->withStatus(500)->withBody($body);
    }
});

?>