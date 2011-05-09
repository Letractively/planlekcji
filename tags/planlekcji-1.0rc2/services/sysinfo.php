<?php

/**
 * Web Service: sysinfo
 * Sprawdza stan systemu
 * 
 * @author Michal Bocian <mhl.bocian@gmail.com>
 */
/**
 * Import modulow NuSOAP i ISFramework
 */
require_once '../lib/nusoap/nusoap.php';
require_once '../modules/isf/classes/kohana/isf.php';

$ns = 'com.intersys.planlekcji';
$srv = new soap_server();
$srv->configureWSDL('sysinfo', $ns);


$srv->register('is_installed', array(), array('return' => 'xsd:boolean'), $ns);

function is_installed() {
    $isf = new Kohana_Isf();
    $isf->DbConnect();
    if (!file_exists('../modules/isf/isf_resources/default.sqlite')) {
        return false;
    } else {
        $ctb = $isf->DbSelect('sqlite_master', array('*'), 'where name="rejestr"');
        if (count($ctb) != 0) {
            return true;
        } else {
            return false;
        }
    }
}

$srv->register('version', array(), array('return' => 'xsd:string'), $ns);

function version() {
    $isf = new Kohana_Isf();
    $isf->DbConnect();
    if (!is_installed()) {
		return '';
    } else {
        $res = $isf->DbSelect('rejestr', array('*'), 'where opcja="app_ver"');
        return $res[1]['wartosc'];
    }
}

$srv->service($HTTP_RAW_POST_DATA);
exit;