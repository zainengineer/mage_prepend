<?php
/**
 *  On top of your temp or index include
 * \ZainPrePend\MageInclude\sendResourceNotFound();
 */
namespace ZainPrePend\MageInclude;
/**
 * @param array $aExtension
 */
function sendResourceNotFound($aExtension = array('jpg', 'png', 'css', 'gif'))
{
    if (!isset($_SERVER['REQUEST_URI'])) {
        return;
    }
    $vRequest = $_SERVER['REQUEST_URI'];
    $vExtension = pathinfo($vRequest, PATHINFO_EXTENSION);
    if (in_array($vExtension, $aExtension)) {
        header("HTTP/1.0 404 Not Found");
        echo "PHP continues $vRequest .\n";
        die();
    }
}