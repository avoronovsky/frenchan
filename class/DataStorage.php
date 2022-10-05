<?php
include("Post.php");

class DataStorage {

    private string $_source;

    public array $_jsonList;

    public function __construct($source) {
        $this->_source = $source;
        $this->_jsonList = array();
        $jsonList = glob("$source/*.json");
        foreach ($jsonList as $jsonLoc) {
            $key = $this->_getThreadIdFromFileLoc($jsonLoc);
            $date = max(array_column(
                json_decode(file_get_contents($jsonLoc)), 'dateTime'));
            $this->_jsonList += [ $key => ["file" => $jsonLoc, "date" => $date]];
        }
    }

    public function getNextThreadId(): int {
        $maxThread = sizeof($this->jsonList) > 0 ? max(array_keys($this->jsonList)) : 0;
        return $maxThread + 1;
    }

    public function getNextPostId(int $threadId): int {
        $data = array_key_exists($threadId, $this->_jsonList) ? 
            $this->_getJsonValues($threadId) : 0;
        return max(array_column($data, 'id')) + 1;
    }

    public function appendPost(Post $post): void {
        $posts = array();
        $threadExists = false;
        $threadId = $post->threadId;
        if (array_key_exists($threadId, $this->_jsonList)) {
            $posts = $this->_getJsonValues($threadId);
            $threadExists = true;
        }
        $postArray = $this->_fromPostToArray($post);
        array_push($posts, $postArray);
        file_put_contents($this->_getFileLocFromThreadId($threadId), json_encode($posts));
    }

    public function getPostsByThreadId(int $threadId): array {
        $data = $this->_getJsonValues($threadId);
        $threadPosts = array();
        foreach ($data as $postArray) {
            $post = $this->_fromArrayToPost($postArray);
            array_push($threadPosts, $post);
        }
        return $threadPosts;
    }

    public function getThreadListOrdByLastPost() {
        foreach (array_keys($this->_jsonList) as $key) {
        }
        uasort($this->_jsonList, function($a, $b) {
            $ad = DateTime::createFromFormat('d.m.Y H:i:s',$a['date']);
            $bd = DateTime::createFromFormat('d.m.Y H:i:s',$b['date']);
          
            if ($ad == $bd) {
              return 0;
            }
          
            return $ad < $bd ? 1 : -1;
        });
        foreach (array_keys($this->_jsonList) as $key) {
        }
        return array_keys($this->_jsonList);
    }

    private function _getJsonValues(int $threadId): array {
        $jsonContent = file_get_contents($this->_jsonList[$threadId]['file']);
        $data = json_decode($jsonContent, true);
        return $data;
    }

    private function _getThreadIdFromFileLoc(string $jsonLoc): string {
        return substr($jsonLoc, $this->_backwardStrpos($jsonLoc, "/")+1, -5);
    }

    private function _getFileLocFromThreadId(int $threadId): string {
        return $this->_source . '/' . $threadId . '.json';
    }

    private function _backwardStrpos($haystack, $needle, $offset = 0){
        $length = strlen($haystack);
        $offset = ($offset > 0)?($length - $offset):abs($offset);
        $pos = strpos(strrev($haystack), strrev($needle), $offset);
        return ($pos === false)?false:( $length - $pos - strlen($needle) );
    }

    private function _fromArrayToPost(array $postArray): Post {
        $post = new Post(
            $postArray['id'],
            $postArray['text'],
            $postArray['dateTime'],
            $postArray['threadId'],
            $postArray['username'],
        );
        return $post;
    }

    private function _fromPostToArray(Post $post): array {
        $postArray = array(
            "id" => $post->id,
            "text" => $post->text,
            "dateTime" => $post->dateTime,
            "username" => $post->username,
            "threadId" => $post->threadId,
        );
        return $postArray;
    }
}