<?php

/**
 * Plik serwera uslug sieci Web
 * 
 * @author Michal Bocian <mhl.bocian@gmail.com>
 * @license GNU GPL v3
 * @package main\webapi
 */
if (!file_exists('config.php')) {
    echo file_get_contents('application/planlekcji/initerr.html');
    exit;
}

require_once 'config.php';
require_once 'lib/nusoap/nusoap.php';
require_once 'modules/isf/classes/kohana/isf.php';
require_once 'application/planlekcji/webapi.app';
require_once 'application/planlekcji/globals.app';

$ver = explode(' ', App_Globals::getRegistryKey('app_ver'));
$ver = explode('.', $ver[0]);

define('API_VER', $ver[0] . '.' . $ver[1]);
define('API_NS', 'api.planlekcji.isf');

$soapsrv = new soap_server();

$soapsrv->configureWSDL('ipl-' . API_VER . 'api', API_NS);

/**
 * Struktury WSDL
 */
$soapsrv->wsdl->addComplexType(
        'Klasy', 'complexType', 'struct', 'sequence', '', array(
    'klasa' => array(
        'name' => 'klasa',
        'type' => 'xsd:string'
    ),
        )
);

$soapsrv->wsdl->addComplexType(
        'KlasyArray', 'complexType', 'array', '', 'SOAP-ENC:Array', array(), array(
    array('ref' => 'SOAP-ENC:arrayType', 'wsdl:arrayType' => 'tns:Klasy[]')
        ), 'tns:Klasy'
);

/**
 * AUTH API
 * auth
 */
$soapsrv->register('doLogin', array('username' => 'xsd:string', 'password' => 'xsd:string', 'token' => 'xsd:string'), array('return' => 'xsd:string'), API_NS);
$soapsrv->register('doShowAuthTime', array('token' => 'xsd:string'), array('return' => 'xsd:string'), API_NS);
$soapsrv->register('doRenewToken', array('token' => 'xsd:string'), array('return' => 'xsd:string'), API_NS);
$soapsrv->register('doLogout', array('token' => 'xsd:string'), array('return' => 'xsd:string'), API_NS);
$soapsrv->register('doChangePass', array('token' => 'xsd:string', 'old' => 'xsd:string', 'new' => 'xsd:string'), array('return' => 'xsd:string'), API_NS);

/**
 * SYSTEM API
 * sysapi
 */
$soapsrv->register('doGetRegistryKey', array('token' => 'xsd:string', 'key' => 'xsd:string'), array('return' => 'xsd:string'), API_NS);
$soapsrv->register('doSysClean', array('token' => 'xsd:string', 'param' => 'xsd:string'), array('return' => 'xsd:string'), API_NS);

/**
 * CLASSES MANAGMENT API
 * clsmapi
 */
$soapsrv->register('doAddClass', array('token' => 'xsd:string', 'class' => 'xsd:string'), array('return' => 'xsd:string'), API_NS);
$soapsrv->register('doDelClass', array('token' => 'xsd:string', 'class' => 'xsd:string'), array('return' => 'xsd:string'), API_NS);
$soapsrv->register('doShowClasses', array('token' => 'xsd:string'), array('return' => 'tns:KlasyArray'), API_NS);

/**
 * CLASSROOM MANAGMENT API
 * clrmapi
 */
$soapsrv->register('doAddClassroom', array('token' => 'xsd:string', 'class' => 'xsd:string'), array('return' => 'xsd:string'), API_NS);

$HTTP_RAW_POST_DATA = isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : '  ';
$soapsrv->service($HTTP_RAW_POST_DATA);