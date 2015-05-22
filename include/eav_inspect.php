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
    /**
     * @var
     * customer_entity
     * catalog_product_entity
     */
    protected $vEntityTable;
    public function __construct($iProductId, $bShowAttributeId, $bShowTable, $vEntityTable)
    {
        $this->iProductId = $iProductId;

        $this->rCore = \Mage::getSingleton('core/resource');
        $this->rRead = $this->rCore->getConnection('core_read');

        $this->bShowAttributeId = $bShowAttributeId;
        $this->bShowTable= $bShowTable;
        $this->vEntityTable = $vEntityTable;
    }
    public function inspectFilter($vField)
    {
        $aInspect = $this->inspect();
        $aFilter = array();
        $aMainTable = $aInspect['Main Table'];
        if (!$aMainTable){
            return 'record not found';
        }
        $aEav = $aInspect['Eav'];
        foreach ($aMainTable as $k => $v) {
            if (strpos($k,$vField)!==false){
                $aFilter['Main Table'][$k] = $v;
            }
        }

        foreach ($aEav as $vStore => $aStoreData) {
            foreach ($aStoreData as $k => $v) {
                if (strpos($k,$vField)!==false){
                    $aFilter['Eav'][$vStore][$k] = $v;
                }
            }
        }
        return $aFilter;

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
        $vTableName = $this->vEntityTable;
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
            $vTable = $this->vEntityTable . "_$vType";
            $aEav =  $this->inspectEavTable($vTable);
            if ($this->bShowTable){
                $aOutput[$vTable] = $aEav;
            }
            else{
                $aOutput= array_merge_recursive($aOutput,$aEav);
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
            return array();
//            throw new \Exception('No Eav attribute found for ' . $this->iProductId);
        }
        $vSql = "SELECT attribute_id,attribute_code FROM eav_attribute WHERE attribute_id IN ($vAttributeList)";
        $aAttributeList = $this->rRead->fetchPairs($vSql);
        $aEavData = array();
        foreach ($aAllRows as $aSingleRow) {
            if (isset($aAttributeList[ $aSingleRow['attribute_id']])){
                $vAttributeCode = $aAttributeList[ $aSingleRow['attribute_id']];
            }
            else{
                $vAttributeCode = 'unknown-attribute-'  . $aSingleRow['attribute_id'];
            }

            //product etc
            if (isset($aSingleRow['store_id'])){
                $iStoreId = (int) $aSingleRow['store_id'];
                //string key is needed so array_merge merges them properly
                $vStringKey = 'store_id_' . $iStoreId;
                if ($this->bShowAttributeId){
                    $aEavData[$vStringKey ][ $aSingleRow['attribute_id'] . '/' .$vAttributeCode] = $aSingleRow['value'];
                }
                else{
                    $aEavData[$vStringKey ][$vAttributeCode] = $aSingleRow['value'];
                }
            }
            //customer etc
            else{
                if ($this->bShowAttributeId){
                    $aEavData[ $aSingleRow['attribute_id'] . '/' .$vAttributeCode] = $aSingleRow['value'];
                }
                else{
                    $aEavData[$vAttributeCode] = $aSingleRow['value'];
                }
            }
        }

        return $aEavData;

    }
}