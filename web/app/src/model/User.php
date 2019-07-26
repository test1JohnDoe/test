<?php

namespace App\Acme\model;

class User
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $userName;

    /**
     * @var string
     */
    private $passwordHash;

    /**
     * User constructor.
     * @param int $id
     * @param string $userName
     * @param string $passwordHash
     */
    public function __construct(int $id, string $userName, string $passwordHash)
    {
        $this->id = $id;
        $this->userName = $userName;
        $this->passwordHash = $passwordHash;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getUserName(): string
    {
        return $this->userName;
    }

    /**
     * @return string
     */
    public function getPasswordHash(): string
    {
        return $this->passwordHash;
    }

}
