<?php

include("DataBase.php");
include("DataStorage.php");

$oldDataStorage = new DataStorage('/var/www/frenchan/messages');
$dataBase = new DataBase();
$newDataStorage = new DataStorage('../new_messages');

foreach ($oldDataStorage->getThreadListOrdByLastPost() as $thread) {

    foreach ($oldDataStorage->getPostsByThreadId($thread) as $post) {
        $myDateTime = DateTime::createFromFormat('d.m.y h:i:s', $post->dateTime);
        $post->dateTime = $myDateTime->format('Y-m-d H:i:s');
        $post->text = htmlspecialchars($post->text);
        $post->username = htmlspecialchars($post->username);
        $dataBase->appendNewPost($post);
    }
}
/* foreach ($dataBase->getThreadListOrdByLastPost() as $thread) {
    echo $thread . "\n";
    echo "Posts:\n";
    foreach ($dataBase->getPostsByThreadId($thread) as $post) {
        $newDataStorage->appendNewPost($post);
    }
}
*/

