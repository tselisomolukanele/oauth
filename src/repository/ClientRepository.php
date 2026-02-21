<?php

declare(strict_types=1);

require_once('../entity/ClientEntity.php');

use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;

class ClientRepository implements ClientRepositoryInterface
{
    private const CLIENT_NAME = 'THE_CLIENT_ID';
    private const REDIRECT_URI = 'http://localhost:8081/callback';

    public function getClientEntity($clientIdentifier): ?ClientEntityInterface
    {
        $client = new ClientEntity();

        $client->setIdentifier($clientIdentifier);
        $client->setName(self::CLIENT_NAME);
        $client->setRedirectUri(self::REDIRECT_URI);
        $client->setConfidential();

        return $client;
    }

    public function validateClient($clientIdentifier, $clientSecret, $grantType): bool
    {
        $clients = [
            'THE_CLIENT_ID' => [
                'secret'          => password_hash('abc123', PASSWORD_BCRYPT),
                'name'            => self::CLIENT_NAME,
                'redirect_uri'    => self::REDIRECT_URI,
                'is_confidential' => true,
            ],
        ];

        // Check if client is registered
        if (array_key_exists($clientIdentifier, $clients) === false) {
            return false;
        }

        if (password_verify($clientSecret, $clients[$clientIdentifier]['secret']) === false) {
            return false;
        }

        return true;
    }
}

?>