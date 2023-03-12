<?php
namespace Ivan\Php\Blog;

class Comment {
    
    private UUID $uuid;
    private User $user;
    private Post $post;
    private string $text;

    public function __construct(UUID $uuid, User $user, Post $post, string $text)
    {
        $this->uuid = $uuid;
        $this->user = $user;
        $this->post = $post;
        $this->text = $text;
    }

    public function __toString(): string
    {
        return "Юзер $this->author_uuid написал коментарий к посту номер $this->post_uuid с номером $this->uuid и текстом: $this->text" . PHP_EOL;
    }

    public function uuid(): UUID
    {
        return $this->uuid;
    }

    public function post(): Post
    {
        return $this->post;
    }

    public function user(): User
    {
        return $this->user;
    }
    
    public function text(): string
    {
        return $this->text;
    }

}