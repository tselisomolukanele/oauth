<?php

require_once('../entity/UserEntity.php');

use Laminas\Diactoros\Stream;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

$app->get('/login', function (ServerRequestInterface $request, ResponseInterface $response) {
    $html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f5f5f5; }
        .container { max-width: 400px; margin: 80px auto; padding: 24px; background: #fff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
        h1 { font-size: 22px; margin-bottom: 16px; text-align: center; }
        label { display: block; margin: 8px 0 4px; font-size: 14px; }
        input[type="text"], input[type="password"] {
            width: 100%; padding: 8px 10px; border-radius: 4px; border: 1px solid #ccc; box-sizing: border-box;
        }
        button {
            margin-top: 16px; width: 100%; padding: 10px 12px;
            background: #007bff; border: none; color: #fff;
            border-radius: 4px; font-size: 15px; cursor: pointer;
        }
        button:hover { background: #0069d9; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Login</h1>
        <form method="post" action="/login">
            <label for="username">Username</label>
            <input id="username" name="username" type="text" required />

            <label for="password">Password</label>
            <input id="password" name="password" type="password" required />

            <button type="submit">Sign in</button>
        </form>
    </div>
</body>
</html>
HTML;

    $body = new Stream('php://temp', 'r+');
    $body->write("$html");

    return $response
        ->withHeader('Content-Type', 'text/html; charset=utf-8')
        ->withBody($body);
});

$app->post('/login', function (ServerRequestInterface $request, ResponseInterface $response) use ($server) {
    $params = $request->getParsedBody() ?? [];
    $username = $params['username'] ?? '';
    $password = $params['password'] ?? '';

    // Very simple hard-coded credentials for demo purposes.
    $validUsername = 'user';
    $validPassword = 'password';

    if ($username !== $validUsername || $password !== $validPassword) {
        $body = new Stream('php://temp', 'r+');
        $body->write('Invalid username or password');

        return $response->withStatus(401)->withBody($body);
    }

    $_SESSION['user_id'] = $username;

    if (empty($_SESSION['authRequest'])) {
        $body = new Stream('php://temp', 'r+');
        $body->write('No pending authorization request.');

        return $response->withStatus(400)->withBody($body);
    }

    try {
        $authRequest = unserialize($_SESSION['authRequest']);
        unset($_SESSION['authRequest']);

        $authRequest->setUser(new UserEntity($_SESSION['user_id']));
        $authRequest->setAuthorizationApproved(true);

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