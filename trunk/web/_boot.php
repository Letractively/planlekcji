<?php

require_once 'lib/nusoap/nusoap.php';
require_once DOCROOT . 'modules' . DS . 'isf' . DS . 'classes' . DS . 'kohana' . DS . 'isf.php';
require_once DOCROOT . 'modules' . DS . 'isf' . DS . 'classes' . DS . 'isf2.php';
require_once APPPATH . 'planlekcji/system.app';
require_once APPPATH . 'planlekcji/core.app';

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