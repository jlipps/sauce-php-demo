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
    $file_str = file_get_contents($file);
    preg_match_all("/function (test[^\(]+)\(/", $file_str, $matches, PREG_PATTERN_ORDER);
    foreach ($matches[1] as $match) {
        $methods[] = $match;
    }
    return $methods;
}

function chunkTests($tests, $processes)
{
    if ($processes <= 1) {
        return array($tests);
    } else {
        $num_tests = count($tests);
        $tests_per_set = ceil(($num_tests*1.0) / ($processes*1.0));
        $sets = array();
        for ($i=0; $i<$processes; $i++) {
            $sets[$i] = array();
            for ($j=0; $j<$tests_per_set; $j++) {
                if (count($tests))
                    $sets[$i][] = array_pop($tests);
            }
        }
        return $sets;
    }
}

function runTestSets($all_tests, $num_procs)
{
    $outputs = array();
    $active_procs = array();
    $active_pipes = array();
    while (count($all_tests) || count($active_procs)) {
        if (count($active_pipes))
            collectStreamOutput($active_pipes, $outputs);
        if (count($active_procs))
            updateProcessStatus($active_procs, $active_pipes, $outputs);
        if (count($active_procs) < $num_procs && count($all_tests))
            startNewProcess($all_tests, $active_procs, $active_pipes, $outputs);
    }
}

function collectStreamOutput($active_pipes, &$outputs)
{
    $out_streams = array();
    foreach ($active_pipes as $pipes) {
        $out_streams[] = $pipes[1];
    }
    $e = NULL; $f = NULL;
    $num_changed = stream_select($out_streams, $e, $f, 0, 200000);
    if ($num_changed) {
        foreach ($out_streams as $changed_stream) {
            foreach ($active_pipes as $proc_id => $pipes) {
                if ($changed_stream === $pipes[1]) {
                    $outputs[$proc_id] .= stream_get_contents($changed_stream);
                }
            }
        }
    }
}


function updateProcessStatus(&$active_procs, &$active_pipes, $outputs)
{
    foreach ($active_procs as $id => $proc) {
        $status = proc_get_status($proc);
        if (!$status['running']) {
            //echo "Command {$status['command']} finished\n";
            fclose($active_pipes[$id][0]);
            fclose($active_pipes[$id][1]);
            fclose($active_pipes[$id][2]);
            proc_close($proc);
            printImmediateOutput($id, $outputs[$id]);
            unset($active_procs[$id]);
            unset($active_pipes[$id]);
        }
    }
}

function printImmediateOutput($id, $output)
{
    echo $output."\n";
}

function startNewProcess(&$all_tests, &$active_procs, &$active_pipes, &$outputs)
{
    list($proc, $pipes) = getProcessForTest(array_pop($all_tests));
    $proc_id = uniqid();
    $status = proc_get_status($proc);
    //echo "Running {$status['command']}\n";
    $active_procs[$proc_id] = $proc;
    $active_pipes[$proc_id] = $pipes;
    $outputs[$proc_id] = '';
}

function getProcessForTest($test)
{
    $dspec = array(
        0 => array("pipe", "r"),
        1 => array("pipe", "w"),
        2 => array("pipe", "w"),
    );
    list($testName, $testFile) = $test;
    $cmd = "phpunit --filter=$testName $testFile";
    $process = proc_open($cmd,
        $dspec,
        $pipes,
        NULL);
    stream_set_blocking($pipes[1], 0);
    stream_set_blocking($pipes[2], 0);

    return array($process, $pipes);
}


function main($argv)
{
    $usage = "Usage: parallel.php -p[NUM_PROCESSES] --path=TEST_PATH/TEST_GLOB\n";
    if (!isset($argv[1]) || !$argv[1])
        die($usage);

    $opts = getopt("p::", array('path:'));
    if (!isset($opts['path']))
        die($usage);
    if (isset($opts['p']) && intval($opts['p']) > 0)
        $processes = intval($opts['p']);
    else
        $processes = 1;

    $files = getFiles($opts['path'], dirname(__FILE__));

    $all_tests = array();
    foreach($files as $file) {
        $methods = getTestMethodsFromFile($file);
        foreach($methods as $method) {
            $all_tests[] = array($method, $file);
        }
    }

    $test_sets = chunkTests($all_tests, $processes);

    runTestSets($all_tests, $processes);
}

if (isset($argv[0]) && $argv[0])
{
    main($argv);
}
