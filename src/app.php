<?php

use Silex\Application;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\ValidatorServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;
use Dflydev\Provider\DoctrineOrm\DoctrineOrmServiceProvider;
use Silex\Provider\DoctrineServiceProvider;
use Silex\Provider\SessionServiceProvider;

$app = new Application();

$app->register(new Performance\DomainServiceProvider());
$app->register(new ValidatorServiceProvider());
$app->register(new TwigServiceProvider());
$app->register(new SessionServiceProvider());
$app->register(new ServiceControllerServiceProvider());
$app->register(new DoctrineServiceProvider);
$app->register(new DoctrineOrmServiceProvider);
$app->register(new Predis\Silex\ClientsServiceProvider(), [
        'predis.clients' => [
            'redis-master' => [
                'host' => '127.0.0.1',
                'port' => 6379
            ],
            'redis-slave1' => [
                'host' => '',
                'port' => 6379,
            ],
            'redis-slave2' => [
                'host' => '',
                'port' => 6379
            ],
        ]
]);

return $app;
