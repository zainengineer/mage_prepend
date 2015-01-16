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
    require_once dirname(__FILE__) . '/include/shutdown.php';
    require_once dirname(__FILE__) . '/include/lib.php';
    require_once dirname(__FILE__) . '/include/mage_include.php';
    require_once dirname(__FILE__) . '/include/param_response.php';
    $projectCustomFile = dirname(__FILE__) . '/project_custom/include.php';
    if (file_exists($projectCustomFile)) {
        require_once $projectCustomFile;
    }
    require_once dirname(__FILE__) . '/session_destroy.php';
    require_once dirname(__FILE__) . '/include/cache_url.php';
    require_once dirname(__FILE__) . '/include/duplicate_posts.php';
    require_once dirname(__FILE__) . '/include/temp.php';
}
catch (\Exception $e){
    \ZainPrePend\ShutDown\T::printException($e);
    die;
}
