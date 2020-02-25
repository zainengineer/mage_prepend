<?php

//common typo and muscle memory, so work around
if (isset($_GET['op']) && ($_GET['op']=='snippets')){
    $_GET['zop'] = 'snippets';
}
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
