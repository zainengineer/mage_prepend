<?
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($argv[1])){
    echo "Usage is: zain_custom/tools/composer_quick.php aijko/aijko_widgetimagechooser git@github.com:aligent/aijko-widgetimagechooser.git\n";
    echo "OR\n";
    echo "(NOT WORKING) xdebug local.dps.com zain_custom/tools/composer_quick.php  connect20/dynamic_creative package\n";
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
$aOriginalRepositories = $aJson['repositories'];
$aJson['repositories']  = [
    [
        'packagist.org' => false
    ],
    [
        'packagist' => false
    ]

];
$bRepositoryAdded = false;
if ($vRepositoryUrl === 'package'){
    foreach ($aOriginalRepositories as $aRepositoryData) {
        if (isset($aRepositoryData['package'])){
            $aPackage = $aRepositoryData['package'];
            if (!empty($aPackage['name']) && $aPackage['name']==$vRequirement){
                $aJson['repositories'][] = $aPackage;
                $bRepositoryAdded = true;
                break;
            }
        }
    }
    if ($bRepositoryAdded){
        $aJson['repositories'][] = [
            'type' => 'vcs',
            'url' => 'git@github.com:Cotya/magento-composer-installer.git',
        ];
    }
    else{
        throw new Exception('no package repository found with name ' . $vRequirement);
    }
}
else {
    $aJson['repositories'][] = [
        'type' => 'vcs',
        'url'  => $vRepositoryUrl,
    ];
}

$vContents = json_encode($aJson,JSON_PRETTY_PRINT + JSON_UNESCAPED_SLASHES);

$vComposerBackup = str_replace('composer.json', 'composer.json.bak', $vComposerPath);
if (file_exists($vComposerBackup) &&
    !(isset($argv[3]))){
    throw new Exception('backup already exists: '. $vComposerBackup);
}
else{
    if (!file_exists($vComposerBackup)){
        rename($vComposerPath, $vComposerBackup);
    }
}

$return = file_put_contents($vComposerPath, $vContents );
$vChangedContents = file_get_contents($vComposerPath);
if ($vChangedContents !== $vContents){
    throw new Exception('contents not written to ' . $vComposerPath);
}
$vCommand = 'composer update -vvv ' . $vRequirement;
echo "Executing: $vCommand";
$vOutput = shell_exec($vCommand);
rename($vComposerBackup,$vComposerPath);
echo $vOutput . "\n";

