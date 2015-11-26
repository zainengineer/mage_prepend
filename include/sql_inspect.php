<?php
namespace ZainPrePend\MageInclude;

class SqlInspect
{
    protected $vSql;
    protected $aRows;
    protected $rRead;
    /** @var \Mage_Core_Model_Resource  */
    protected $rCoreResource;
    /**
     * @var
     * customer_entity
     * catalog_product_entity
     */
    protected $vEntityTable;
    public function __construct($vSql, $limit)
    {
        if ($limit && !strpos($vSql,' limit ') && (stripos($vSql,'select')===0)){
            $vSql = rtrim($vSql,';');
            $vSql .= " limit $limit;";
        }
        $this->vSql = $vSql;

        $this->rCoreResource = \Mage::getSingleton('core/resource');
        $this->rRead = $this->rCoreResource->getConnection('core_read');
    }

    protected function getQueryRow()
    {
        if (is_null($this->aRows)){
            $this->aRows = $this->rRead->fetchAssoc($this->vSql);
            if (count($this->aRows) ==1){
                $this->aRows = current($this->aRows);
            }
        }
        return $this->aRows;
    }
    public function getJson()
    {
        $aRow = $this->getQueryRow();
        $vJson = \Zend_Json::encode($aRow);
        return \Zend_Json::prettyPrint($vJson);
    }
    public function getYaml($bDisplaySql)
    {
        $aRow = $this->getQueryRow();
        $vOutput =  yaml_emit($aRow);
        if ($bDisplaySql){
            $vOutput = $this->vSql . "\n\n" . $vOutput;
        }
        return $vOutput;
    }
}