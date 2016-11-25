<?php

abstract class Mage_Core_Controller_Varien_Action extends Mage_Core_Controller_Varien_ActionZCustom
{
    
    public function preDispatch()
    {
        parent::preDispatch();
        $vPreDispatchInclude = AUTO_PREPEND_BASE_PATH_Z . '/include/local_modified/pre_dispatch.php';
        if (file_exists($vPreDispatchInclude)){
            include $vPreDispatchInclude;
        }
        \ZainPrePend\lib\T::printr(1,true,'');
        exit();
    }

}
