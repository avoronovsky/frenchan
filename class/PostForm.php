<?php

//include("DataBase.php");

class PostForm {

    private ?int $_threadId;

    public function __construct(?int $threadId) {
        $this->_threadId = $threadId;
    }

    public function renderForm($POSTFORMTEMPLATELOC) {
        printf(file_get_contents($POSTFORMTEMPLATELOC), $buttonText);
    }


    public function handlePost (DataBase $dataStorage, string $url) {

        $username = htmlspecialchars(print_r($_POST['username'], true));

        $text = print_r($_POST['message'], true);
        $threadId = is_int($this->_threadId) ? $this->_threadId : $dataStorage->getNextThreadId();

        if ($this->_isPostSane($text, $username)) {

            $newPost = new Post(
                $dataStorage->getNextPostId(),
                $text,
                date('Y.m.d H:i:s'),
                $threadId,
                $username ? $username : 'Anonymous',
            );

            if ($this->_threadId) {
                $this->_checkForReplies($dataStorage, $newPost);
            }
            $newPost->text = htmlspecialchars($newPost->text);
            $dataStorage->appendNewPost($newPost);
        }

        header('Location: ' . $url, true, 303);
        die();
    }



    private function _isPostSane($text, $username): bool {
        if (strlen($text) > 0 and strlen($text) < 2000 and strlen($username) < 32) {
            return true and ctype_space($text);
        }
        return false;
    }

    private function _checkForReplies(DataBase $dataStorage, Post $newPost): void {
        $regex = "/>>[0-9]+\b/";
        preg_match_all($regex, $newPost->text, $matches);

        foreach ($matches[0] as $needle) {
            $newPost->text = str_replace(
                $needle,
                "<a href=$ROOT/thread/?id=$this->_threadId#".substr($needle, 2).">$needle</a>",
                $newPost->text,
            );
        }

        $postIds = preg_filter("/>>/", "", $matches[0]);
        foreach ($postIds as $postId) {
            foreach ($dataStorage->getPostsByThreadId($this->_threadId) as $originalPost) {
                if ($originalPost->id == intval($postId)) {
                    $dataStorage->appendReply($originalPost, $newPost);
}
            }
        }
    }

}

