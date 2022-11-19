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

    public function renderPost($templateLoc, $replies, $postType = "post") {
        $template = file_get_contents($templateLoc);

        $replyStr = '';
        if (sizeof($replies) != 0) {
            $replyAnchors = array();
            foreach ($replies as $reply) {
                array_push($replyAnchors, "<a href='#$reply'>$reply</a>");
            }
            $replyStr = "Replies: " . implode(', ', $replyAnchors);
        }

        printf($template, $postType, 
               $this->dateTime, $this->id, $this->id, $this->username, 
               nl2br(htmlspecialchars_decode($this->text)), $replyStr);
    }
}
