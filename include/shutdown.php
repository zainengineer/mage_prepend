<?php
namespace ZainPrePend\ShutDown;
require_once dirname(__FILE__) . '/add_link_to_admin_product.php';
use \ZainPrePend\lib;

function ZainShutDownFunction()
{
    $error = error_get_last();
    if (!$error) {
        outPutAfterSuccess();
        return;
    }
    if (!$error) {
        outPutAfterSuccess();
        return;
    }
    $file = isset($error['file']) ? $error['file'] : false;
    $line = isset($error['line']) ? $error['line'] : false;
    /**
     * Error suppression was used, but it could be a fatal error
     * No Good work around as we can't be sure if it is a normal error or fatal error
     * error_get_last() could be last error which did not cause fatal
     * or it is fatal error which stopped code flow
     * both have same data in error_get_last()
     * So just using xdebug_break as a notice / help for developer to find the issue.
     * Normally it happens when @include a file with syntax error (typed random values)
     */
    //
    //
    if (!error_reporting()){
        if (strpos($file, 'xdebug://debug-eval') === false) {
            if (function_exists('xdebug_break')){
                xdebug_break();
            }
            if (!empty($_ENV['ignore_last_error'])){
                return;
            }
        }
    }
    //suppressed errors
    if (!error_reporting() && !$error) {
        outPutAfterSuccess();
        return;
    }
    if (isset($error['type']) && in_array($error['type'], array(E_ERROR, E_PARSE, E_CORE_ERROR, E_CORE_WARNING, E_COMPILE_ERROR, E_COMPILE_WARNING))) {

    }
    else {
        outPutAfterSuccess();
        return;
    }
    if (strpos($file, 'xdebug://debug-eval') === 0) {
        outPutAfterSuccess();
        return;
    }
    if ($error['type'] == E_ERROR){
        $aBackTrace = debug_backtrace();
        if (class_exists('\Mage') && !\Mage::getIsDeveloperMode() && (count($aBackTrace)>1)){
            outPutAfterSuccess();
            return ;
        }
    }
    @ob_clean();
    if (($error['type'] == E_ERROR) && strpos($error['message'], 'memory')) {
        \Mage::reset();
    }
    $iMemoryUsage =  memory_get_usage();
    if ($iMemoryUsage < 2 * pow(10,9)){
        ini_set('memory_limit','2G');
    }
    debug_print_backtrace();
    //convert error type into string
    $errorConstants = get_defined_constants(true);
    $errorConstants = $errorConstants['Core'];
    //filter errors in the constants
    $errorConstants = array_intersect_key($errorConstants, array_flip(preg_grep('/^E_(\w+)/i', array_keys($errorConstants))));
    if (array_search($error['type'], $errorConstants) !== false) {
        $error['type'] = array_search($error['type'], $errorConstants);
    }
    $phpStormRemote = true;
    if ($phpStormRemote && $file && $line) {
        //ignore xdebug errors
        if (strpos($file, 'xdebug') !== false) {
            xdebug_break();
        }
        if (!empty($error['type']) && !empty($error['message']) &&
            $error['type'] == 'E_ERROR' && (strpos($error['message'], 'Uncaught exception') === 0)
            && ($errorTrace = lib\T::getBetweenString($error['message'], "Stack trace:\n", ""))
        ) {
            $counter = 0;
            while (true) {
                $lineInfo = lib\T::getBetweenString($errorTrace, "#$counter", ":");
                $counter++;
                $tempFile = lib\T::getBetweenString($lineInfo, " ", "(");
                $tempLine = lib\T::getBetweenString($lineInfo, "(", ")");
                $internalFunction = (trim($lineInfo) == '[internal function]');
                if ((!$tempFile || !$tempLine) && !$internalFunction) {
                    break;
                }
                $tempFile = str_replace(BP . '/', '', $tempFile);
                //if error is originating form lib/ or core/ , I dont want to click it
                if ((strpos($tempFile, 'lib/') === 0) ||
                    (strpos($tempFile, 'app/core/') === 0) ||
                    (strpos($tempFile, 'app/Mage') === 0) ||
                    //not sure if it is needed but some project use core overwrites for basic things
                    (strpos($tempFile, 'app/code/local/Mage/Core') === 0)
                    || $internalFunction
                ) {
                    continue;
                }
                $file = $tempFile;
                $line = $tempLine;
                break;
            }

        }
        $stormLine = lib\T::getPhpStormLine($file, $line);
        //to copy paste form debugger
        $simpleLine = lib\T::removeBasePath($file) . ":$line";
        echo "\n<br/>$stormLine <br/>\n";
        T::jsClearPageAndDisplayError($stormLine, $error);
    }
    lib\T::printr($error);
    if (function_exists('xdebug_get_function_stack')) {
        T::printTrace(xdebug_get_function_stack());
    }
    lib\T::printr($error);
    if (function_exists('xdebug_break')) {
        xdebug_break();
    }
}

