<?php
require "DataStorage.php";

class Thread {

    private int $_threadId;

    private array $_threadPosts;

    public function __construct(int $threadId, DataStorage $data) {
        $this->_threadId = $threadId;
        $this->_threadPosts = $data->getPostsByThreadId($threadId);
    }

    public function renderThread(string $templateLoc): void {
        foreach ($this->_threadPosts as $post) {
            $post->renderPost($templateLoc);
        }
    }

    public function renderShowcase(string $templateLoc): void {
        $nOfPosts = count($this->_threadPosts);
        if ($nOfPosts >= 1) {
            $this->_threadPosts[0]->renderPost($templateLoc);
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
        printf("<table><tr><th>
        <a href='index.php?thread=%s'>Proceed to thread</a>
        </th></tr></table>", 
        $this->_threadId);
        echo "<hr>";
    }
}