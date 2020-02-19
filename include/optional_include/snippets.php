<?php

function phpPaths($snippetsPath,$prefix='')
{
    $it = new RecursiveTreeIterator(new RecursiveDirectoryIterator($snippetsPath,
        RecursiveDirectoryIterator::SKIP_DOTS + RecursiveDirectoryIterator::UNIX_PATHS));
    $aPhpPath = [];
    foreach ($it as $path) {
        $split = explode($snippetsPath . '/', $path);
        $fileName = $split[1];
        $extension = substr($fileName, -4);
        if ($extension == '.php') {
            $firstFileNameChars = substr(basename($fileName),0,2);
            if ($firstFileNameChars=='__'){
                continue;
            }
            $aPhpPath[$fileName] = $prefix . ltrim(pathinfo($fileName, PATHINFO_DIRNAME) . '/' . pathinfo($fileName, PATHINFO_FILENAME),'./');
        }
    }
    return $aPhpPath;
}
function pathsToLink($aPhpPath)
{
    $aLinks = [];
    foreach ($aPhpPath as $fileName => $opName) {
        $url = "<a href='/?zop=$opName'>$opName</a>";
        $aLinks[] = $url;
    }
    return $aLinks;
}

function linkToString($aLinks)
{
    return implode("\n<br/>",$aLinks);
}
function getAll($snippetsPath,$prefix = '')
{
    $aPhpPath = phpPaths($snippetsPath,$prefix);
    $aLinks = pathsToLink($aPhpPath);
    $vLinks = linkToString($aLinks);
    return $vLinks;
}
echo "project snippets<br/><br/>";

$targetPath = AUTO_PREPEND_BASE_PATH_Z . "/project_custom/snippets";
echo getAll($targetPath);


d(1);