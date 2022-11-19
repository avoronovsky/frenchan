<?php
include("./configs.php");
include($POSTFORMCLASSLOC);
include($THREADCLASSLOC);


$PAGINATIONSTEP = 20;

$url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

$url_components = parse_url($url);
parse_str($url_components['query'], $params);
$currentPage = $params['page'] ? $params['page'] : 0;

$dataStorage = new DataBase($DATABASECREDS);

$pageTitle = "/uat - Frenchan";
printf(file_get_contents($HEADTEMPLATELOC), $pageTitle);


$cleanUrl = $currentPage > 0 ? strtok($url, '?') : "..";
$navBarText = $currentPage > 0 ? "Back to 0" : "Back to main";
echo "<a href=$cleanUrl>$navBarText</a>";
echo "<hr>";

if (count($dataStorage->getThreadListOrdByLastPost()) < $currentPage * $PAGINATIONSTEP) {
    echo 'Out of range'; //it's supposed to be done better
    exit(1);
}

$postForm = new PostForm(null);
$postForm->renderForm($POSTFORMTEMPLATELOC);
if ($_POST) {
    $postForm->handlePost($dataStorage, $url);
}

$slice = array_slice($dataStorage->getThreadListOrdByLastPost(), 
                     $currentPage * $PAGINATIONSTEP,
                     $PAGINATIONSTEP);
foreach ( $slice as $threadId) {
    $thread = new Thread($threadId, $dataStorage);
    $thread->renderShowcase($POSTTEMPLATELOC);
}

if (count($dataStorage->getThreadListOrdByLastPost()) > $PAGINATIONSTEP) {
    echo "Go to page:\n";
    $page = 0;
    while (count($dataStorage->getThreadListOrdByLastPost()) > $page * $PAGINATIONSTEP) {
        $pageUrl = strtok($url, '?') . '?page=' . $page;
        echo '<a href="' . $pageUrl . '">' . $page . '</a> ';
        $page += 1;
    }
}
