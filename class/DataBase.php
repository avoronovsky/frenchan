<?php

include("Post.php");

class DataBase {

    private MySQLi $_conn;

    public function __construct(array $DATABASECREDS){

        extract($DATABASECREDS, EXTR_PREFIX_SAME, "wddx");

        $this->_conn = new MySQLi($servername, $username, $password, $database);
    }

    public function getNextThreadId(): int {
        $sql = "select max(thread_id) FROM Posts";
        $result = $this->_conn->query($sql);
        while ($row = mysqli_fetch_assoc($result)) {
            $lastId = $row["max(thread_id)"];
        }
        return $lastId + 1;
    }

    public function getNextPostId(): int {
        $sql = "select max(id) FROM Posts";
        $result = $this->_conn->query($sql);
        while ($row = mysqli_fetch_assoc($result)) {
            $lastId = $row["max(id)"];
        }
        return $lastId + 1;
    }

    public function appendNewPost(Post $post): void {
        $sql = "insert into Posts (id, text, username, datetime, thread_id)
        values ('$post->id', '$post->text', '$post->username', '$post->dateTime', '$post->threadId')";
        $this->_conn->query($sql);
    }

    public function getPostsByThreadId(int $threadId): array {
        $sql = "select * from Posts where thread_id=$threadId order by datetime asc";
        $postData = $this->_conn->query($sql);

        $posts = array();
        while($row = $postData->fetch_assoc()) {
            $post = new Post(
                $row['id'],
                $row['text'],
                $row['datetime'],
                $row['thread_id'],
                $row['username']
            );
            array_push($posts, $post);
        }
        return $posts;
    }

    public function getThreadListOrdByLastPost(): array {
        $sql = "select thread_id from Posts group by thread_id order by max(datetime) desc";
        $threadsData = $this->_conn->query($sql);
        $threads = array();
        while($row = $threadsData->fetch_assoc()) {
            array_push($threads, $row['thread_id']);
        }
        return $threads;
    }
}



