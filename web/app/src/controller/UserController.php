<?php

namespace App\Acme\controller;

use App\Acme\service\AuthorizationHelper;
use App\Acme\service\RenderService;
use App\Acme\service\UserAuthenticationService;

class UserController
{
    use AuthorizationHelper;

    public const LOGIN_ROUTE = 'login';

    public const LOGOUT_ROUTE = 'logout';

    public const SIGNUP_ROUTE = 'signup';

    /**
     * @var UserAuthenticationService
     */
    private $userAuthenticationService;

    /**
     * @var RenderService
     */
    private $renderService;

    /**
     * UserController constructor.
     * @param UserAuthenticationService $userAuthenticationService
     * @param RenderService $renderService
     */
    public function __construct(
        UserAuthenticationService $userAuthenticationService,
        RenderService $renderService
    ) {
        $this->userAuthenticationService = $userAuthenticationService;
        $this->renderService = $renderService;
    }

    /**
     * @return string
     */
    public function loginAction(): string
    {
        if ($this->userAuthenticationService->isLogged()) {
            header('Location: /');
            exit();
        }
        return $this->renderService->render($_SERVER['DOCUMENT_ROOT'] . '/view/login.php', []);
    }

    /**
     * @param string $userName
     * @param string $userPassword
     */
    public function loginPostAction(string $userName, string $userPassword): void
    {
        $isAuth = $this->userAuthenticationService->auth($userName, $userPassword);
        if (!$isAuth) {
            header('Location: ?route=' . self::LOGIN_ROUTE);
            exit();
        }

        header('Location: /');
        exit();
    }

    /**
     * @param string $userName
     * @param string $userPassword
     * @param string $userPassword2
     */
    public function signupPostAction(string $userName, string $userPassword, string $userPassword2): void
    {
        $isRegistered = $this->userAuthenticationService->signup($userName, $userPassword, $userPassword2);
        if (!$isRegistered) {
            header('Location: ?route=' . self::SIGNUP_ROUTE . '&error=1');
            exit();
        }

        header('Location: /?route=' . self::LOGIN_ROUTE);
        exit();
    }

    /**
     * @return string
     */
    public function logoutAction(): void
    {
        $this->userAuthenticationService->logout();
        header('Location: /');
        exit();
    }

    /**
     * @return string
     */
    public function signupAction(): string
    {
        if ($this->userAuthenticationService->isLogged()) {
            header('Location: /');
            exit();
        }
        return $this->renderService->render($_SERVER['DOCUMENT_ROOT'] . '/view/signup.php', []);
    }
}