function outPutAfterSuccess()
{
    \ZainPrePend\AdminProductLink\T::addProductLink();
}

class T
{
    static function printException(\Exception $e)
    {
        $file = $e->getFile();
        $line = $e->getLine();

        $stormLine = lib\T::getPhpStormLine($file, $line);
        echo "\n<br/>$stormLine <br/>\n";
        self::displayMagentoErrorSourceLink($e->getMessage());
        lib\T::printr($e->getMessage(), 'Error Message');
        self::printTrace($e->getTrace());
    }

    public static function displayMagentoErrorSourceLink($vMessage)
    {
        $aMessage = explode(' ', $vMessage);
        $iCount = count($aMessage);
        if ($iCount < 5) {
            return;
        }
        $vOnLine = $aMessage[$iCount - 3] . ' ' . $aMessage [$iCount - 2];
        if ($vOnLine != 'on line') {
            return;
        }
        $iLineNumber = $aMessage[$iCount - 1];
        if (!is_numeric($iLineNumber)) {
            return;
        }
        $vFile = $aMessage[$iCount - 4];
        $stormLine = lib\T::getPhpStormLine($vFile, $iLineNumber);
        echo "\n$stormLine <br/><br/>\n";
    }

    static function printTrace(array $trace)
    {
        $aReplace = array();

        foreach ($trace as $key => &$call) {
            if (empty($call['file']) || empty($call['file'])) {
                $call = array('location' => 'missing in trace') + $call;
                continue;
            }
            $file = $call['file'];
            $line = $call['line'];
            $phpStormLink = lib\T::getPhpStormLine($file, $line);
            unset($call['file']);
            unset($call['line']);

            $call = array('location' => 'ZainReplaceIt' . $key) + $call;
            $aReplace[$call['location']] = $phpStormLink;
        }

        $output = lib\T::printr($trace, false,'Trace', false, true);
        //using strtr instead of replace as ZainReplace1 and ZainReplace10 will conflict causing incorrect replacement
        $output = strtr($output,$aReplace);
        echo $output;
    }

    public static function jsClearPageAndDisplayError($vStormLine, $aError)
    {
        return ;
        $aConfig = array('stormLine' => $vStormLine);
        if (function_exists('xdebug_get_function_stack')){
            ob_start();
            echo "<pre>";
            debug_print_backtrace();
            echo "</pre>";
            lib\T::printr($aError);
            if (function_exists('xdebug_get_function_stack')) {
                T::printTrace(xdebug_get_function_stack());
            }
            $vErrorDump = ob_get_clean();
        }
        else{
            echo 'Could not get error dump because xdebug not enabled';
            die;
        }

        $aConfig['errorDump'] = $vErrorDump;
        $vConfig = json_encode($aConfig);
        ?>
        <script>
            config = <?php echo $vConfig; ?>;
            setTimeout(function () {
                document.clear();
                document.write(config.stormLine);
                document.write(config.errorDump);
            }, 100);
        </script>
    <?php
    }
}
if (php_sapi_name() !== 'cli'){
    register_shutdown_function('\ZainPrePend\ShutDown\ZainShutDownFunction');
}

//$f=false;
//$f->nocall();