<?php
$initialClasses = get_declared_classes();

class ClassName
{
    public function __construct()
    {
//        Mage::app()->setCurrentStore('default');
//        Mage::getSingleton('core/session', array('name' => 'frontend'))->start();
    }

    public function test()
    {
        //return Mage::getBaseUrl();
        return 1;
    }
}

\ZActionDetect::showOutput(end($initialClasses));
