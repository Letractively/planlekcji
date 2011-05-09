<?php
/*
 * Ścieżka do aplikacji w adresie URL
 * 
 * Jeżeli uruchamiasz aplikację wpisując: http://[nazwa_hosta]/, nie zmienaj
 * tej wartości, w przeciwnym razie zmień na właściwą wartość.
 * 
 */
$path = '/';

/**
 * Proszę NIE MODYFIKOWAĆ poniższego kodu
 */

if(phpversion()<5.3){
    die('<h1>Aplikacja Plan Lekcji, wymaga PHP w wersji min. 5.3</h1>');
}
if(!class_exists('SQLite3')){
    die('<h1>Aplikacja Plan Lekcji wymaga obslugi SQLite3 przez PHP</h1>');
}
if(!is_writable('modules/isf/isf_resources')){
    die('<h1>Katalog <b>modules/isf/isf_resources</b> musi byc zapisywalny</h1>');
}
define('HTTP_PATH', $path);
$application = 'application';
$modules = 'modules';
$system = 'system';
define('EXT', '.php');
error_reporting(E_ALL | E_STRICT);
define('DOCROOT', realpath(dirname(__FILE__)) . DIRECTORY_SEPARATOR);
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
    // Load the installation check
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
