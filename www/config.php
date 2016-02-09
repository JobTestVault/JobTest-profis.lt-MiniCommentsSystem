<?php

return [
	'pdo.dsn' => 'mysql:dbname=app;host=127.0.0.1',
	'pdo.username' => 'root', 
	'pdo.password' => '', 
	'pdo.options' => [
		\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'"
	],
	'twig.path' => __DIR__ . DIRECTORY_SEPARATOR . 'templates',
];
