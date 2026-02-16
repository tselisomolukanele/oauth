<?php

require_once('../../vendor/autoload.php');
require_once('../entity/UserEntity.php');

use Laminas\Diactoros\Stream;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

session_start();

$log = new Logger('app');
$log->pushHandler(new StreamHandler('php://stdout', Logger::DEBUG)); 

function isAuthenticated(): bool
{
    return isset($_SESSION['user_id']);
}

function authenticate(string $username, string $password): bool
{
    if ($username === 'user' && $password === 'pass') {
        $_SESSION['user_id'] = 'user-123';
        return true;
    }
    return false;
}

$app->post('/authorize', function (ServerRequestInterface $request, ResponseInterface $response) use ($server, $log) {
            $data = (array)$request->getParsedBody();
        $username = $data['username'] ?? '';
        $password = $data['password'] ?? '';

        if (!authenticate($username, $password)) {
            $response->getBody()->write('Invalid credentials');
            return $response->withStatus(401);
        }

        try {
            if (!isset($_SESSION['auth_request'])) {
                $log->debug('Authorization session not set');
                throw new \RuntimeException('Missing authorization request context.');
            }

            /** @var \League\OAuth2\Server\AuthorizationRequest $authRequest */
            $authRequest = unserialize($_SESSION['auth_request']);
            unset($_SESSION['auth_request']);

            $authRequest->setUser(new UserEntity($_SESSION['user_id']));
            // In a real app, show a consent screen; here we auto-approve
            $authRequest->setAuthorizationApproved(true);

            return $server->completeAuthorizationRequest($authRequest, $response);
        } catch (OAuthServerException $exception) {
            return $exception->generateHttpResponse($response);
        } catch (\Exception $exception) {
            $response->getBody()->write($exception->getMessage());
            return $response->withStatus(500);
        }
});

$app->get('/authorize', function (ServerRequestInterface $request, ResponseInterface $response) use ($server, $log) {

    try {
        // Validate the HTTP request and return an AuthorizationRequest object.
        // The auth request object can be serialized into a user's session
        $authRequest = $server->validateAuthorizationRequest($request);

        $log->debug('Authorization request validated', ['client_id' => $authRequest->getClient()->getIdentifier()]);
        $authSession = serialize($authRequest);
        $_SESSION['auth_request'] = $authSession;
        $log->debug('Authorization session set on session'.$_SESSION['auth_request']);

        if (!isAuthenticated()) {
                $html = <<<HTML
<!doctype html>
<html>
  <head><title>Login</title></head>
  <body>
    <h1>Login to approve access</h1>
    <form method="POST" action="/authorize">
      <label>Username: <input type="text" name="username" /></label><br/>
      <label>Password: <input type="password" name="password" /></label><br/>
      <button type="submit">Login</button>
    </form>
  </body>
</html>
HTML;
                $response->getBody()->write($html);
                return $response->withHeader('Content-Type', 'text/html');
            }

        // Once the user has logged in set the user on the AuthorizationRequest
        $authRequest->setUser(new UserEntity());

        // Once the user has approved or denied the client update the status
        // (true = approved, false = denied)
        $authRequest->setAuthorizationApproved(true);

        // Return the HTTP redirect response
        return $server->completeAuthorizationRequest($authRequest, $response);
    } catch (OAuthServerException $exception) {

        return $exception->generateHttpResponse($response);
    } catch (Exception $exception) {
        $body = new Stream('php://temp', 'r+');
        $body->write($exception->getMessage());

        return $response->withStatus(500)->withBody($body);
    }
});

?>