<?php
namespace ZainPrePend\lib;

Class T
{

    /**
     * @param $contents
     * @param $start
     * @param $end
     * @param bool $removeStart
     * @param bool $removeEnd
     * @return string
     */
    static public function getBetweenString($contents, $start, $end, $removeStart = true, $removeEnd = true)
    {
        if ($start) {
            $startPos = strpos($contents, $start);
        }
        else {
            $startPos = 0;
        }
        if ($startPos === false) {
            return false;
        }
        if ($end) {
            $endPos = strpos($contents, $end, $startPos);
            if ($endPos === false) {
                $endPos = $endPos = strlen($contents);
            }
        }
        else {
            $endPos = strlen($contents);
        }
        if ($removeStart) {
            $startPos += strlen($start);
        }
        $len = $endPos - $startPos;
        if (!$removeEnd && $end && $endPos) {
            $len = $len + strlen($end);
        }
        $subString = substr($contents, $startPos, $len);
        return $subString;
    }

    public static function getSimpleMembers($aList, $maintainKey, $addPlaceHolders)
    {
        $aResult = array();
        foreach ($aList as $key => $value) {
            if (is_array($value) || is_object($value)) {
                if ($addPlaceHolders){
                    $vType = is_object($value) ? get_class($value) : gettype($value);
                    $value = "[zain_bold]Removed:[/zain_bold] " . $vType;
                }
                else{
                    continue;
                }
            }
            if ($maintainKey) {
                $aResult[$key] = $value;
            }
            else {
                $aResult[] = $value;
            }
        }
        return $aResult;

    }

    public static function printr($object, $name = '', $simpleArrayElements = true, $htmlEntities = true, $return = false)
    {
        $console = false;
        $response = '';
        if (in_array(php_sapi_name(), array('cli'))) {
            $console = true;
        }
        $classHint = '';
        if (($simpleArrayElements) && (is_array($object) )) {
            $object = self::getSimpleMembers($object,true,true);
        }
        $bt = debug_backtrace();
        $file = $bt[0]['file'];
        $file = self::removeBasePath($file);
        $line = $bt[0]['line'];
        $preStart = '<pre>';
        $preEnd = '</pre>';
        //xdebug overloads var_dump with html so ignore that
        if (!is_array($object) && function_exists('xdebug_break')) {
            $htmlEntities = false;
            $preStart = '';
            $preEnd = '';
        }
        if ($console) {
            $htmlEntities = false;
            $response .= $file . ' on line ' . $line . " $name is: ";
        }
        else {

            $phpStormRemote = true;
            $response .= self::getPhpStormLine($file, $line);
            $response .= '<div style="background: #FFFBD6">';
             $nameLine = '';
            if ($name)
                $nameLine = '<b> <span style="font-size:18px;">' . $name . "</span></b> $classHint printr:<br/>";
            $response .= '<span style="font-size:12px;">' . $nameLine . ' ' . $file . ' on line ' . $bt[0]['line'] . '</span>';
            $response .= '<div style="border:1px so lid #000;">';
            $response .= $preStart;
        }
        if ($return) {
            $htmlEntities = false;
        }
        else {
            echo $response;
            $response = '';
        }
        if ($htmlEntities) {
            ob_start();
        }
        if (is_array($object) | $return) {
            if ($return) {
                $response .= print_r($object, true);
            }
            else {
                print_r($object);
            }
        }
        else {
            var_dump($object);
        }

        if ($htmlEntities) {
            if (ob_get_length() > 5000){
                $content = "<b>buffer size is very large ignored</b>";
                ob_clean();
            }
            else{
                $content = ob_get_clean();
            }
            if (strlen($content) > 5000){
                $response.= "<b>content very large ignored<b/>";
            }
            else{
                $response .= htmlentities($content);
                $response = str_replace(array('[zain_bold]','[/zain_bold]'), array('<b>','</b>'),$response);
            }
        }
        if (!$console) {
            $response .= $preEnd;
            $response .= '</div></div><hr/>';
        }
        if ($return) {
            return $response;
        }
        echo $response;
    }

    public static function removeBasePath($file)
    {
        $bp = '';
        $possibleBasePath = __DIR__;
        if (strpos($file, $possibleBasePath) === 0) {
            $bp = $possibleBasePath . '/';
        }
        if (!$bp) {
            $possibleBasePath = dirname(__DIR__);
            if (strpos($file, $possibleBasePath) === 0) {
                $bp = $possibleBasePath . '/';
            }
        }

        if (!$bp) {
            $possibleBasePath = dirname(dirname(__DIR__));
            if (strpos($file, $possibleBasePath) === 0) {
                $bp = $possibleBasePath . '/';
            }
        }
        $file = str_replace($bp, '', $file);
        return $file;
    }

    public static function getPhpStormLine($file, $line)
    {
        $file = self::removeBasePath($file);
        return "<a href='http://localhost:8091/?message=$file:$line'>$file:$line</a>";
    }

    public static function showException(\Exception $e)
    {
        \ZainPrePend\ShutDown\T::printException($e);
    }
}

Class Logger
{
    public static $log = array();
    public static $appendLogToFile = true;
    public static $appendLogFile = '/tmp/zain_log_prepend.txt';
    public static $callInit = true;

    public static function init()
    {
        if (!self::$callInit) {
            return;
        }
        self::$callInit = true;
        self::$appendLogFile = dirname(__FILE__) . '/temp/zain_log_prepend.txt';
        $targetDirectory = dirname(self::$appendLogFile);
        if (!file_exists($targetDirectory)) {
            mkdir($targetDirectory);
        }
    }

    public static function addLog($content)
    {
        self::init();
        self::$log[] = $content;
        self::appendLogToFile($content);
    }

    public static function appendLogToFile($newContent)
    {
        if (!self::$appendLogToFile) {
            return;
        }
        $content = '';
        if (@file_exists(self::$appendLogFile)) {
            $content = file_get_contents(self::$appendLogFile);
        }
        $newContent = is_string($newContent) ? $newContent : var_export($newContent, true);
        $content = $content . $newContent . "\n";
        @file_put_contents(self::$appendLogFile, $content);

    }

    public static function dumpContentToFile($content, $varExport = true)
    {
        $dumpFile = dirname(dirname(__FILE__)) . '/dump.txt';
        $dumpContent = $content;
        if ($varExport && (!is_string($content))) {
            $dumpContent = var_export($dumpContent, true);
        }
        file_put_contents($dumpFile, $dumpContent);
        if (function_exists('xdebug_break')) {
            xdebug_break();
        }
        return is_string($dumpContent) ? strlen($dumpContent) : count($dumpContent);
    }
}