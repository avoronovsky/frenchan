<?php
class Post {

    public int $id;

    public string $text;

    public string $dateTime;

    public int $threadId;

    public string $username;

    public function __construct(int $id, string $text, string $dateTime, 
                                int $threadId, string $username) {
        $this->id = $id;
        $this->text = $text;
        $this->dateTime = $dateTime;
        $this->threadId = $threadId;
        $this->username = $username;
    }

    public function renderPost($templateLoc, $postType = "post") {
        $template = file_get_contents($templateLoc);
        printf($template, $postType, $this->dateTime, $this->id, $this->username, $this->text);
    }
}
