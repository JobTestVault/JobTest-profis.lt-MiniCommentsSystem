<?php

require_once __DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

// set the error handling
ini_set('display_errors', 1);
error_reporting(-1);
Symfony\Component\Debug\ErrorHandler::register();
if ('cli' !== php_sapi_name()) {
	Symfony\Component\Debug\ExceptionHandler::register();
}

// init application
$app = new Silex\Application();

// set debug mode
$app['debug'] = true;

$app->register(new Silex\Provider\ServiceControllerServiceProvider());
$app->register(new Silex\Provider\TwigServiceProvider());
$app->register(new Herrera\Pdo\PdoServiceProvider());
$app->register(new Silex\Provider\UrlGeneratorServiceProvider());
$app->register(new Silex\Provider\SessionServiceProvider());
$app->register(new Igorw\Silex\ConfigServiceProvider(__DIR__ . DIRECTORY_SEPARATOR . 'config.php'));

$app->mount('/admin', new App\Controller\AdminController());
$app->mount('/comments', new App\Controller\CommentsController());
$app->mount('/', new App\Controller\IndexController());

$app->run();