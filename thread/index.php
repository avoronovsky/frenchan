<?php
include("../configs.php");
include($POSTFORMCLASSLOC);
include($THREADCLASSLOC);

$url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
$url_components = parse_url($url);

parse_str($url_components["query"], $params);
if ($params['id']) {
    $currentThread = $params['id'];
}

$dataStorage = new DataBase($DATABASECREDS);

$pageTitle = "/uat thread #$currentThread - Frenchan";
printf(file_get_contents($HEADTEMPLATELOC), $pageTitle);

$cleanUrl = "..";
echo "<a href=$cleanUrl>Back to board</a>";
echo "<hr>";

if (! in_array($currentThread, $dataStorage->getThreadListOrdByLastPost())) { 
   echo "No such thread";
   exit(1);
}

$postForm = new PostForm($currentThread);
$postForm->renderForm($POSTFORMTEMPLATELOC);
if ($_POST) {
    $postForm->handlePost($dataStorage, $url);
}

$thread = new Thread($currentThread, $dataStorage);
$thread->renderThread($POSTTEMPLATELOC);
