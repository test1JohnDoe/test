<?php

namespace App\Acme\repository;

use App\Acme\model\Post;
use App\Acme\service\DatabaseHelper;
use App\Acme\service\DatabaseService;
use App\Acme\service\UserAuthenticationService;

class PostDatabaseRepository
{
    use DatabaseHelper;

    private const POSTS_TABLE_NAME = 'posts';

    private const USER_POSTS_TABLE_NAME = 'user_posts';

    /**
     * @var DatabaseService
     */
    private $databaseService;

    /**
     * @var UserDatabaseRepository
     */
    private $userDatabaseRepository;

    /**
     * @var UserAuthenticationService
     */
    private $userAuthenticationService;

    /**
     * PostDatabaseRepository constructor.
     * @param DatabaseService $databaseService
     * @param UserDatabaseRepository $userDatabaseRepository
     * @param UserAuthenticationService $userAuthenticationService
     */
    public function __construct(
        DatabaseService $databaseService,
        UserDatabaseRepository $userDatabaseRepository,
        UserAuthenticationService $userAuthenticationService
    ) {
        $this->databaseService = $databaseService;
        $this->userDatabaseRepository = $userDatabaseRepository;
        $this->userAuthenticationService = $userAuthenticationService;
    }

    /**
     * @param int $userId
     * @param int $postId
     * @return bool
     */
    public function isPostAssignToUser(int $userId, int $postId): bool
    {
        $pdo = $this->databaseService::getPdo();

        $query = 'SELECT * FROM ' . self::USER_POSTS_TABLE_NAME . ' WHERE user_id = :userId AND post_id = :postId';
        $sth = $pdo->prepare($query);
        $sth->execute([
            'userId' => $userId,
            'postId' => $postId,
        ]);
        $userPostEntity = $sth->fetch(\PDO::FETCH_ASSOC);
        if (empty($userPostEntity['user_id'])) {
            return false;
        }

        return true;
    }

    /**
     * Return array of all Posts
     * @return array
     */
    public function getAllPosts(): array
    {
        $pdo = $this->databaseService::getPdo();

        $query = 'SELECT * FROM ' . self::USER_POSTS_TABLE_NAME;
        $sth = $pdo->prepare($query);
        $sth->execute();

        $userPostEntities = $sth->fetchAll(\PDO::FETCH_ASSOC);
        return $this->buildPosts($userPostEntities);
    }

    /**
     * @param int $postId
     * @return Post|null
     */
    public function getPostById(int $postId):? Post
    {
        $pdo = $this->databaseService::getPdo();

        $query = 'SELECT * FROM ' . self::POSTS_TABLE_NAME . ' WHERE id = ?';
        $sth = $pdo->prepare($query);
        $sth->execute([$postId]);

        $postEntity = $sth->fetch(\PDO::FETCH_ASSOC);
        if (empty($postEntity['id'])) {
            return null;
        }

        $user = $this->userDatabaseRepository->getUserByUserId($this->userAuthenticationService->getCurrentUserId());
        if ($user === null) {
            //TODO: add logging here
            return null;
        }

        return new Post(
            $postEntity['id'],
            $postEntity['title'],
            $postEntity['text'],
            $postEntity['create_date'],
            $user,
            $postEntity['ref_id']
        );
    }

