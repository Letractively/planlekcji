<?php

/**
 * Proszę NIE MODYFIKOWAĆ poniższego kodu
 */
$err = '';

if (phpversion() < 5.3) {
    $err.='&bull; Wymagane jest PHP w wersji 5.3<br/>';
}
if (!class_exists('SQLite3')) {
    $err.='&bull; Wymagana jest obsluga SQLite3 przez PHP<br/>';
}
if (!is_writable(realpath('modules/isf/isf_resources'))) {
    $err.='&bull; Katalog modules/isf/isf_resources musi byc zapisywalny<br/>';
}
if (!file_exists(realpath('modules/isf/isf_resources/default.sqlite'))) {
    require_once 'modules/isf/classes/kohana/isf.php';
    $isf = new Kohana_Isf();
    $isf->DbConnect();
}
if ((file_exists(realpath('modules/isf/isf_resources/default.sqlite'))) && !is_writable(realpath('modules/isf/isf_resources/default.sqlite'))) {
    $err .= '&bull; Plik modules/isf/isf_resources/default.sqlite musi byc zapisywalny<br/>';
}
if (!is_writable(realpath('application/logs')) || !is_writable(realpath('application/cache'))) {
    $err .= '&bull; Katalog application/logs i application/cache musi byc zapisywalny<br/>';
}
if (!empty($err)) {
    echo '<p><b>Nastapila proba nadania praw plikom i katalogom.</b></p>';
    echo '<pre><b>Na systemie UNIX uruchom nastepujace polecenie</b>' . PHP_EOL;
    echo '$ cd [sciezka_do_katalogu_z_aplikacja]' . PHP_EOL;
    echo '$ sudo php unixinstall.php</pre><p>Gdy blad wystepuje, musisz
        recznie zmienic uprawnienia plikow i katalogow</p>';
    die($err);
}
if (!file_exists('config.php')) {
    require_once 'install.php';
    exit;
} else {
    require_once 'config.php';
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
