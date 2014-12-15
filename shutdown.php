<?php
namespace ZainPrePend\ShutDown;
function ZainShutDownFunction()
{
    $error = error_get_last();
    if (!$error){
        return ;
    }
    //supressed errors
    if (!error_reporting()){
        return ;
    }
    if (isset($error['type']) && in_array($error['type'], array(E_ERROR, E_PARSE, E_CORE_ERROR, E_CORE_WARNING, E_COMPILE_ERROR, E_COMPILE_WARNING))) {

    }
    else{
        return ;
    }
    ob_clean();
    if (($error['type'] == E_ERROR) && strpos($error['message'],'memory')){
        \Mage::reset();
    }
    debug_print_backtrace();
    //convert error type into string
    $errorConstants = get_defined_constants(true);
    $errorConstants = $errorConstants['Core'];
    //filter errors in the constants
    $errorConstants = array_intersect_key($errorConstants, array_flip(preg_grep('/^E_(\w+)/i', array_keys($errorConstants))));
    if (array_search($error['type'],$errorConstants)!==false){
        $error['type'] = array_search($error['type'],$errorConstants);
    }

    \ZainPrePend\lib\printr($error);
    if (function_exists('xdebug_get_function_stack')){
        \ZainPrePend\lib\printr(xdebug_get_function_stack());
    }
    \ZainPrePend\lib\printr($error);
    if (function_exists('xdebug_break')){
        xdebug_break();
    }
}

register_shutdown_function('\ZainPrePend\ShutDown\ZainShutDownFunction');

//$f=false;
//$f->nocall();