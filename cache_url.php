<?php
namespace ZainPrePend\CacheUrl;
use ZainPrePend\lib;
return;
function getFileNameFromUrlKey($urlKey)
{
    if ($urlKey == '/') {
        $urlKey = '/index';
    }
    $urlKey = ltrim($urlKey, '/');
    $urlKey = str_replace('/', '-', $urlKey);
    $urlKey = '/' . $urlKey;
    $file = __DIR__ . '/cache_url' . $urlKey . '.html';
    return $file;
}

function getContentsFileName($urlKey)
{
    $file = getFileNameFromUrlKey($urlKey);
    if (file_exists($file)) {
        return file_get_contents($file);
    }
    return false;
}

if (!isset($urlList)){
    $urlList = array('/',
                     '/checkout/onepage/index',
                     '/checkout/onepage/success',

    );
}

function showUrlContents($urlList)
{
    if (empty($_SERVER["REQUEST_URI"])) {
        return false;
    }
    $request = $_SERVER["REQUEST_URI"];
    $request = rtrim($request, '/');
    $checkContents = false;
    if (in_array($request, $urlList)) {
        $checkContents = true;
    }
    else {
        $request = $request . '/index';
        $checkContents = in_array($request, $urlList);
    }
    if ($checkContents) {
        $contents = getContentsFileName($request);
        if ($contents) {
            preg_match("/<body[^>]*>/", $contents, $matches);
            if ($matches) {
//                \ZainPrePend\lib\T::printr($matches);
//                die;
                $bodyPreFix = "<span
style ='font-size:22px ; position:absolute; left:0;top:0;z-index:99999; color:green;'>
This content is loaded from File:" . __FILE__ . " line:" . __LINE__ . " custom cache</span>";
                $contents = str_replace($matches[0], $matches[0] . $bodyPreFix, $contents);
            }
            header('Pragma: no-cache');
            header('Cache-Control: private, no-cache, no-store, max-age=0, must-revalidate, proxy-revalidate');
            header('Expires: Tue, 04 Sep 2012 05:32:29 GMT');
            echo $contents;
            die;
        }
    }
}

function putUrlKeyContents($urlKey)
{
    \ZainPrePend\MageInclude\includeMage();
    $baseUrl = \Mage::getBaseUrl();
    $baseUrl = rtrim($baseUrl, '/');
    $url = $baseUrl . $urlKey;
    $contents = getContentsFromUrl($url);
    if ($contents && ($fileName = getFileNameFromUrlKey($urlKey))) {
        file_put_contents($fileName, $contents);
    }

}

function getContentsFromUrl($url)
{
    echo "<br/> getting url $url  File:" . __FILE__ . " line:" . __LINE__ . "<br/>\r\n";
    $contents = file_get_contents($url);
//    echo $contents;
    return $contents;
}

function putAllContents($urlList)
{
    foreach ($urlList as $urlKey) {
        putUrlKeyContents($urlKey);
    }
}

if (isset($argv) && isset($argv[0]) && (strpos($argv[0], 'zain_custom') !== false)) {
    putAllContents($urlList);
    die;
}
//avoid loop by get url
if (isset($_SERVER['HTTP_USER_AGENT'])) {
    showUrlContents($urlList);
}