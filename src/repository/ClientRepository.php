<?php

declare(strict_types=1);

require_once __DIR__ . '/../entity/ClientEntity.php';

use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;
use Propel\Runtime\Propel;

class ClientRepository implements ClientRepositoryInterface
{
    private const TABLE_NAME = 'oauth_client';

    public function getClientEntity($clientIdentifier): ?ClientEntityInterface
    {
        $row = $this->findClientByIdentifier((string) $clientIdentifier);
        if ($row === null) {
            return null;
        }

        $client = new ClientEntity();
        $client->setIdentifier($row['identifier']);
        $client->setName($row['name']);
        $client->setRedirectUri($row['redirect_uri']);
        if (!empty($row['is_confidential'])) {
            $client->setConfidential();
        }

        return $client;
    }

    public function validateClient($clientIdentifier, $clientSecret, $grantType): bool
    {
        $row = $this->findClientByIdentifier((string) $clientIdentifier);
        if ($row === null) {
            return false;
        }

        if (empty($row['secret'])) {
            return false;
        }

        return password_verify($clientSecret, $row['secret']);
    }

    /**
     * @return array<string, mixed>|null
     */
    private function findClientByIdentifier(string $identifier): ?array
    {
        $con = Propel::getServiceContainer()->getReadConnection('oauth');
        $stmt = $con->prepare(
            'SELECT id, identifier, secret, name, redirect_uri, is_confidential FROM ' . self::TABLE_NAME . ' WHERE identifier = :identifier LIMIT 1'
        );
        $stmt->execute(['identifier' => $identifier]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $row ?: null;
    }
}
