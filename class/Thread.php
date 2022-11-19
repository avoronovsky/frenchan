<?php
require "DataBase.php";

class Thread {

    private int $_threadId;

    private array $_threadPosts;

    public function __construct(int $threadId, DataBase $data) {
        $this->_threadId = $threadId;
        $this->_threadPosts = $data->getPostsByThreadId($threadId);
    }

    public function renderThread(string $templateLoc): void {
        $postType = 'oppost';
        foreach ($this->_threadPosts as $post) {
            $post->renderPost($templateLoc, $postType);
            $postType = 'post';
        }
    }

    public function renderShowcase(string $templateLoc): void {
        $nOfPosts = count($this->_threadPosts);
        if ($nOfPosts >= 1) {
            $this->_threadPosts[0]->renderPost($templateLoc, "oppost");
        }
        if ($nOfPosts >= 4) {
            $nOfMore = $nOfPosts - 3;
            echo "$nOfMore more post(s)";
        }
        if ($nOfPosts >= 3) {
            $this->_threadPosts[$nOfPosts-2]->renderPost($templateLoc);
        }
        if ($nOfPosts >= 2) {
            $this->_threadPosts[$nOfPosts-1]->renderPost($templateLoc);
        }
        printf("
        <a href='thread/?id=%s'>Proceed to thread</a>
        ", 
        $this->_threadId);
        echo "<hr>";
    }
}