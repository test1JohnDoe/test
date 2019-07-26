<?php

namespace App\Acme\repository;

use App\Acme\model\User;
use App\Acme\service\DatabaseHelper;
use App\Acme\service\DatabaseService;

class UserDatabaseRepository
{
    use DatabaseHelper;

    private const USERS_TABLE_NAME = 'users';

    /**
     * @var DatabaseService
     */
    private $databaseService;

    /**
     * UserDatabaseRepository constructor.
     * @param DatabaseService $databaseService
     */
    public function __construct(DatabaseService $databaseService)
    {
        $this->databaseService = $databaseService;
    }

    /**
     * @param string $userName
     * @return User|null
     */
    public function getUserByUsername(string $userName):? User
    {
        $pdo = $this->databaseService::getPdo();
        $query = 'SELECT * FROM ' . self::USERS_TABLE_NAME . ' WHERE user_name = :userName';
        $sth = $pdo->prepare($query);
        $sth->execute([
            'userName' => $userName,
        ]);
        $userEntity = $sth->fetch(\PDO::FETCH_ASSOC);
        if (empty($userEntity['id'])) {
            return null;
        }

        return new User(
            $userEntity['id'],
            $userEntity['user_name'],
            $userEntity['password_hash']
        );
    }

    /**
     * @param string $userName
     * @param string $passwordHash
     * @return User|null
     */
    public function getUserByUsernameAndHash(string $userName, string $passwordHash):? User
    {
        $pdo = $this->databaseService::getPdo();
        $query = 'SELECT * FROM ' . self::USERS_TABLE_NAME . ' WHERE user_name = :user_name AND password_hash = :password_hash';
        $sth = $pdo->prepare($query);
        $sth->execute([
            'user_name' => $userName,
            'password_hash' => $passwordHash,
        ]);
        $userEntity = $sth->fetch(\PDO::FETCH_ASSOC);
        if (empty($userEntity['id'])) {
            return null;
        }

        return new User(
            $userEntity['id'],
            $userEntity['user_name'],
            $userEntity['password_hash']
        );
    }

    /**
     * @param int $userId
     * @return User|null
     */
    public function getUserByUserId(int $userId):? User
    {
        $pdo = $this->databaseService::getPdo();

        $query = 'SELECT * FROM ' . self::USERS_TABLE_NAME . ' WHERE id = ?';
        $sth = $pdo->prepare($query);
        $sth->execute([$userId]);
        $userEntity = $sth->fetch(\PDO::FETCH_ASSOC);
        if (empty($userEntity['id'])) {
            return null;
        }

        return new User(
            $userEntity['id'],
            $userEntity['user_name'],
            $userEntity['password_hash']
        );;
    }

    /**
     * @param array $userIds
     * @return array|User[]
     */
    public function getUsersByUserIds(array $userIds): array
    {
        $pdo = $this->databaseService::getPdo();

        $query = 'SELECT * FROM ' . self::USERS_TABLE_NAME . ' WHERE id IN (' . $this->buildQuestionMarks($userIds) . ')';
        $sth = $pdo->prepare($query);
        $sth->execute($userIds);
        $userEntities = $sth->fetchAll(\PDO::FETCH_ASSOC);
        $users = [];
        foreach ($userEntities as $userEntity) {
            $users[$userEntity['id']] = new User(
                $userEntity['id'],
                $userEntity['user_name'],
                $userEntity['password_hash']
            );
        }

        return $users;
    }

    /**
     * @param string $userName
     * @param string $passwordHash
     * @return User
     */
    public function saveUser(string $userName, string $passwordHash): User
    {
        $pdo = $this->databaseService::getPdo();

        $postQuery = 'INSERT INTO ' . self::USERS_TABLE_NAME . '(user_name, password_hash) VALUES (?,?)';
        $sth = $pdo->prepare($postQuery);
        $sth->execute([$userName, $passwordHash]);
        $userId = $pdo->lastInsertId();

        return new User(
            $userId,
            $userName,
            $passwordHash
        );
    }
}
