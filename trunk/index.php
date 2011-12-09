<?php
/**
 * Plik główny projektu - bootloader Kohany
 * 
 * @author Michał Bocian <mhl.bocian@gmail.com>
 * @version 1.5
 * @license GNU GPL v3
 * @package main\index
 */
/**
 * Proszę NIE MODYFIKOWAĆ poniższego kodu
 */
$err = '';

if (phpversion() < '5.2.5') {
    $err.='&bull; Wymagane jest PHP w wersji 5.2.5<br/>';
}
if (!class_exists('PDO')||!extension_loaded('pdo_sqlite')) {
    $err.='&bull; Wymagana jest obsluga PDO SQLite3 przez PHP<br/>';
}
if (!is_writable(realpath('application/logs')) || !is_writable(realpath('application/cache'))) {
    $err .= '&bull; Katalog application/logs i application/cache musi byc zapisywalny<br/>';
}
if (!empty($err)) {
    echo $err;
    echo '<p><b>Jezeli blad dotyczy uprawnien plikow:</b></p>';
    echo '<pre><b>Na systemie UNIX uruchom nastepujace polecenie</b>' . PHP_EOL;
    echo '$ cd [sciezka_do_katalogu_z_aplikacja]' . PHP_EOL;
    echo '$ sudo php unixinstall.php</pre><p>Gdy blad wystepuje, musisz
        recznie zmienic uprawnienia plikow i katalogow</p>';
    die();
}
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
function insert_log ($modul, $wiadomosc){
    $db = new Kohana_Isf();
    $db->Connect(APP_DBSYS);
    $id = $db->DbSelect('log', array('max(id) as id'));
    if(count($id)==0)
	$id=1;
    else
	$id = $id[0]['id'];
    $db->DbInsert('log', array('id'=>$id, 'data'=>date('d.m.Y H:i:s'),'modul'=>$modul,'wiadomosc'=>$wiadomosc));
}