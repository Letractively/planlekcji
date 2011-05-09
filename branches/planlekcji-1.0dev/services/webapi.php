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

function is_installed() {
    $isf = new Kohana_Isf();
    $isf->DbConnect();
    if (!file_exists('../modules/isf/isf_resources/default.sqlite')) {
        echo 'Uslugi WebAPI sa niedostepne dopoki system nie zostanie zainstalowany';
        exit;
    } else {
        $ctb = $isf->DbSelect('sqlite_master', array('*'), 'where name="rejestr"');
        if (count($ctb) == 0) {
            echo 'Uslugi WebAPI sa niedostepne dopoki system nie zostanie zainstalowany';
            exit;
        }
    }
}

$ns = 'com.intersys.planlekcji';
$srv = new soap_server();
$srv->configureWSDL('sysinfo', $ns);


$srv->register('is_installed', array(), array('return' => 'xsd:boolean'), $ns);

$srv->register('version', array(), array('return' => 'xsd:string'), $ns);

function version() {
    $isf = new Kohana_Isf();
    $isf->DbConnect();
    $res = $isf->DbSelect('rejestr', array('*'), 'where opcja="app_ver"');
    return $res[1]['wartosc'];
}

$srv->service($HTTP_RAW_POST_DATA);
exit;