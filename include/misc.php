<?php
namespace ZainPrePend\MageInclude;

if (!isset($_GET['op'])){
    return;
}
$vOp = $_GET['op'];
if ($vOp == 'phpinfo'){
    phpinfo();
    exit;
}