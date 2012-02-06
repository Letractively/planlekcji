<?php

/**
 * Plik główny projektu - bootloader Kohany
 * 
 * @author Michał Bocian <mhl.bocian@gmail.com>
 * @license GNU GPL v3
 * @package main\index
 */
date_default_timezone_set('UTC');
define('APP_ROOT', realpath('..'));
define('DS', DIRECTORY_SEPARATOR);
define('DOCROOT', realpath(dirname(__FILE__)) . DIRECTORY_SEPARATOR);
$application = 'application';
$modules = 'modules';
$system = 'system';
define('EXT', '.php');
error_reporting(E_ALL | E_STRICT);
if (!is_dir($application) AND is_dir(DOCROOT . $application))
    $application = DOCROOT . $application;
if (!is_dir($modules) AND is_dir(DOCROOT . $modules))
    $modules = DOCROOT . $modules;
if (!is_dir($system) AND is_dir(DOCROOT . $system))
    $system = DOCROOT . $system;
define('APPPATH', realpath($application) . DIRECTORY_SEPARATOR);
define('MODPATH', realpath($modules) . DIRECTORY_SEPARATOR);
define('SYSPATH', realpath($system) . DIRECTORY_SEPARATOR);
unset($application, $modules, $system);

if (!defined('KOHANA_START_TIME')) {
    define('KOHANA_START_TIME', microtime(TRUE));
}

if (!defined('KOHANA_START_MEMORY')) {
    define('KOHANA_START_MEMORY', memory_get_usage());
}

require '_boot.php';

try {
    Core_Tools::CheckInstalled();
} catch (Exception $e) {
    if ($e->getCode() == 501) {
	return include 'install.php';
	exit;
    } else {
	define('APP_PATH', global_app_path);
	define('APP_DBSYS', global_app_dbsys);
	require APPPATH . 'bootstrap' . EXT;
	echo Request::factory()
		->execute()
		->send_headers()
		->body();
    }
}