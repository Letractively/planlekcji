<?php

/**
 * Plik główny projektu - bootloader Kohany
 * 
 * @author Michał Bocian <mhl.bocian@gmail.com>
 * @version 1.5
 * @license GNU GPL v3
 * @package main\index
 */
date_default_timezone_set('UTC');
define('APPROOTPATH', realpath('..'));
define('DOCROOT', realpath(dirname(__FILE__)) . DIRECTORY_SEPARATOR);
if (!file_exists('config.php')) {
    require_once 'install.php';
    exit;
} else {
    require_once 'config.php';
}
require_once 'lib/nusoap/nusoap.php';
define('HTTP_PATH', $path);
$application = 'application';
$modules = 'modules';
$system = 'system';
define('EXT', '.php');
define('DS', DIRECTORY_SEPARATOR);
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
if (file_exists('install' . EXT)) {
    /**
     * Sprawdza istnienie pliku install.php
     */
    return include 'install' . EXT;
}
if (!defined('KOHANA_START_TIME')) {
    define('KOHANA_START_TIME', microtime(TRUE));
}
if (!defined('KOHANA_START_MEMORY')) {
    define('KOHANA_START_MEMORY', memory_get_usage());
}
require APPPATH . 'bootstrap' . EXT;
echo Request::factory()
	->execute()
	->send_headers()
	->body();

/**
 * Dodaje nowa wiadomosc do loga systemowego
 *
 * @param string $modul
 * @param string $wiadomosc 
 */
function insert_log($modul, $wiadomosc) {
    if (!file_exists(APPROOTPATH . DS . 'resources' . DS . 'ipl-' . date('Ymd') . '.log')) {
	$content = '';
    } else {
	$content = file_get_contents(APPROOTPATH . DS . 'resources' . DS . 'ipl-' . date('Ymd') . '.log');
    }

    $file_handler = fopen(APPROOTPATH . DS . 'resources' . DS . 'ipl-' . date('Ymd') . '.log', 'w');

    $timestamp = date('H:i:s');
    $message = '[' . $timestamp . '] ' . $modul . ': ' . $wiadomosc . PHP_EOL;

    $content .= $message;

    fwrite($file_handler, $content);
    fclose($file_handler);
}