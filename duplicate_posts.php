<?php
namespace Zain\DuplicatePosts;
return ;
if (!isset($_SERVER['REQUEST_URI'])) {
    return;
}
if (!$_POST){
    return ;
}
$requestVars = json_encode(array_merge($_POST, $_GET, $_COOKIE,array($_SERVER['REQUEST_URI'])));
$filePath = '/tmp/duplicate_post.txt';
$fileExists = file_exists($filePath);
$contents = @file_get_contents($filePath);

if ($contents) {
    $contents = json_decode($contents, true);
    $lastTime = $contents['time'];
    $lastRequestVars = $contents['RequestVars'];
    $diff = time() - $lastTime;
    if (($diff < 3) && ($lastRequestVars == $requestVars)) {
//        \ZainPrePend\MageInclude\in
        if (function_exists('xdebug_break')){
            xdebug_break();
        }
        die;
    }
}
$payLoad = array(
    'time'        => time(),
    'RequestVars' => $requestVars,
);
$payLoad = json_encode($payLoad);
file_put_contents($filePath, $payLoad);
if (!$fileExists) {
    chmod($filePath, 0777);
}