<?php
/**
 *
 * works in index.php

 function write_dbg_stack()
{
static $fStart;
if (!$fStart){
$fStart = microtime(true);
}
if ((microtime(true) -$fStart) > 30){
$GLOBALS['dbg_stack'] = debug_backtrace();
}
}
register_tick_function('write_dbg_stack');
declare(ticks=20);

 *
 */
namespace ZainPrePend\Ticks;

use \ZainPrePend\lib;

return ;
declare(ticks=20);

// using a function as the callback
register_tick_function('my_function', true);

class TickClass {
    function tickMethod()
    {
        $GLOBALS['debugStack'] = debug_backtrace();
    }
}
// using an object->method
$object = new TickClass();
register_tick_function(array(&$object, 'tickMethod'));