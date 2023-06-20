<?php
require_once 'vendor/autoload.php';

$_data_env = file_get_contents(__DIR__.'/.env');
$_data_env = explode(PHP_EOL, $_data_env);

foreach($_data_env as $env){
    if($env)
    putenv($env);
}

define('PRESET', __DIR__.'/preset');


require 'common/functional.php';
require 'common/facebook.php';
require 'common/instagram.php';
require 'common/youtubeshort.php';
require 'services/convert.php';

