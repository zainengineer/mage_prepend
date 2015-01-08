<?php
namespace ZainPrePend\Index;
try {
    if (php_sapi_name() != 'cli') {
        ob_start();
    }
    if (isset($_SERVER["REQUEST_URI"])) {
        if ($_SERVER["REQUEST_URI"] == '/health_check.php') {
            return;
        }
    }
    require_once dirname(__FILE__) . '/shutdown.php';
    require_once dirname(__FILE__) . '/lib.php';
    require_once dirname(__FILE__) . '/mage_include.php';
    require_once dirname(__FILE__) . '/param_response.php';
    $projectCustomFile = dirname(__FILE__) . '/project_custom/include.php';
    if (file_exists($projectCustomFile)) {
        require_once $projectCustomFile;
    }
    require_once dirname(__FILE__) . '/session_destroy.php';
    require_once dirname(__FILE__) . '/cache_url.php';
    require_once dirname(__FILE__) . '/duplicate_posts.php';
    require_once dirname(__FILE__) . '/temp.php';
}
catch (\Exception $e){
    \ZainPrePend\ShutDown\printException($e);
    die;
}
