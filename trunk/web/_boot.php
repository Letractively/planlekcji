<?php

/**
 * Bootloader bibliotek IPL
 * @author Michal Bocian <mhl.bocian@gmail.com>
 * @license GNU GPL v3
 * @package core
 */
require_once 'lib/nusoap/nusoap.php';
if (!defined('SYSPATH')) {
    define('APP_ROOT', realpath('..'));
    define('DS', DIRECTORY_SEPARATOR);
    define('DOCROOT', realpath(dirname(__FILE__)) . DIRECTORY_SEPARATOR);
    define('APPPATH', realpath('application') . DIRECTORY_SEPARATOR);
}
require_once APPPATH . 'planlekcji' . DS . 'isf.app';
require_once APPPATH . 'planlekcji' . DS . 'isf2.app';
require_once APPPATH . 'planlekcji' . DS . 'system.app';
require_once APPPATH . 'planlekcji' . DS . 'core.app';

function insert_log($modul, $wiadomosc) {
    if (!file_exists(APP_ROOT . DS . 'resources' . DS . 'ipl-' . date('Ymd') . '.log')) {
        $content = '';
    } else {
        $content = file_get_contents(APP_ROOT . DS . 'resources' . DS . 'ipl-' . date('Ymd') . '.log');
    }

    $file_handler = fopen(APP_ROOT . DS . 'resources' . DS . 'ipl-' . date('Ymd') . '.log', 'w');

    $timestamp = date('H:i:s');
    $message = '[' . $timestamp . '] ' . $modul . ': ' . $wiadomosc . PHP_EOL;

    $content .= $message;

    fwrite($file_handler, $content);
    fclose($file_handler);
}