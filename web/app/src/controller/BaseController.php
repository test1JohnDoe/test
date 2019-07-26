<?php

namespace App\Acme\controller;

use App\Acme\repository\PostDatabaseRepository;
use App\Acme\service\RenderService;

class BaseController
{
    /**
     * @var PostDatabaseRepository
     */
    private $postDatabaseRepository;

    /**
     * @var RenderService
     */
    protected $renderService;

    /**
     * BaseController constructor.
     * @param PostDatabaseRepository $postDatabaseRepository
     * @param RenderService $renderService
     */
    public function __construct(
        PostDatabaseRepository $postDatabaseRepository,
        RenderService $renderService
    ) {
        $this->postDatabaseRepository = $postDatabaseRepository;
        $this->renderService = $renderService;
    }

    /**
     * @return string
     */
    public function indexAction(): string
    {
        $posts = $this->postDatabaseRepository->getAllPosts();

        return $this->renderService->render($_SERVER['DOCUMENT_ROOT'] . '/view/index.php', $posts);
    }
}
