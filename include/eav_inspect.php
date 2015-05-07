<?php
namespace ZainPrePend\MageInclude;

class EavInspect
{
    protected $iProductId;
    /** @var  \Varien_Db_Adapter_Interface */
    protected $rRead;
    /** @var \Mage_Core_Model_Resource  */
    protected $rCore;
    public function __construct($iProductId)
    {
        $this->iProductId = $iProductId;

        $this->rCore = \Mage::getSingleton('core/resource');
        $this->rRead = $this->rCore->getConnection('core_read');
    }
    public function inspect()
    {
        $aMain = $this->inspectMain();
        $aEav = $this->inspectEav();
        $aReturn = array(
            'Main Table' => $aMain,
            'Eav' => $aEav,
        );
        return $aReturn;
    }
    protected function inspectMain()
    {
//        $vTableName = $this->rCore->getTableName('catalog_product_entity');
        $vTableName = 'catalog_product_entity';
        $vSql  = "select * from $vTableName WHERE entity_id = {$this->iProductId}";
        return $this->rRead->fetchRow($vSql);
    }
    protected function inspectEav()
    {
        $vSql = <<< zHereDoc

SELECT `attr_table`.*  FROM `catalog_product_entity_varchar` AS `attr_table`
 WHERE (attr_table.entity_id = '{$this->iProductId}')

  UNION ALL SELECT `attr_table`.* FROM `catalog_product_entity_int` AS `attr_table`
  WHERE (attr_table.entity_id = '{$this->iProductId}')

  UNION ALL SELECT `attr_table`.* FROM `catalog_product_entity_decimal` AS `attr_table`
  WHERE (attr_table.entity_id = '{$this->iProductId}')

  UNION ALL SELECT `attr_table`.* FROM `catalog_product_entity_text` AS `attr_table`
 WHERE (attr_table.entity_id = '{$this->iProductId}')

 UNION ALL SELECT `attr_table`.* FROM `catalog_product_entity_datetime` AS `attr_table`
 WHERE (attr_table.entity_id = '{$this->iProductId}')

zHereDoc;



        $aAllRows = $this->rRead->fetchAll($vSql);
        $aAttributeId = array();
        foreach ($aAllRows as $aSingleRow) {
            $aAttributeId[$aSingleRow['attribute_id']] = (int) $aSingleRow['attribute_id'];
        }
        $vAttributeList = implode(',',$aAttributeId);
        if (!$vAttributeList){
            throw new \Exception('No Eav attribute found for ' . $this->iProductId);
        }
        $vSql = "SELECT attribute_id,attribute_code FROM eav_attribute WHERE attribute_id IN ($vAttributeList)";
        $aAttributeList = $this->rRead->fetchPairs($vSql);
        $aEavData = array();
        foreach ($aAllRows as $aSingleRow) {
            $iStoreId = (int) $aSingleRow['store_id'];
            $vAttributeCode = $aAttributeList[ $aSingleRow['attribute_id']];
            $aEavData[$iStoreId][$vAttributeCode] = $aSingleRow['value'];
        }

        return $aEavData;

    }
}