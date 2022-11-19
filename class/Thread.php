<?php
require "DataBase.php";

class Thread {

    private int $_threadId;

    private array $_threadPosts;

    public function __construct(int $threadId, DataBase $data) {

        $this->data = $data;
        $this->_threadId = $threadId;
        $this->_threadPosts = $data->getPostsByThreadId($threadId);
    }

    public function renderThread(string $templateLoc): void {
        $postType = 'oppost';
        foreach ($this->_threadPosts as $post) {

            $replies = $this->data->getReplies($post);
            $post->renderPost($templateLoc, $replies, $postType);
            $postType = 'post';
        }
    }

    public function renderShowcase(string $templateLoc): void {
        $nOfPosts = count($this->_threadPosts);
        if ($nOfPosts >= 1) {
            $replies = $this->data->getReplies($this->_threadPosts[0]);
            $this->_threadPosts[0]->renderPost($templateLoc, $replies, $postType="oppost");

        }
        if ($nOfPosts >= 4) {
            $nOfMore = $nOfPosts - 3;
            echo "$nOfMore more post(s)";
        }
        if ($nOfPosts >= 3) {
            $replies = $this->data->getReplies($this->_threadPosts[$nOfPosts-2]);
            $this->_threadPosts[$nOfPosts-2]->renderPost($templateLoc, $replies);
        }

        if ($nOfPosts >= 2) {
            $replies = $this->data->getReplies($this->_threadPosts[$nOfPosts-1]);
            $this->_threadPosts[$nOfPosts-1]->renderPost($templateLoc, $replies);
        }

        printf("
        <a href='thread/?id=%s'>Proceed to thread</a>
        ", 
        $this->_threadId);
        echo "<hr>";
    }
}