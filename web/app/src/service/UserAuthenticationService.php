<?php

namespace App\Acme\service;

use App\Acme\repository\UserDatabaseRepository;

class UserAuthenticationService
{
    /**
     * @var UserDatabaseRepository
     */
    private $userDatabaseRepository;

    /**
     * UserAuthenticationService constructor.
     * @param UserDatabaseRepository $userDatabaseRepository
     */
    public function __construct(UserDatabaseRepository $userDatabaseRepository)
    {
        $this->userDatabaseRepository = $userDatabaseRepository;
    }

    /**
     * @param string $userName
     * @param string $userPassword
     * @return bool
     */
    public function auth(string $userName, string $userPassword): bool
    {
        $passwordHash = md5($userPassword);
        $user = $this->userDatabaseRepository->getUserByUsernameAndHash($userName, $passwordHash);

        if ($user === null) {
            return false;
        }

        $_SESSION['isLogged'] = true;
        $_SESSION['userName'] = $user->getUserName();
        $_SESSION['userId'] = $user->getId();
        return true;
    }

    /**
     * @param string $userName
     * @param string $userPassword
     * @param string $userPassword2
     * @return bool
     */
    public function signup(string $userName, string $userPassword, string $userPassword2): bool
    {
        if ($userPassword !== $userPassword2) {
            return false;
        }

        $user = $this->userDatabaseRepository->getUserByUsername($userName);
        if ($user !== null) {
            return false;
        }

        $passwordHash = md5($userPassword);
        $this->userDatabaseRepository->saveUser($userName, $passwordHash);

        return true;
    }

    /**
     * @return int
     */
    public function getCurrentUserId(): int
    {
        return $_SESSION['userId'];
    }

    /**
     * @return array
     */
    public function getUserData(): array
    {
        return [
            'isLogged' => $_SESSION['isLogged'],
            'userName' => $_SESSION['userName'],
            'userId' => $_SESSION['userId'],
        ];
    }

    /**
     * Logout function
     */
    public function logout(): void
    {
        session_destroy();
    }

    /**
     * @return bool
     */
    public function isLogged(): bool
    {
        return isset($_SESSION['isLogged']) ?? false;
    }
}
