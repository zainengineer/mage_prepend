<?php
namespace ZainPrePend\Index;
define('AUTO_PREPEND_BASE_PATH_Z', dirname(__FILE__));
//so variables do not jump into global name spaces
function executeIndex()
{
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
        require_once dirname(__FILE__) . '/include/local_modified/server_param.php';
        require_once dirname(__FILE__) . '/include/mage_include.php';
        require_once dirname(__FILE__) . '/include/param_response.php';
        require_once dirname(__FILE__) . '/include/eav_inspect.php';
        require_once dirname(__FILE__) . '/include/sql_inspect.php';
        require_once dirname(__FILE__) . '/include/custom_code_pool.php';
        require_once dirname(__FILE__) . '/include/misc.php';
        require_once dirname(__FILE__) . '/include/ignore_resource_requests.php';
        $projectCustomFile = dirname(__FILE__) . '/project_custom/include.php';
        if (file_exists($projectCustomFile)) {
            require_once $projectCustomFile;
        }
        $aAutoInclude = glob(dirname(__FILE__) . '/project_custom/auto_include/*.php');
        foreach ($aAutoInclude as $vAutoFile) {
            require_once $vAutoFile;
        }
        require_once dirname(__FILE__) . '/session_destroy.php';
        require_once dirname(__FILE__) . '/include/cache_url.php';
        require_once dirname(__FILE__) . '/include/duplicate_posts.php';
        require_once dirname(__FILE__) . '/include/remove_admin_product_popup.php';
        require_once dirname(__FILE__) . '/include/ticks.php';
        require_once dirname(__FILE__) . '/include/filter_git.php';
        $vTempFile = dirname(__FILE__) . '/include/local_modified/temp.php';
        require_once $vTempFile;
    }
    catch (\Exception $e){
        \ZainPrePend\ShutDown\T::printException($e);
        die;
    }

}
executeIndex();