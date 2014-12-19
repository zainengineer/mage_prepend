<?php
namespace ZainPrePend\Session;
if (!empty($_GET['session_destroy'])){
    if (!session_id()){
        session_start();
    }
    session_destroy();
    $params = session_get_cookie_params();
    setcookie(session_name(), '', 0, $params['path'], $params['domain'], $params['secure'], isset($params['httponly']));
    $cookiNameList= array();
    $domainList = array();
    if (isset($_SERVER["HTTP_HOST"])){
        $primaryDomain = $_SERVER["HTTP_HOST"];
        $firstDomain = ltrim($primaryDomain,'.');
        $domainParts =  explode('.',$firstDomain);
        unset($domainParts[0]);
        $parentDomain = implode('.',$domainParts);

        $domainList[] = $firstDomain;
        $domainList[] = '.' . $firstDomain;
        $domainList[] = $parentDomain;
        $domainList[] = '.' . $parentDomain;

    }
    if (isset($_SERVER['HTTP_COOKIE'])) {
        $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
        foreach($cookies as $cookie) {
            $parts = explode('=', $cookie);
            $name = trim($parts[0]);
            if ($name=='XDEBUG_SESSION'){
                continue;
            }
            $cookiNameList[] = $name;
            setcookie($name, '', time()-1000);
            setcookie($name, '', time()-1000, '/');
            foreach ($domainList as $domain) {
                setcookie($name, '', time()-1000,null,$domain);
                setcookie($name, '', time()-1000, '/',$domain);
            }

        }
    }
    $contents =  ob_get_contents();
    if ($contents){
        echo "<br/> has contents  File:" . __FILE__ . " line:" . __LINE__ . "<br/>\r\n";
    }
    if (empty($cookies)){
        echo "<br/> no cookies found  File:" . __FILE__ . " line:" . __LINE__ . "<br/>\r\n";
    }
    else{
        \ZainPrePend\lib\T::printr($cookies,'cookies');
        \ZainPrePend\lib\T::printr($cookiNameList,'cookie names');
        \ZainPrePend\lib\T::printr($domainList,'domain list');
    }
    echo "<br/> destroyed your session  File:" . __FILE__ . " line:" . __LINE__ . "<br/>\r\n";
    die;
}

