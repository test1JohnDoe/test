<?php

namespace App\Acme\model;

class Post
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $text;

    /**
     * @var string
     */
    private $createDate;

    /**
     * @var int|null
     */
    private $refId;

    /**
     * @var User
     */
    private $user;

    /**
     * @var Post[]
     */
    private $comments = [];

    /**
     * Post constructor.
     * @param int $id
     * @param string $title
     * @param string $text
     * @param string $createDate
     * @param User $user
     * @param int|null $refId
     */
    public function __construct(
        int $id,
        string $title,
        string $text,
        string $createDate,
        User $user,
        int $refId = null
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->text = $text;
        $this->createDate = $createDate;
        $this->refId = $refId;
        $this->user = $user;
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
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @return string
     */
    public function getCreateDate(): string
    {
        return $this->createDate;
    }

    /**
     * @return int|null
     */
    public function getRefId(): ?int
    {
        return $this->refId;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @return Post[]
     */
    public function getComments(): array
    {
        return $this->comments;
    }

    /**
     * @param Post[] $comments
     */
    public function addComments(array $comments): void
    {
        foreach ($comments as $comment) {
            $this->comments[] = $comment;
        }
    }
}
