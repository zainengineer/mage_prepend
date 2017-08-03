<?php
if (!isset($_GET['op'])){
    return;
}
$vOp = $_GET['op'];
if ($vOp == 'phpinfo'){
    phpinfo();
    exit;
}
if ($vOp == 'xdebug'){
    $debug =1;
    xdebug_break();
    \ZainPrePend\lib\T::printr(1,true,'');
    exit;
}
if ($vOp == 'mage_info'){
    \ZainPrePend\Code\CodePool::getInstance();
}