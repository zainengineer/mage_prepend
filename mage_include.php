<?php
namespace ZainPrePend\MageInclude;
function includeMage()
{
    showErrors();
    if (class_exists('Mage')) {
        return;
    }
    if (!empty($_GET['no_mage'])) {
        echo "<br/> cannot include mage because of get parameter no_mage  File:" . __FILE__ . " line:" . __LINE__ . "<br/>\r\n";
        return;
    }
    set_time_limit(0);
    $fileName = dirname(__FILE__) . '/../app/Mage.php';
    require_once($fileName);
    \Mage::app();
    showErrors();
    \Mage::setIsDeveloperMode(true);
}

function showErrors()
{
    static $repeat = false;
    if ($repeat)
        return;
    $repeat = true;

    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}
