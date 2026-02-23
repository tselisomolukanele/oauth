<?php

declare(strict_types=1);

require_once __DIR__ . '/../entity/AuthCodeEntity.php';

use League\OAuth2\Server\Entities\AuthCodeEntityInterface;
use League\OAuth2\Server\Repositories\AuthCodeRepositoryInterface;

class AuthCodeRepository implements AuthCodeRepositoryInterface
{
    public function persistNewAuthCode(AuthCodeEntityInterface $authCodeEntity): void
    {
        // Some logic to persist the auth code to a database
    }

    public function revokeAuthCode($codeId): void
    {
        // Some logic to revoke the auth code in a database
    }

    public function isAuthCodeRevoked($codeId): bool
    {
        return false; // The auth code has not been revoked
    }

    public function getNewAuthCode(): AuthCodeEntityInterface
    {
        return new AuthCodeEntity();
    }
}

?>