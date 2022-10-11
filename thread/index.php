<?php
include("../class/Thread.php");

//$JSONLOC = "../messages";
$POSTTEMPLATELOC = "../templates/postTemplate.html";
$HEADTEMPLATELOC = "../head.html";

$url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
$url_components = parse_url($url);

parse_str($url_components["query"], $params);
if ($params['id']) {
    $currentThread = $params['id'];
}

//$dataStorage = new DataStorage($JSONLOC);
$dataStorage = new DataBase();

$pageTitle = "Frenchan - Thread #$currentThread";
printf(file_get_contents($HEADTEMPLATELOC), $pageTitle);

$cleanUrl = "http://$_SERVER[HTTP_HOST]";
echo "<a href=$cleanUrl>Back to board</a>";
echo "<hr>";

$buttonText = "../Assets/postpixel.png";
printf(file_get_contents("../templates/postForm.html"), $buttonText);

if ($_POST) {
    $username = htmlspecialchars(print_r($_POST['username'], true));
    $text = htmlspecialchars(print_r($_POST['message'], true));

    if (strlen($text) > 0 and strlen($text) < 2000 and strlen($username) < 32) {

        $post = new Post(
            $dataStorage->getNextPostId(),
            $text,
            date('Y.m.d H:i:s'),
            $currentThread,
            $username ? $username : 'Anonymous',
        );
        $dataStorage->appendNewPost($post);
    }
    header('Location: ' . $url, true, 303);
    die();
}

$thread = new Thread($currentThread, $dataStorage);
$thread->renderThread($POSTTEMPLATELOC);
