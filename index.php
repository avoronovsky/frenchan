<?php
include("configs.php");
include($POSTFORMCLASSLOC);
include($THREADCLASSLOC);


$url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

$dataStorage = new DataBase($DATABASECREDS);

$pageTitle = "/ - Frenchan Board";
printf(file_get_contents($HEADTEMPLATELOC), $pageTitle);

$postForm = new PostForm(null);
$postForm->renderForm($POSTFORMTEMPLATELOC);
if ($_POST) {
    $postForm->handlePost($dataStorage, $url);
}

foreach ($dataStorage->getThreadListOrdByLastPost() as $threadId) {
    $thread = new Thread($threadId, $dataStorage);
    $thread->renderShowcase($POSTTEMPLATELOC);
}
