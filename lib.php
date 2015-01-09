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

    public static function printr($object, $name = '', $attributes = false, $properties = false, $htmlEntities = true, $return = false)
    {
        $console = false;
        $response = '';
        if (in_array(php_sapi_name(), array('cli'))) {
            $console = true;
        }
        $classHint = '';
        if (($attributes | $properties) && (is_array($object) || is_object($object))) {
            if (is_object($object)) {
                $class = get_class($object);
                if (!$name)
                    $name = $class;
                else
                    $classHint = 'type: ' . $class;
            }
            if (function_exists('getAttributes')) {
                $object = getAttributes($object, $attributes, $properties);
            }
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
        else{
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
            $content = ob_get_clean();
            $response.= htmlentities($content);
        }
        if (!$console) {
            $response.= $preEnd;
            $response.= '</div></div><hr/>';
        }
        if ($return){
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
        echo self::getPhpStormLine($e->getFile(), $e->getLine());
        printr($e);
    }
}

Class Logger
{
    public static $log = array();
    public static $appendLogToFile = true;
    public static $appendLogFile = '/tmp/zain_log_prepend.txt';

    public static function addLog($content)
    {
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
        $content = $content . $newContent . "\n";
        @file_put_contents(self::$appendLogFile, $content);

    }

    public static function dumpContentToFile($content, $varExport = true)
    {
        $dumpFile = dirname(dirname(__FILE__)) . '/dump.txt';
        $dumpContent = $content;
        if ($varExport) {
            $dumpContent = var_export($dumpContent, true);
        }
        file_put_contents($dumpFile, $dumpContent);
    }
}