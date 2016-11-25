<?php
namespace ZainPrePend\Code;

Class CodePool
{
    static $instance;
    public function __construct()
    {
        $this->enableCustomCodePool();
        $this->copyFiles();
    }
    protected function enableCustomCodePool()
    {
        $vCustomPath = $this->getCustomCodePoolPath();
        $GLOBALS['paths'] = array(
            $vCustomPath,
        );
//        $vMagentoIncludePath =  get_include_path();
//        set_include_path($vCustomPath . PS . $vMagentoIncludePath);

    }
    protected function getCustomCodePoolPath()
    {
        return dirname(dirname(__FILE__)) . '/custom_code_pool';
    }
    protected function copyFiles()
    {
        $vOriginal = 'app/code/core/Mage/Core/Controller/Varien/Action.php';
        $vPoolPath = 'Mage/Core/Controller/Varien/Action.php';
        $vTargetPoolPath = 'Mage/Core/Controller/Varien/ActionZCustom.php';
        $vClassOriginalName = 'Mage_Core_Controller_Varien_Action';
        $vClassOriginalCustomName = 'Mage_Core_Controller_Varien_ActionZCustom';
        $vTargetPath = $this->getCustomCodePoolPath() . "/$vTargetPoolPath";
        if (true || !file_exists($vTargetPath)){
            if (!is_dir(dirname($vTargetPath))){
                $bFolderCreated = mkdir(dirname($vTargetPath),0777,true);
            }
            $vContents = file_get_contents($vOriginal);
            $vContents = str_replace($vClassOriginalName,$vClassOriginalCustomName ,$vContents);
            $bFileWritten = file_put_contents($vTargetPath, $vContents);
        }
    }

    /**
     * @return CodePool
     */
    public static function getInstance()
    {
        if (is_null(self::$instance)){
            self::$instance = new CodePool();
        }
        return self::$instance;
    }
}