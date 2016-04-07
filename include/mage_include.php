<?php
namespace ZainPrePend\MageInclude;
function includeMage($code = null)
{
showErrors();
    if (class_exists('\Mage')) {
        return;
    }
    if (!empty($_GET['no_mage'])) {
        echo "<br/> cannot include mage because of get parameter no_mage  File:" . __FILE__ . " line:" . __LINE__ . "<br/>\r\n";
        return;
    }
    set_time_limit(0);
    $fileName = dirname(__FILE__) . '/../../app/Mage.php';
    require_once($fileName);
    \Mage::app();
    if (is_null($code)){
        \Mage::app()->setCurrentStore(0);
    }
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

/**
 *
 * Quick Include magento

umask(0);
require 'app/Mage.php';

\Mage::setIsDeveloperMode(true);
error_reporting(E_ALL);
ini_set('display_errors', 1);
set_time_limit(0);

Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);


 *
 */