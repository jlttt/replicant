<?php
require_once(__DIR__ . '/vendor/autoload.php');
if (!isset($app['conf'])) {
    $app['conf'] = loadConf(__DIR__ . '/replicant.json');
}
$app['source'] = getFileSystem($app['conf']['source']);
$app['destination'] = getFileSystem($app['conf']['destination']);
