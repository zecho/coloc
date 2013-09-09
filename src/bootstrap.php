<?php

use \Pomm\Silex\PommServiceProvider;
use \Silex\Provider\TwigServiceProvider;
use \Silex\Provider\SessionServiceProvider;
use \Silex\Provider\SecurityServiceProvider;
use \Silex\Provider\WebProfilerServiceProvider;
use \Silex\Provider\UrlGeneratorServiceProvider;
use \Silex\Provider\ServiceControllerServiceProvider;

require_once __DIR__ . '/../vendor/autoload.php';

if (!is_file(__DIR__ . '/config/current.php')) {
    throw new \RunTimeException('No current configuration file found in config.');
}

$app = new Silex\Application();

$app['config'] = require __DIR__ . '/config/current.php';

$app['debug'] = $app['config']['debug'];

$app->register(new TwigServiceProvider(), [
    'twig.path' => __DIR__ . '/views',
]);

$app->register(new SessionServiceProvider());
$app->register(new SecurityServiceProvider());

$app->register(new PommServiceProvider(), [
    'pomm.class_path' => __DIR__ . '/vendor/pomm',
    'pomm.databases' => $app['config']['pomm'],
]);

$app['db'] = $app->share(function() use ($app) {
    return $app['pomm']->createConnection();
});

if (class_exists('\Silex\Provider\WebProfilerServiceProvider')) {
    $app->register(new UrlGeneratorServiceProvider());
    $app->register(new ServiceControllerServiceProvider());

    $profiler = new WebProfilerServiceProvider();
    $app->register($profiler, [
        'profiler.cache_dir' => __DIR__ . '/../cache/profiler',
    ]);
    $app->mount('/_profiler', $profiler);
}

return $app;
