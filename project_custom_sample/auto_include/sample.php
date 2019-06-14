<?php
if (empty($_GET['op'])){
    return;
}
$op  = $_GET['op'];
if ($op != 'sample'){
    return ;
}

ZainPrePend\MageInclude\includeMage();

\ZainPrePend\lib\T::printr('start',true,'');

$result = Mage::getStoreConfig('web/unsecure/base_url');

\ZainPrePend\lib\T::printr($result,true,'result');
\ZainPrePend\lib\T::printr('finished',true,'');
die;
