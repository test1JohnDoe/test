<?php

namespace App\Acme\service;

class RenderService
{
    /**
     * @var UserAuthenticationService
     */
    private $userAuthenticationService;

    /**
     * RenderService constructor.
     * @param UserAuthenticationService $userAuthenticationService
     */
    public function __construct(UserAuthenticationService $userAuthenticationService)
    {
        $this->userAuthenticationService = $userAuthenticationService;
    }

    /**
     * @param $file
     * @param $data
     * @return false|string
     */
    public function render($file, $data)
    {
        if(is_file($file))
        {
            ob_start();
            $userData = $this->userAuthenticationService->getUserData();
            $viewData = $data;
            include($file);
            return ob_get_clean();
        }
        header('Location: /');
        exit();
    }
}
