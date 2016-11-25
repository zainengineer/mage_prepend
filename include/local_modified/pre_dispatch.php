<?php

// Mage::getSingleton('checkout/session')->getQuote()->getData()
$oSession =  Mage::getSingleton('checkout/session');
if ($oSession  && ($oQuote = $oSession->getQuote())){
// Mage::getSingleton('checkout/session')->getQuote->getData()
\ZainPrePend\lib\T::printr($oQuote->getData(),true,'quote');
};