<?php
namespace ZainPrePend\MageInclude;

class EavInspect
{
    protected $iProductId;
    /** @var  \Varien_Db_Adapter_Interface */
    protected $rRead;
    /** @var \Mage_Core_Model_Resource  */
    protected $rCore;
    protected $bShowAttributeId = false;
    protected $bShowTable = false;
    public function __construct($iProductId, $bShowAttributeId, $bShowTable)
    {
        $this->iProductId = $iProductId;

        $this->rCore = \Mage::getSingleton('core/resource');
        $this->rRead = $this->rCore->getConnection('core_read');

        $this->bShowAttributeId = $bShowAttributeId;
        $this->bShowTable= $bShowTable;
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
        $aTypeList = array(
           'varchar',
           'int',
           'decimal',
           'text',
           'datetime',
        );
        $aOutput = array();
        foreach ($aTypeList as $vType) {
            $vTable = "catalog_product_entity_$vType";
            $aEav =  $this->inspectEavTable($vTable);
            if ($this->bShowTable){
                $aOutput[$vTable] = $aEav;
            }
            else{
                $aOutput= array_merge($aOutput,$aEav);
            }
        }
        return $aOutput;
    }

    protected function inspectEavTable($vTable)
    {
        $vSql = "SELECT *  FROM $vTable WHERE (entity_id = '{$this->iProductId}')";
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
            if ($this->bShowAttributeId){
                $aEavData[$iStoreId][ $aSingleRow['attribute_id'] . '/' .$vAttributeCode] = $aSingleRow['value'];
            }
            else{
                $aEavData[$iStoreId][$vAttributeCode] = $aSingleRow['value'];
            }

        }

        return $aEavData;

    }
}