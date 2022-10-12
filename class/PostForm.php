<?php

//include("DataBase.php");

class PostForm {

    private ?int $_threadId;

    public function __construct(?int $threadId) {
        $this->_threadId = $threadId;
    }

    public function renderForm($POSTFORMTEMPLATELOC) {
        $buttonText = $this->_threadId ? "Assets/postpixel.png" : "Assets/create_thread.png";
        printf(file_get_contents($POSTFORMTEMPLATELOC), $buttonText);
    }


    public function handlePost (DataBase $dataStorage, string $url) {

        $username = htmlspecialchars(print_r($_POST['username'], true));
        $text = htmlspecialchars(print_r($_POST['message'], true));
        $threadId = is_int($this->_threadId) ? $this->_threadId : $dataStorage->getNextThreadId();

        if ($this->_isPostSane($text, $username)) {

            $post = new Post(
                $dataStorage->getNextPostId(),
                $text,
                date('Y.m.d H:i:s'),
                $threadId,
                $username ? $username : 'Anonymous',
            );

            $dataStorage->appendNewPost($post);
        }

        header('Location: ' . $url, true, 303);
        die();
    }



    private function _isPostSane($text, $username): bool {
        if (strlen($text) > 0 and strlen($text) < 2000 and strlen($username) < 32) {
            return true;
        }
        return false;
    }
}