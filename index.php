<?php
include("class/Thread.php");

//$JSONLOC = "messages";
$POSTTEMPLATELOC = "templates/postTemplate.html";
$HEADTEMPLATELOC = "head.html";

$url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
$url_components = parse_url($url);

parse_str($url_components["query"], $params);

//$dataStorage = new DataStorage($JSONLOC);
$dataStorage = new DataBase();

$pageTitle = "/ - Frenchan Board";
printf(file_get_contents($HEADTEMPLATELOC), $pageTitle);

$buttonText = "/Assets/create_thread.png";
printf(file_get_contents("templates/postForm.html"), $buttonText);

if ($_POST) {
    $username = htmlspecialchars(print_r($_POST['username'], true));
    $text = htmlspecialchars(print_r($_POST['message'], true));

    if (strlen($text) > 0 and strlen($text) < 2000 and strlen($username) < 32) {

        $post = new Post(
            $dataStorage->getNextPostId(),
            $text,
            date('Y.m.d H:i:s'),
            $dataStorage->getNextThreadId(),
            $username ? $username : 'Anonymous',
        );
        $dataStorage->appendNewPost($post);
    }
    header('Location: ' . $url, true, 303);
    die();
}

foreach ($dataStorage->getThreadListOrdByLastPost() as $threadId) {
    $thread = new Thread($threadId, $dataStorage);
    $thread->renderShowcase($POSTTEMPLATELOC);
}
