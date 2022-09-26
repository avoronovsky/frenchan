<?php
include("class/DataRecord.php");

$JSONLOC = "messages.json";
$POSTTEMPLATELOC = "templates/postTemplate.html";
$HEADTEMPLATELOC = "head.html";

$url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
$url_components = parse_url($url);
parse_str($url_components['query'], $params);
if ($params['thread']) {
    $currentThread = $params['thread'];
}

$dataStorage = new DataRecord($JSONLOC);

$pageTitle = ($currentThread ? "/test2 - Thread #$currentThread" : "/test2 board");
printf(file_get_contents($HEADTEMPLATELOC), $pageTitle);

if ($currentThread) {
    $cleanUrl = strtok($url, '?');
    echo "<a href=$cleanUrl>Back to board</a>";
    echo "<hr>";
}

$buttonText = ($currentThread ? "/Assets/postpixel.png" : "/Assets/create_thread.png");
printf(file_get_contents("templates/postForm.html"), $buttonText);

if ($_POST) {
    $text = htmlspecialchars(print_r($_POST['message'], true));
    $username = htmlspecialchars(print_r($_POST['username'], true));
    $dataStorage->appendNewPost(
        $text, 
        $username ? $username : 'Anonymous',
        $currentThread ? $currentThread : $dataStorage->getNextThreadId()
    );
}

if ($currentThread) {
    $posts = $dataStorage->getPostsByThreadId($currentThread);
    foreach ($posts as $post) {
        $post->renderPost($POSTTEMPLATELOC);
    }
}

if (!$currentThread) {
    $threads = $dataStorage->getThreadList();
    for ($i = 0; $i <= count($threads)-1; $i++) {
        $threadId = $threads[$i];
        $showcase = $dataStorage->createThreadShowcase($threadId);
        $showcase['threadPosts'][0]->renderPost($POSTTEMPLATELOC);
        if ($showcase['morePosts']) {
            $more = $showcase['morePosts'];
            echo "There is $more more posts";
        }
        if (count($showcase['threadPosts']) > 1) {
            foreach (array_reverse(array_slice($showcase['threadPosts'], 1)) as $nextPost) {
                $nextPost->renderPost($POSTTEMPLATELOC);
            }
        }
        printf("<table><tr><th>
        <a href='http://frenchan.zzz.com.ua/test2/?thread=%s'>Proceed to thread</a>
        </th></tr></table>", 
        $threadId);
        echo "<hr>";
    }
}
