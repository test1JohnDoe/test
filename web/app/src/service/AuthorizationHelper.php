<?php

namespace App\Acme\service;

use App\Acme\controller\UserController;

trait AuthorizationHelper
{
    public function isAuthorized(): void
    {
        if (!$_SESSION['isLogged']) {
            header('Location: ?route=' . UserController::LOGIN_ROUTE);
            exit();
        }
    }
}