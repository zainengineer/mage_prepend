<?php
if (!isset($_GET['op']) || ($_GET['op'] != 'raw')) {
    return;
}

$bShowNext = false;

$bShowNext = true;
if ($bShowNext) {
    $vDate = '2017-06-27T12:30:26+00:00';
//    $iTime - strtotime('2017-06-27T12:30:26+00:00');
    $dt = new DateTime($vDate,new DateTimeZone('Australia/Sydney'));
    \ZainPrePend\lib\T::printr($dt->format('c'),true,'');
}
//$bShowNext = true;
if ($bShowNext) {
    $bShowNext = false;
    $vOrderDate = '14-Jun-2017 11:57:17 PM';
//    date_default_timezone_set('Australia/Sydney');
//    $iStamp = strtotime($vOrderDate);
    $tzSydney = new DateTimeZone('Australia/Sydney');
    $tzUTC = new DateTimeZone('UTC');
    $date = new DateTime($vOrderDate, $tzSydney);
    $date->setTimezone($tzUTC);
    $output = $date->format('c');
//    date_default_timezone_set('UTC');
//    $date = new DateTime(date('c',$iStamp));
//    $output = $date->getTimezone()->getName();
//    $output = date('c',$iStamp);
    \ZainPrePend\lib\T::printr($output, true, '');
}

\ZainPrePend\lib\T::printr(1, true, 'finished');
die;