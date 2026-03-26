<?php

$compiledPath = rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR)
    .DIRECTORY_SEPARATOR.'clinic-test-views'
    .DIRECTORY_SEPARATOR.'p'.getmypid()
    .DIRECTORY_SEPARATOR.bin2hex(random_bytes(6));

if (! is_dir($compiledPath)) {
    mkdir($compiledPath, 0777, true);
}

putenv('VIEW_COMPILED_PATH='.$compiledPath);
$_ENV['VIEW_COMPILED_PATH'] = $compiledPath;
$_SERVER['VIEW_COMPILED_PATH'] = $compiledPath;

require __DIR__.'/../vendor/autoload.php';
