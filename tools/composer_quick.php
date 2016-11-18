<?
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($argv[1])){
    echo "Usage is: zain_custom/tools/composer_quick.php aijko/aijko_widgetimagechooser git@github.com:aligent/aijko-widgetimagechooser.git no-backup";
    exit;
}
$aBasePath = [
    getcwd(),
    dirname(dirname(dirname(__FILE__))),
];
$bFound = false;
foreach ($aBasePath as $vBasePath) {
    $vComposerPath = $vBasePath . '/composer.json';
    if (file_exists($vComposerPath)) {
        $bFound = true;
        break;
    }
}
if (!$bFound || empty($vComposerPath)) {
    throw new Exception('cannot find composer.json');
}
if (!is_writable($vComposerPath)){
    return false;
}
$vRequirement = $argv[1];
$vRepositoryUrl = $argv[2];
$vJson = file_get_contents($vComposerPath);
$aJson = json_decode($vJson, true);
$aJson['repositories']  = [
//    [
//        'type' => 'vcs',
//        'url' => $vRepositoryUrl,
//    ],
    [
        'packagist.org' => false
    ],
    [
        'packagist' => false
    ]

];
$vComposerBackup = str_replace('composer.json', 'composer.json.bak', $vComposerPath);
if (file_exists($vComposerBackup) &&
    !(isset($argv[3]) && ($argv[3] = 'no-backup'))){
    throw new Exception('backup already exists: '. $vComposerBackup);
}
if (!file_exists($vComposerBackup)){
    rename($vComposerPath, $vComposerBackup);
}
$vContents = json_encode($aJson,JSON_PRETTY_PRINT);
$return = file_put_contents($vComposerPath, $vContents );
$vChangedContents = file_get_contents($vComposerPath);
if ($vChangedContents !== $vContents){
    throw new Exception('contents not written to ' . $vComposerPath);
}
$vOutput = shell_exec('composer update ' . $vRequirement);
rename($vComposerBackup,$vComposerPath);
echo $vOutput . "\n";

