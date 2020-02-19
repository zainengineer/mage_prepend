<?php
$initialClasses = get_declared_classes();

class ClassName
{
    public function __construct()
    {
    }

    public function test()
    {
        //return Mage::getBaseUrl();
        return 1;
    }
}

\ZActionDetect::showOutput(end($initialClasses));
