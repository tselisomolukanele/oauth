<?php

declare(strict_types=1);

use League\OAuth2\Server\Entities\UserEntityInterface;

class UserEntity implements UserEntityInterface
{
    /**
     * @var string
     */
    private $identifier;

    public function __construct(string $identifier)
    {
        $this->identifier = $identifier;
    }

    /**
     * Return the user's identifier.
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }
}

?>