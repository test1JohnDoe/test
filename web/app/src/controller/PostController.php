<?php

namespace App\Acme\controller;

use App\Acme\repository\PostDatabaseRepository;
use App\Acme\service\AuthorizationHelper;
use App\Acme\service\RenderService;
use App\Acme\service\UserAuthenticationService;

class PostController
{
    use AuthorizationHelper;

    public const ADD_POST_POST_ROUTE = 'add_post';

    public const DELETE_POST_POST_ROUTE = 'delete_post';

    public const EDIT_POST_ROUTE = 'edit_post';

    /**
     * @var PostDatabaseRepository
     */
    private $postDatabaseRepository;

    /**
     * @var UserAuthenticationService
     */
    private $userAuthenticationService;

    /**
     * @var RenderService
     */
    private $renderService;

    /**
     * PostController constructor.
     * @param PostDatabaseRepository $postDatabaseRepository
     * @param UserAuthenticationService $userAuthenticationService
     * @param RenderService $renderService
     */
    public function __construct(
        PostDatabaseRepository $postDatabaseRepository,
        UserAuthenticationService $userAuthenticationService,
        RenderService $renderService
    ) {
        $this->postDatabaseRepository = $postDatabaseRepository;
        $this->userAuthenticationService = $userAuthenticationService;
        $this->renderService = $renderService;
    }

    /**
     * @param string $title
     * @param string $text
     * @param int|null $refId
     */
    public function addPostAction(string $title, string $text, int $refId = null): void
    {
        $this->isAuthorized();
        $this->postDatabaseRepository->savePost(
            $this->userAuthenticationService->getCurrentUserId(),
            $title,
            $text,
            $refId
        );

        header('Location: /');
        exit();
    }

    /**
     * @param int $postId
     */
    public function deletePostAction(int $postId): void
    {
        $this->isAuthorized();

        $isPostAssignToUser = $this->postDatabaseRepository->isPostAssignToUser(
            $this->userAuthenticationService->getCurrentUserId(),
            $postId
        );

        if (!$isPostAssignToUser) {
            header('Location: /?error=11');
            exit();
        }

        $this->postDatabaseRepository->deletePost($postId);

        header('Location: /');
        exit();
    }

    /**
     * @param int $postId
     * @return string
     */
    public function editPostAction(int $postId): string {
        $this->isAuthorized();

        $isPostAssignToUser = $this->postDatabaseRepository->isPostAssignToUser(
            $this->userAuthenticationService->getCurrentUserId(),
            $postId
        );

        if (!$isPostAssignToUser) {
            header('Location: /?error=11');
            exit();
        }

        $post = $this->postDatabaseRepository->getPostById($postId);

        return $this->renderService->render($_SERVER['DOCUMENT_ROOT'] . '/view/editPost.php', $post);
    }

    /**
     * @param int $postId
     * @param string $title
     * @param string $text
     */
    public function editPostPostAction(int $postId, string $title, string $text): void
    {
        $this->isAuthorized();

        $isPostAssignToUser = $this->postDatabaseRepository->isPostAssignToUser(
            $this->userAuthenticationService->getCurrentUserId(),
            $postId
        );

        if (!$isPostAssignToUser) {
            header('Location: /?error=1');
            exit();
        }

        $this->postDatabaseRepository->updatePost($postId, $title, $text);

        header('Location: /');
        exit();
    }
}
