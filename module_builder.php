<?php


$shortopts = "";
$shortopts .= "n:";  // name search module
$shortopts .= "z::";  // Display result = 1 or 0

$longopts = array(
    "name:",     // Обязательное значение
    "zip::",    // Display result = 1 or 0
);
$options = getopt($shortopts, $longopts);

/**
 * moduleName
 */

$moduleName = false;
if ((isset($options['n'])) || (isset($options['name']))) {
    $moduleName = $options['n'] ?: $options['name'];
} else {
    echo "\033[31m Name Error \033[0m -n or --name  \n";
    return;
}


/**
 * Display
 */
$display = 0;

if ((isset($options['z'])) || (isset($options['zip']))) {
    $display = $options['z'] ?: $options['zip'];
}

/***
 *
 */

$path = [];

$iterator = new RecursiveDirectoryIterator("../");
foreach (new RecursiveIteratorIterator($iterator) as $file) {

    if ($file->isDir()) {

    }
    if ($file->isFile()) {

        if (strpos($file->getFilename(), $moduleName) === 0) {
            $path[] = $file->getPathname();
        }


    }
}

/**
 * @param $path
 * @return array|string|string[]
 */
function clearPath($path)
{
    return str_replace(sprintf('../%s/',basename(__DIR__)), '', $path);
}



/**
 * @param $arrayPath
 * @param $name
 * @return bool
 */
function Zip($arrayPath, $name)
{
    $zip = new ZipArchive();
    if ($zip->open($name, ZipArchive::CREATE | ZipArchive::OVERWRITE)) {
        foreach ($arrayPath as $file) {
            $zip->addfile($file, clearPath($file));
        }
        $zip->close();
        return true;
    } else {
        return false;
    }
}

/**
 * Run
 */

if (count($path) == 0 ) return colorLog(sprintf('%s - not found',$moduleName),'i');

if ($display) {
    $zipFileName = sprintf('%s.zip', $moduleName);
    if (Zip($path, $zipFileName)) {
        echo colorLog($zipFileName . " \033[0m Created zip  \n",'s');
    }else{
        echo colorLog($zipFileName . " \033[0m The file has not been created \n",'e');
    }

} else {

    foreach ($path as $file){

        echo colorLog($file,'i');
    }
}


/**
 * color logs
 */

function colorLog($str, $type = 'i'){
    switch ($type) {
        case 'e': //error
            echo "\033[31m$str \033[0m\n";
            break;
        case 's': //success
            echo "\033[32m$str \033[0m\n";
            break;
        case 'w': //warning
            echo "\033[33m$str \033[0m\n";
            break;
        case 'i': //info
            echo "\033[36m$str \033[0m\n";
            break;
        default:
            # code...
            break;
    }
}