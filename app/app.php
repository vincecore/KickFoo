<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Debug\Debug;

$env = getenv('SYMFONY__ENV') ?: 'prod';
$debug = getenv('SYMFONY__DEBUG') === '1';

$loader = require_once __DIR__.'/../app/autoload.php';
include_once __DIR__.'/../var/bootstrap.php.cache';

if ($debug) {
    Debug::enable();
}

require_once __DIR__ . '/../app/AppKernel.php';
require_once __DIR__.'/../app/AppCache.php';

$kernel = new AppKernel($env, $debug);
$kernel->loadClassCache();

//if ($debug == false) {
    //$kernel = new AppCache($kernel);
//}

$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
