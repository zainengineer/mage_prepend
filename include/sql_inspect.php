<?php
namespace ZainPrePend\MageInclude;

class SqlInspect
{
    protected $vSql;
    protected $aRows;
    protected $rRead;
    /** @var \Mage_Core_Model_Resource */
    protected $rCoreResource;
    protected $bColumnOnly = false;
    protected $bKeyValue;
    /**
     * @var
     * customer_entity
     * catalog_product_entity
     */
    protected $vEntityTable;

    public function __construct($vSql, $limit)
    {
        if ($limit && !strpos($vSql, ' limit ') && (stripos($vSql, 'select') === 0)) {
            $vSql = rtrim($vSql, ';');
            $vSql .= " limit $limit;";
        }
        $this->vSql = $vSql;

        $this->rCoreResource = \Mage::getSingleton('core/resource');
        $this->rRead = $this->rCoreResource->getConnection('core_read');
    }

    public function getQueryRow()
    {
        if (is_null($this->aRows)) {
            $aRows = $this->rRead->fetchAssoc($this->vSql);
            if (!$aRows) {
                $this->aRows = array();
            }
            else {
                // one column only
                if ($this->bColumnOnly) {
                    foreach ($aRows as $aSingleRow) {
                        $this->aRows[] = current($aSingleRow);
                    }
                }
                //key value pair
                elseif ($this->bKeyValue && isset($this->bKeyValue[0][1])) {
                    foreach ($aRows as $aSingleRow) {
                        $this->aRows[$aSingleRow[0]] = current($aSingleRow[1]);
                    }
                }
                //normal query
                else {
                    //only one record
                    if (count($aRows) == 1) {
                        $this->aRows = current($aRows);
                    }
                    //normal case
                    else {
                        $this->aRows = $aRows;
                    }
                }
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
        if (function_exists('yaml_emit')) {
            $vOutput = yaml_emit($aRow);
        }
        else {
            $vOutput = json_encode($aRow, JSON_PRETTY_PRINT);
        }

        if ($bDisplaySql) {
            $vOutput = $this->vSql . "\n\n" . $vOutput;
        }
        return $vOutput;
    }

    public function setColumnOnlyValue($bValue)
    {
        $this->bColumnOnly = $bValue;
        return $this;
    }

    public function setKeyValuePairValue($bValue)
    {
        $this->bKeyValue = $bValue;
        return $this;
    }

    public static function embed($vSql, $iLimit = 5)
    {
        /**
         * for some reason
         * ZainPrePend\MageInclude\SqlInspect::embed('select * from catalog_product_entity');
         * is not working in Watches
         * it goes circular in eval for no reason
         * even after restart of machine restart try
         *
         */
        try {
            if (!function_exists('mageCoreErrorHandler')) {
                return "Mage not initialized";
            }
            if (!@class_exists('\Mage', false)) {
                return 'Mage not initialized';
            }
            $oObject = new self($vSql, $iLimit);
            return $oObject->getQueryRow();
        } catch (\Exception $e) {
            return $e->getMessage();
        }

    }
}