<?php
include("Post.php");

class DataRecord {

    public string $source;

    public $data;

    public function __construct(string $source) {
        $this->source = $source;
        $this->data = json_decode(file_get_contents($source), true);
    }

    public function getNextPostId() {
        return max(array_column($this->data, 'id')) + 1;
    }

    public function getNextThreadId() {
        return max(array_column($this->data, 'threadId')) + 1;
    }

    public function fromPostToArray(Post $post) {
        $postArray = array(
            "id" => $post->id,
            "text" => $post->text,
            "dateTime" => $post->dateTime,
            "username" => $post->username,
            "threadId" => $post->threadId,
        );
        return $postArray;
    }

    public function fromArrayToPost(array $postArray) {
        $post = new Post(
            $postArray['id'],
            $postArray['text'],
            $postArray['dateTime'],
            $postArray['threadId'],
            $postArray['username'],
        );
        return $post;
    }

    public function appendNewPost(string $text, string $username, string $threadId) {
        $postArray = array(
            "id" => $this->getNextPostId(),
            "text" => $text,
            "dateTime" => date('d.m.y h:i:s'),
            "username" => $username,
            "threadId" => $threadId,
        );
        array_push($this->data, $postArray);
        file_put_contents($this->source, json_encode($this->data));
    }

    public function getPostById($id) {
        foreach ($this->data as $postData) {
            if ($postData['id'] == $id) {
                return $this->fromArrayToPost($postData);
            }
        }
    }

    public function getPostsByThreadId($threadId) {
        $postsOfThread = array();
        foreach ($this->data as $postData) {
            if ($postData['threadId'] == $threadId) {
                array_push($postsOfThread, $this->fromArrayToPost($postData));
            }
        }
        return $postsOfThread;
    }

    public function getThreadList() {
        $threads = array();
        foreach (array_reverse($this->data) as $postData) {
            if (!in_array($postData['threadId'], $threads)) {
                array_push($threads, ($postData['threadId']));
            }
        }
        return $threads;
    }

    public function createThreadShowcase($threadId) {
        $showCase = array('threadPosts' => array());
        $posts = $this->getPostsByThreadId($threadId);
        if (count($posts) >= 1) {
            array_push($showCase['threadPosts'], $posts[0]);
        }
        if (count($posts) >= 2) {
            array_push($showCase['threadPosts'], $posts[count($posts)-1]);
        }
        if (count($posts) >= 3) {
            array_push($showCase['threadPosts'], $posts[count($posts)-2]);
        }
        if (count($posts) >= 4) {
            $showCase["morePosts"] = (count($posts) - 3);
        }
        return $showCase;
    }
}
