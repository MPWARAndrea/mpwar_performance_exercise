<?php

use Silex\Provider;

// include the prod configuration
require __DIR__ . '/prod.php';

// enable the debug mode
$app['debug'] = true;

$app->register(new Provider\HttpFragmentServiceProvider());
$app->register(new Provider\WebProfilerServiceProvider());
$app->register(new Sorien\Provider\DoctrineProfilerServiceProvider());

$app['profiler.cache_dir'] 			= '/tmp/cache/profiler';
$app['profiler.mount_prefix'] 		= '/_profiler';