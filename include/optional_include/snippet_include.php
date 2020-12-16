<?php
namespace ZainCustom\Snippets\Includes;
Class AutoInclude
{
    protected $programmaticSnippetPath;
    protected static $instance;
    public function __construct()
    {
        self::$instance = $this;
    }
    public static function getInstance()
    {
        return self::$instance;
    }
    protected function getSnippetName()
    {
        return $this->programmaticSnippetPath ?: $_GET['zop'];
    }

    protected function getSnippetPath()
    {
        static $fullPath;
        if (!$fullPath){
            $vSnippetName = $this->getSnippetName();
            $fullPath = AUTO_PREPEND_BASE_PATH_Z . "/project_custom/snippets/{$vSnippetName}.php";
        }
        return $fullPath;
    }

    protected function getSnippetNameIfCustomMissing()
    {
        $path = $this->getSnippetPath();
        return file_exists($path) ? false : $this->getSnippetName();
    }

    protected function checkSnippet()
    {
        $path = $this->getSnippetPath();
        if (!file_exists($path)) {
            !d("$path does not exist");
//            throw new Exception(($path) . ' does not exist');
            require_once __DIR__ . '/snippets.php';
            die;
        }
    }





    public function processSnippet()
    {

        $this->checkSnippet();
        $this->includeMagentoSnippet();

    }

    public function initializeMagento()
    {
        $path = $this->getSnippetPath();
        if (strpos($path,'pre_')===0){
            //do nothing
        }
        else{
            require_once AUTO_PREPEND_BASE_PATH_Z . '/include/mage_include.php';
            \ZainPrePend\MageInclude\includeMage();
        }
    }
    protected function includeMagentoSnippet()
    {
        $path = $this->getSnippetPath();
        require_once __DIR__ . '/action_detect.php';
        require_once $path;
    }
}

$autoInclude = new AutoInclude();

$autoInclude->processSnippet();
die;