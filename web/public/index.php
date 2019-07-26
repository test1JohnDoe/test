<?php
session_start();
include '../app/vendor/autoload.php';

use App\Acme\controller\BaseController;
use App\Acme\controller\UserController;
use App\Acme\controller\PostController;

try {
    $builder = new DI\ContainerBuilder();
    $container = $builder->build();

    $requestMethod = $_SERVER['REQUEST_METHOD'] ?? null;
    switch ($requestMethod) {
        case 'GET':
            $requestRoute = $_GET['route'] ?? null;
            switch ($requestRoute) {
                case UserController::LOGIN_ROUTE:
                    $userController = $container->get(UserController::class);
                    echo $userController->loginAction();
                    break;
                case UserController::LOGOUT_ROUTE:
                    $userController = $container->get(UserController::class);
                    $userController->logoutAction();
                    break;
                case UserController::SIGNUP_ROUTE:
                    $userController = $container->get(UserController::class);
                    echo $userController->signupAction();
                    break;
                case PostController::EDIT_POST_ROUTE:
                    $postId = $_GET['post_id'] ?? null;
                    if (empty($postId)) {
                        header('Location: ?error=1');
                        exit();
                    }
                    $postController = $container->get(PostController::class);
                    echo $postController->editPostAction($postId);
                    break;
                default:
                    $baseController = $container->get(BaseController::class);
                    echo $baseController->indexAction();
                    break;
            }
            break;
        case 'POST':
            $requestRoute = $_REQUEST['route'] ?? null;
            switch ($requestRoute) {
                case PostController::ADD_POST_POST_ROUTE:
                    $title = $_POST['title'] ?? null;
                    $text = $_POST['text'] ?? null;
                    $refId = $_POST['refId'] ?? null;
                    if (empty($title) || empty($text)) {
                        header('Location: ?error=1');
                        exit();
                    }
                    $postController = $container->get(PostController::class);
                    $postController->addPostAction(
                        $title,
                        $text,
                        $refId
                    );
                    break;
                case PostController::DELETE_POST_POST_ROUTE:
                    $postId = $_POST['postId'] ?? null;
                    if (empty($postId)) {
                        header('Location: ?error=1');
                        exit();
                    }
                    $postController = $container->get(PostController::class);
                    $postController->deletePostAction($postId);
                    break;
                case PostController::EDIT_POST_ROUTE:
                    $postId = $_POST['postId'] ?? null;
                    $title = $_POST['title'] ?? null;
                    $text = $_POST['text'] ?? null;
                    if (empty($postId) || empty($title) || empty($text)) {
                        header('Location: ?error=1');
                        exit();
                    }
                    $postController = $container->get(PostController::class);
                    $postController->editPostPostAction($postId, $title, $text);
                    break;
                case UserController::LOGIN_ROUTE:
                    $userName = $_POST['userName'] ?? null;
                    $userPassword = $_POST['userPassword'] ?? null;
                    if (empty($userName) || empty($userPassword)) {
                        header('Location: ?route=' . UserController::LOGIN_ROUTE . '&error=1');
                        exit();
                    }
                    $userController = $container->get(UserController::class);
                    $userController->loginPostAction($userName, $userPassword);
                    break;
                case UserController::SIGNUP_ROUTE:
                    $userName = $_POST['userName'] ?? null;
                    $userPassword = $_POST['userPassword'] ?? null;
                    $userPassword2 = $_POST['userPassword2'] ?? null;
                    if (empty($userName) || empty($userPassword) || empty($userPassword2)) {
                        header('Location: ?route=' . UserController::SIGNUP_ROUTE . '&error=1');
                        exit();
                    }
                    $userController = $container->get(UserController::class);
                    $userController->signupPostAction($userName, $userPassword, $userPassword2);
                    break;
                default:
                    throw new LogicException('Not supported POST request route');
                    break;
            }
            break;
        default:
            throw new LogicException('Not supported request method');
            break;
    }
} catch (\Exception $exception) {
    //TODO: Add logger here
    echo 'Error. Please try again later.';
}