    /**
     * @param array $userPostEntities
     * @return array
     */
    private function buildPosts(array $userPostEntities): array
    {
        $userIdsByPostId = [];
        $postIds = [];
        $userIds = [];
        foreach ($userPostEntities as $userPostEntity) {
            $userIdsByPostId[$userPostEntity['post_id']] = $userPostEntity['user_id'];
            $postIds[] = $userPostEntity['post_id'];
            $userIds[] = $userPostEntity['user_id'];
        }

        $users = $this->userDatabaseRepository->getUsersByUserIds(array_values(array_unique($userIds, SORT_NUMERIC)));

        $pdo = $this->databaseService::getPdo();
        $query = 'SELECT * FROM ' . self::POSTS_TABLE_NAME . ' WHERE id IN(' . $this->buildQuestionMarks($postIds) . ') ORDER BY create_date DESC';
        $sth = $pdo->prepare($query);
        $sth->execute(array_values(array_unique($postIds, SORT_NUMERIC)));
        $postEntities = $sth->fetchAll(\PDO::FETCH_ASSOC);

        /** @var Post[] $posts */
        $posts = [];
        $comments = [];
        foreach ($postEntities as $postEntity) {
            if ($postEntity['ref_id'] !== null) {
                $comments[$postEntity['ref_id']][] = new Post(
                    $postEntity['id'],
                    $postEntity['title'],
                    $postEntity['text'],
                    $postEntity['create_date'],
                    $users[$userIdsByPostId[$postEntity['id']]],
                    $postEntity['ref_id']
                );
            } else {
                $posts[$postEntity['id']] = new Post(
                    $postEntity['id'],
                    $postEntity['title'],
                    $postEntity['text'],
                    $postEntity['create_date'],
                    $users[$userIdsByPostId[$postEntity['id']]],
                    $postEntity['ref_id']
                );
            }
        }
        foreach ($comments as $postId => $comments) {
            if (isset($posts[$postId])) {
                $posts[$postId]->addComments($comments);
            }
        }

        return $posts;
    }

    /**
     * @param int $userId
     * @param string $title
     * @param string $text
     * @param int|null $refId
     */
    public function savePost(int $userId, string $title, string $text, int $refId = null): void
    {
        $pdo = $this->databaseService::getPdo();

        $postQuery = 'INSERT INTO ' . self::POSTS_TABLE_NAME . '(title, text, ref_id) VALUES (?,?,?)';
        $sth = $pdo->prepare($postQuery);
        $sth->execute([$title, $text, $refId]);
        $postId = $pdo->lastInsertId();

        $userPostQuery = 'INSERT INTO ' . self::USER_POSTS_TABLE_NAME . '(post_id, user_id) VALUES (?,?)';
        $sth = $pdo->prepare($userPostQuery);
        $sth->execute([$postId, $userId]);
    }

    /**
     * @param int $postId
     * @param string $title
     * @param string $text
     */
    public function updatePost(int $postId, string $title, string $text): void
    {
        $pdo = $this->databaseService::getPdo();

        $query = 'UPDATE ' . self::POSTS_TABLE_NAME . ' SET title = :title, text = :text WHERE id = :postId';
        $sth = $pdo->prepare($query);
        $sth->execute([
            'title' => $title,
            'text' => $text,
            'postId' => $postId,
        ]);
    }

    /**
     * @param int $postId
     */
    public function deletePost(int $postId): void
    {
        $pdo = $this->databaseService::getPdo();

        $query = 'DELETE FROM ' . self::POSTS_TABLE_NAME . ' WHERE id = ?';
        $sth = $pdo->prepare($query);
        $sth->execute([$postId]);

        $query = 'SELECT id FROM ' . self::POSTS_TABLE_NAME . ' WHERE ref_id = ?';
        $sth = $pdo->prepare($query);
        $sth->execute([$postId]);
        $postCommentIds = $sth->fetchAll(\PDO::FETCH_COLUMN);

        if (!empty($postCommentIds)) {
            $query = 'DELETE FROM ' . self::USER_POSTS_TABLE_NAME . ' WHERE post_id IN(' . $this->buildQuestionMarks($postCommentIds) . ')';
            $sth = $pdo->prepare($query);
            $sth->execute(array_values(array_unique($postCommentIds, SORT_NUMERIC)));
        }

        $query = 'DELETE FROM ' . self::POSTS_TABLE_NAME . ' WHERE ref_id = ?';
        $sth = $pdo->prepare($query);
        $sth->execute([$postId]);

        $query = 'DELETE FROM ' . self::USER_POSTS_TABLE_NAME . ' WHERE post_id = ?';
        $sth = $pdo->prepare($query);
        $sth->execute([$postId]);
    }
}
