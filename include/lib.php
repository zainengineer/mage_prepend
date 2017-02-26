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

    public static function printr($object, $simpleArrayElements = true,$name = '', $htmlEntities = true, $return = false, $options = array())
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
        $vFullFilePath = $bt[0]['file'];
        $file = self::removeBasePath($vFullFilePath);
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
            $vPHPStormFile = self::isVM() ? $file : $vFullFilePath;
            $response .= self::getPhpStormLine($vPHPStormFile, $line);
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
                $response .= @print_r($object, true);
            }
            else {
                print_r($object);
            }
        }
        else {
            if (!empty($options['echo']) || is_string($object)){
                echo "<pre>$object</pre>";
            }
            else{
                var_dump($object);
            }
        }

        if ($htmlEntities) {
            $iLength = ob_get_length();
            if ($iLength > 200000){
                $response= "<b>buffer size is very large ignored</b>";
                ob_clean();
            }
            else{
                $content = ob_get_clean();
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
        if (isset($_ENV['MAGE_BASE_PATH'])){
            //something like '/vagrant/'
            $bp = $_ENV['MAGE_BASE_PATH'];
        }
        else{
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
        }
        $file = str_replace($bp, '', $file);
        return $file;
    }

    public static function getPhpStormLine($vFullPath, $line)
    {
        $vDisplayPath = self::removeBasePath($vFullPath);
        $vActualPath = self::isVM()? $vDisplayPath : $vFullPath;
        return "<a href='http://localhost:8091/?message=$vActualPath:$line'>$vDisplayPath:$line</a>";
    }
    public static function isVM()
    {
        if (file_exists('/vagrant')){
            return true;
        }
        return false;
    }

    public static function showException(\Exception $e)
    {
        \ZainPrePend\ShutDown\T::printException($e);
    }
    public static function filterArray($aData, $vFilter)
    {
        if (!$aData){
            $aData = array();
        }
        if (!$vFilter){
            return $aData;
        }
        $aReturn = array();
        foreach ($aData as $vKey => $value) {
            if (strpos($vKey,$vFilter)!==false){
                $aReturn[$vKey] = $value;
            }
        }
        return $aReturn;
    }

    public static function log($content)
    {
        return Logger::addLog($content);
    }
    public static function dumpContentToFile($content, $varExport = true)
    {
        Logger::dumpContentToFile($content,$varExport);
    }

}

Class Logger
{
    public static $log = array();
    public static $appendLogToFile = true;
    public static $appendLogFile = '/local_modified/zain_log_prepend.txt';
    public static $callInit = true;

    public static function init()
    {
        if (!self::$callInit) {
            return;
        }
        self::$callInit = true;
        self::$appendLogFile = dirname(__FILE__) . self::$appendLogFile;
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
    public static function log($content)
    {
        return self::addLog($content);
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
        if (is_writable(self::$appendLogFile)) {
            @chmod(self::$appendLogFile,0777);
        }
        else{
            if (function_exists('xdebug_break')){
                xdebug_break();
            }
            throw new \Exception(self::$appendLogFile . ' not writable ');
        }

    }

    public static function dumpContentToFile($content, $varExport = true)
    {
        $dumpFile = dirname(__FILE__) . '/local_modified/dump.txt';
        $dumpContent = $content;
        if ($varExport && (!is_string($content))) {
            $dumpContent = var_export($dumpContent, true);
        }
        file_put_contents($dumpFile, $dumpContent);
        if (function_exists('xdebug_break')) {
            xdebug_break();
        }
        chmod($dumpFile,0777);
        return is_string($dumpContent) ? strlen($dumpContent) : count($dumpContent);
    }
}

/**
 * $fTimeTaken = end($dummy = array(\ZainPrePend\lib\StopWatch::start(),null, \ZainPrePend\lib\StopWatch::lap()));
 * \ZainPrePend\lib\T::printr(number_format($fTimeTaken,6));
 * Class StopWatch
 * @package ZainPrePend\lib
 */
Class StopWatch {
    protected static $fTime  =0;
    public static function start()
    {
        self::$fTime = microtime(true);
    }
    public static function lap()
    {
        $fCurrenttime = microtime(true);
        $fDiff = $fCurrenttime - self::$fTime;
        return $fDiff;
    }
}