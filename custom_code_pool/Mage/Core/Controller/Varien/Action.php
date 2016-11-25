<?php

abstract class Mage_Core_Controller_Varien_Action extends Mage_Core_Controller_Varien_ActionZCustom
{
    
    public function preDispatch()
    {
        parent::preDispatch();
        // Mage::getSingleton('checkout/session')->getQuote()->getData()
        $oSession =  Mage::getSingleton('checkout/session');
        if ($oSession  && ($oQuote = $oSession->getQuote())){
            // Mage::getSingleton('checkout/session')->getQuote->getData()
            \ZainPrePend\lib\T::printr($oQuote->getData(),true,'quote');
        };
        \ZainPrePend\lib\T::printr(1,true,'');
        exit();
    }

}
