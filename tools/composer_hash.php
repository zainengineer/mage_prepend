<?php
$vRootDir = dirname(dirname(dirname(__FILE__)));
$vPath = $vRootDir . '/composer.json';
if (!file_exists($vPath)){
    echo "$vPath not found\n";
    return;
}
$vContents = file_get_contents($vPath);
if (!trim($vContents)){
    echo "no contents found in $vPath\n";
    return;
}
if (!json_decode($vContents)){
    echo "Cound not decode $vPath\n";
    return;
}

class Helper {
    //https://github.com/composer/composer/blob/122e422682d961233cc5db8b2102cd98b049d4f9/src/Composer/Package/Locker.php#L72
    public static function getContentHash($composerFileContents)
    {
        $content = json_decode($composerFileContents, true);
        $relevantKeys = array(
            'name',
            'version',
            'require',
            'require-dev',
            'conflict',
            'replace',
            'provide',
            'minimum-stability',
            'prefer-stable',
            'repositories',
            'extra',
        );
        $relevantContent = array();
        foreach (array_intersect($relevantKeys, array_keys($content)) as $key) {
            $relevantContent[$key] = $content[$key];
        }
        if (isset($content['config']['platform'])) {
            $relevantContent['config']['platform'] = $content['config']['platform'];
        }
        ksort($relevantContent);
        return md5(json_encode($relevantContent));
    }
}

echo Helper::getContentHash($vContents) . "\n";