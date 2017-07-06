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
    $action = "this is a sample temp file not actual file";
    \ZainPrePend\lib\T::printr($action, false, '');
    \ZainPrePend\lib\T::printr('snippet complete', false, '');
}

//$bShowNext = true;
if ($bShowNext){
    $bShowNext = false;
    $action = "this is a sample temp file not actual file";
    \ZainPrePend\lib\T::printr($action, false, '');
    \ZainPrePend\lib\T::printr('snippet complete', false, '');
}

\ZainPrePend\lib\T::printr(1,true,'');
die;