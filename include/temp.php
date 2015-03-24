<?php
//return;
\ZainPrePend\MageInclude\includeMage();

$cron = new Candm_Harmony_Model_Cron();
$cron->fetchProducts();

die;
$oProduct = Mage::getModel('catalog/product')->load(5104);
$cAttribute = Mage::getResourceModel('catalog/product_type_configurable_attribute_collection')
    ->setProductFilter($oProduct)
    ->orderByPosition()
//    ->load()
;
\ZainPrePend\lib\T::printr((string) $cAttribute->getSelect());
//\ZainPrePend\lib\T::printr($cAttribute->toArray());
/** @var $oAttribute  Mage_Catalog_Model_Resource_Eav_Attribute */
foreach ($cAttribute as $oAttribute) {
    \ZainPrePend\lib\T::printr($oAttribute->getProductAttribute()->getData());
    die;
}

die;