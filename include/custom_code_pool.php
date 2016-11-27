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
    public function injectPath()
    {
        
    }
    protected function enableCustomCodePool()
    {
        //works only with some projects where $paths does not start with array

//        $vCustomPath = $this->getCustomCodePoolPath();
//        $GLOBALS['paths'] = array(
//            $vCustomPath,
//        );
    define('COMPILER_INCLUDE_PATH', dirname(__FILE__). '/compiled_path');

    }
    public function getCustomCodePoolPath()
    {
        return dirname(dirname(__FILE__)) . '/custom_code_pool';
    }
    protected function copyFiles()
    {
        $vOriginalPath = 'app/code/core/Mage/Core/Controller/Varien/Action.php';
        $vPoolPath = 'Mage/Core/Controller/Varien/Action.php';
        $vTargetPoolPath = 'Mage/Core/Controller/Varien/ActionZCustom.php';
        $vClassOriginalName = 'Mage_Core_Controller_Varien_Action';
        $this->modifyTarget($vClassOriginalName, $vTargetPoolPath, $vOriginalPath);

        $vAutoloadTarget = dirname(__FILE__) . '/compiled_path/Varien_AutoloadZCustom.php';
        $this->modifyTarget('Varien_Autoload', $vAutoloadTarget, 'lib/Varien/Autoload.php');

    }
    protected function modifyTarget($vClassOriginalName, $vTargetPoolPath, $vOriginalPath)
    {
        $vClassOriginalCustomName = $vClassOriginalName . 'ZCustom';
        if (substr($vTargetPoolPath,0,1) == '/'){
            $vTargetPath = $vTargetPoolPath;
        }
        else{
            $vTargetPath = $this->getCustomCodePoolPath() . "/$vTargetPoolPath";
        }

        if (true || !file_exists($vTargetPath)){
            if (!is_dir(dirname($vTargetPath))){
                $bFolderCreated = mkdir(dirname($vTargetPath),0777,true);
            }
            $vContents = file_get_contents($vOriginalPath);
            $vContents = str_replace('class ' .$vClassOriginalName,'class ' . $vClassOriginalCustomName ,$vContents);
            $bFileWritten = file_put_contents($vTargetPath, $vContents);
            if ($bFileWritten){
                chmod($vTargetPath, 0777);
            }else{
                $vWritePathCompile = AUTO_PREPEND_BASE_PATH_Z . '/include/compiled_path';
                $rWritePathCodePool = AUTO_PREPEND_BASE_PATH_Z . '/custom_code_pool/';
                $vCommand = "chmod a+w $vWritePathCompile -R && chmod a+w $rWritePathCodePool -R ";
                throw new \Exception('cannot write to ' . $vTargetPath . " try \n<br/> $vCommand and \n<br/>");
            }
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