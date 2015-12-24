<?php
if (!isset($_GET['op']) || ($_GET['op'] != 'filter_git')) {
    return;
}

/***
 * Class GitFilterCustom
 * Generate stat using
 * git diff --stat=1000 ...
 * otherwise git will truncate name
 */
Class GitFilterCustom
{
    public $vFilePath;
    public $vContents;
    public $aAllLines = array();
    public $aFilteredLines = array();
    public $aFilteredPath = array();
    public $vParams = '';
    public $vLastFullPath = '';

    public function __construct()
    {
        $this->vFilePath = dirname(dirname(dirname(__FILE__))) . '/test.txt';
        $this->vContents = file_get_contents($this->vFilePath);
    }

    public function getAllLines()
    {
        $this->aAllLines = explode("\n", $this->vContents);
        return $this->aAllLines;
    }

    function considerThisLine($vLine)
    {
        $aParts = explode("|", $vLine);
        if (count($aParts) != '2') {
            return false;
        }
        $vFile = trim($aParts[0]);
        $vStat = trim($aParts[1]);
        $aParts = pathinfo($vFile);
        $vExtension = $aParts['extension'];
        if (strpos($vFile,'lib/Zend')===0){
            return false;
        }
        if (strpos($vFile,'js/')===0){
            return false;
        }
        if (strpos($vFile,'skin/')===0){
            return false;
        }
        if ($vExtension == '.xml') {
            $this->aFilteredPath[] = $vFile;
            return true;
        }
        $aIgnore = array(
            '16 +-', //change in  doc block at top
            '2 +-', //one line modification in some stat
        );
        if (in_array($vStat, $aIgnore)) {
            return false;
        }
        $this->aFilteredPath[] = $vFile;
        return true;
    }

    public function process()
    {
        $this->getAllLines();
        $this->aFilteredLines = array_filter($this->aAllLines, array($this, "considerThisLine"));
        $this->vParams = implode(' ', $this->aFilteredPath);
    }
}

$oFilter = new GitFilterCustom();
$oFilter->process();
echo $oFilter->vParams;
\ZainPrePend\lib\T::printr(1,true,'');
die;