<?php
namespace Ivan\Php\Blog;

class Post {
    
    private UUID    $uuid;
    private User    $user;
    private string  $title;
    private string  $text;

    public function __construct(UUID $uuid, User $user, string $title, string $text)
    {
        $this->uuid = $uuid;
        $this->user = $user;
        $this->title = $title;
        $this->text = $text;
    }

    public function __toString(): string
    {
        return "Юзер $this->user написал пост номер $this->uuid с заголовком: $this->title и текстом: $this->text" . PHP_EOL;
    }

    public function uuid(): UUID
    {
        return $this->uuid;
    }

    public function user(): User
    {
        return $this->user;
    }

    public function title(): string
    {
        return $this->title;
    }
    
    public function text(): string
    {
        return $this->text;
    }

}