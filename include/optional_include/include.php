<?php

if (empty($_GET['zop'])){
    return ;
}
require_once __DIR__ . '/ZReflection.php';
$zop = $_GET['zop'];
if ($zop=='snippets'){
    include __DIR__ . '/snippets.php';
}
else{
    require_once __DIR__ . '/snippet_include.php';
}

die;
