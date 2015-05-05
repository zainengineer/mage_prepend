<?php
namespace ZainPrePend\ShutDown;

use \ZainPrePend\lib;

function ZainShutDownFunction()
{
    $error = error_get_last();
    if (!$error) {
        return;
    }
    //suppressed errors
    if (!error_reporting()) {
        return;
    }
    if (isset($error['type']) && in_array($error['type'], array(E_ERROR, E_PARSE, E_CORE_ERROR, E_CORE_WARNING, E_COMPILE_ERROR, E_COMPILE_WARNING))) {

    }
    else {
        return;
    }
    $file = isset($error['file']) ? $error['file'] : false;
    $line = isset($error['line']) ? $error['line']: false;
    if (strpos($file,'xdebug://debug-eval')===0) {
        return;
    }
    @ob_clean();
    if (($error['type'] == E_ERROR) && strpos($error['message'], 'memory')) {
        \Mage::reset();
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

class T
{
    static function printException(\Exception $e)
    {
        $file = $e->getFile();
        $line = $e->getLine();

        $stormLine = lib\T::getPhpStormLine($file, $line);
        echo "\n<br/>$stormLine <br/>\n";
        lib\T::printr($e->getMessage(), 'Error Message');
        self::printTrace($e->getTrace());
    }

    static function printTrace(array $trace)
    {
        $i = 0;
        $replaceKeyList = array();
        $replaceValueList = array();

        foreach ($trace as &$call) {
            $i++;
            if (empty($call['file']) || empty($call['file'])){
                $call = array('location' => 'missing in trace') + $call;
                continue;
            }
            $file = $call['file'];
            $line = $call['line'];
            $phpStormLink = lib\T::getPhpStormLine($file, $line);
            unset($call['file']);
            unset($call['line']);

            $call = array('location' => 'ZainReplaceIt' . $i) + $call;
            $replaceKeyList[] = $call['location'];
            $replaceValueList[] = $phpStormLink;
        }

        $output =  lib\T::printr($trace,'Trace',false,false,true,true);
        $output = str_replace($replaceKeyList, $replaceValueList, $output);
        echo $output;
    }
}

register_shutdown_function('\ZainPrePend\ShutDown\ZainShutDownFunction');

//$f=false;
//$f->nocall();