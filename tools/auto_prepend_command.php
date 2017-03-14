<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo php_ini_loaded_file() . "<br/>\n<br/>\n<br/>\n";
$aDir = array();
if ($vFileList = php_ini_scanned_files()) {
    if (strlen($vFileList) > 0) {
        $aFiles = explode(',', $vFileList);

        foreach ($aFiles as $vFile) {
            $aDir[dirname(trim($vFile))] = dirname(trim($vFile));
        }
    }
}
$vSeparator = "";
foreach ($aDir as $vDir) {
    $vCurrentDir = dirname(dirname(__FILE__));
    echo "sudo vi $vDir/auto_prepend.ini<br/>\n";
    $vTargetZCustom = "$vCurrentDir/auto_prepend_file.php";
    echo "auto_prepend_file = '$vTargetZCustom'";
    $vSeparator = "<br/>\n<br/>\n<br/>\n<br/>\n";
}

die;