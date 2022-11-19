<?php

$regex = "/>>[0-9]+\b/";
$text = ">>1 >>as2 >>2a >>23 u r wrong >>sad >>923 as";
preg_match_all($regex, $text, $matches);

$postIds = preg_filter("/>>/", "", $matches[0]);
foreach ($postIds as $postId) {
    echo $postId. "\n";
}

