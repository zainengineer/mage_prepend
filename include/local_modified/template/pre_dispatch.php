<?php
//use ?op=mage_info
// Mage::getSingleton('checkout/session')->getQuote()->getData()
$oSession =  Mage::getSingleton('checkout/session');
if ($oSession  && ($oQuote = $oSession->getQuote())){
// Mage::getSingleton('checkout/session')->getQuote->getData()
    \ZainPrePend\lib\T::printr($oQuote->getData(),true,'quote');
    \ZainPrePend\lib\T::printr($oQuote->getAllItems(),true,'quote item count');
}else{
    \ZainPrePend\lib\T::printr("no session quote",true,'');
};