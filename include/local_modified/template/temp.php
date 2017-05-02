<?php
ZainPrePend\MageInclude\sendResourceNotFound();
if (!isset($_GET['op']) || ($_GET['op'] != 'temp')){
    return;
}
\ZainPrePend\MageInclude\includeMage();

$bShowNext = false;


//$bShowNext = true;
if ($bShowNext){
    $bShowNext = false;
    $action = "put actual code here";
    \ZainPrePend\lib\T::printr($action, false, '');
}

//$bShowNext = true;
if ($bShowNext){
    $bShowNext = false;
    $action = "put actual code here";
    \ZainPrePend\lib\T::printr($action, false, '');
}

\ZainPrePend\lib\T::printr(1,true,'');
die;