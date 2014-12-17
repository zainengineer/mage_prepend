<?php
namespace ZainPrePend\Param;

if (!empty($_GET['cron'])){
    include dirname(dirname(__FILE__)) . '/cron.php';
    die;
}
