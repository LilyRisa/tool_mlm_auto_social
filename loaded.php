<?php
require_once 'vendor/autoload.php';

define('PRESET', __DIR__.'/preset');

session_start();

$common = 'common/';
$services = 'services/';
$files_common = glob($common . '*');
$files_services = glob($services . '*');
$files = array_merge($files_common, $files_services);

foreach ($files as $file) {
    if (is_file($file)) {
        require $file;
    }
}
