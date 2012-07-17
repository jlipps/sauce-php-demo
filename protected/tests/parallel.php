<?php

function getFiles($glob, $base_dir)
{
    $test_files = array();
    $files = glob(joinPaths($base_dir, $glob), GLOB_MARK);
    foreach ($files as $file) {
        if ($file != '.' && $file != '..') {
            if ($file[strlen($file)-1] === '/') {
                $test_files = array_merge($test_files, getFiles('*', $file));
            } else {
                $test_files[] = $file;
            }
        }
    }
    return $test_files;
}

function joinPaths() {
    $paths = array_filter(func_get_args());
    return preg_replace('#/{2,}#', '/', implode('/', $paths));
}


function getTestMethodsFromFile($file)
{
    $methods = array();

}


function main($argv)
{
    $usage = "Usage: parallel.php TEST_PATH/TEST_GLOB\n";
    if (!isset($argv[1]) || !$argv[1])
        die($usage);

    $files = getFiles($argv[1], dirname(__FILE__));

    foreach($files as $file) {
        $methods = getTestMethodsFromFile($file);
        foreach($methods as $method) {
            $all_tests = array($method => $file);
        }
    }
}

if (isset($argv[0]) && $argv[0])
{
    main($argv);
}
