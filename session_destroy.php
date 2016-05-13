<?php
namespace ZainPrePend\Session;
function SessionDestroyOnRequest()
{
    if (!empty($_GET['session_destroy'])) {
        if (!session_id()) {
            session_start();
        }
        session_destroy();
        $params = session_get_cookie_params();
        setcookie(session_name(), '', 0, $params['path'], $params['domain'], $params['secure'], isset($params['httponly']));
        $cookiNameList = array();
        $domainList = getDomainList();
        if (isset($_SERVER['HTTP_COOKIE'])) {
            $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
            foreach ($cookies as $cookie) {
                $parts = explode('=', $cookie);
                $name = trim($parts[0]);
                if ($name == 'XDEBUG_SESSION') {
                    continue;
                }
                $cookiNameList[] = $name;
                deleteCookie($name);
            }
        }
        $contents = ob_get_contents();
        if ($contents) {
            echo "<br/> has contents  File:" . __FILE__ . " line:" . __LINE__ . "<br/>\r\n";
        }
        if (empty($cookies)) {
            echo "<br/> no cookies found  File:" . __FILE__ . " line:" . __LINE__ . "<br/>\r\n";
        }
        else {
            \ZainPrePend\lib\T::printr($cookies, 'cookies');
            \ZainPrePend\lib\T::printr($cookiNameList, 'cookie names');
            \ZainPrePend\lib\T::printr($domainList, 'domain list');
        }
        echo "<br/> destroyed your session  File:" . __FILE__ . " line:" . __LINE__ . "<br/>\r\n";
        die;

    }
}
function SessionInfoDestroyInline()
{
    $aRemoveCookie = array('frontend');
    foreach ($_COOKIE as $vKey => $vValue) {
        if (in_array($vKey,$aRemoveCookie)){
            unset($_COOKIE[$vKey]);
            deleteCookie($vKey);
        }
    }
    $aCookie = explode(';', $_SERVER['HTTP_COOKIE']);
    $aKeepCookie = array();
    foreach ($aCookie as $vCookie) {
        $aParts = explode('=', $vCookie);
        $vName = trim($aParts[0]);
        if (!in_array($vName,$aRemoveCookie)) {
            $aKeepCookie[] = $vCookie;
        }
    }
    $vKeepCookie = implode(';',$aKeepCookie);
    $_SERVER['HTTP_COOKIE'] = $vKeepCookie;
}
function deleteCookie($vCookieName)
{
    setcookie($vCookieName, '', time() - 1000);
    setcookie($vCookieName, '', time() - 1000, '/');
    $aDomainList = getDomainList();
    foreach ($aDomainList as $vDomain) {
        setcookie($vCookieName, '', time() - 1000, null, $vDomain);
        setcookie($vCookieName, '', time() - 1000, '/', $vDomain);
        setcookie($vCookieName, '', time() - 1000, null, '.' . $vDomain);
        setcookie($vCookieName, '', time() - 1000, '/', '.' . $vDomain);
    }
}
function getDomainList()
{
    static $aDomainList;
    if (is_null($aDomainList)){
        $aDomainList = array();
        if (isset($_SERVER["HTTP_HOST"])) {
            $primaryDomain = $_SERVER["HTTP_HOST"];
            $firstDomain = ltrim($primaryDomain, '.');
            $domainParts = explode('.', $firstDomain);
            unset($domainParts[0]);
            $parentDomain = implode('.', $domainParts);

            $aDomainList[] = $firstDomain;
            $aDomainList[] = '.' . $firstDomain;
            $aDomainList[] = $parentDomain;
            $aDomainList[] = '.' . $parentDomain;
        }
    }
    return $aDomainList;
}
SessionDestroyOnRequest();
