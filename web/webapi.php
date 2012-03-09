<?php

/**
 * Plik serwera uslug sieci Web
 * 
 * @author Michal Bocian <mhl.bocian@gmail.com>
 * @license GNU GPL v3
 * @package ipl\api
 */
if (!file_exists('../resources/config.ini')) {
    echo file_get_contents('application/planlekcji/initerr.html');
    exit;
}

define('DS', DIRECTORY_SEPARATOR);
define('APP_ROOT', realpath('..'));
define('DOCROOT', realpath(dirname(__FILE__)) . DIRECTORY_SEPARATOR);

require_once '_boot.php';
require_once APPPATH . 'planlekcji' . DS . 'webapi.app';

Core_Tools::parseCfgFile();

$ver = explode(' ', App_Globals::getRegistryKey('app_ver'));
$ver = explode('.', $ver[0]);

define('API_VER', $ver[0] . '.' . $ver[1]);
define('API_NS', 'api.planlekcji.isf');

$soapsrv = new soap_server();
$soapsrv->soap_defencoding = 'UTF-8';
$soapsrv->configureWSDL('ipl-' . API_VER, API_NS);

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
$soapsrv->wsdl->addComplexType(
        'Sale', 'complexType', 'struct', 'sequence', '', array(
    'sala' => array(
        'name' => 'sala',
        'type' => 'xsd:string'
    ),
        )
);

$soapsrv->wsdl->addComplexType(
        'SaleArray', 'complexType', 'array', '', 'SOAP-ENC:Array', array(), array(
    array('ref' => 'SOAP-ENC:arrayType', 'wsdl:arrayType' => 'tns:Sale[]')
        ), 'tns:Sale'
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
 * CLASSES MANAGMENT API
 * clsmapi
 */
$soapsrv->register('doAddClass', array('token' => 'xsd:string', 'class' => 'xsd:string'), array('return' => 'xsd:string'), API_NS);
$soapsrv->register('doDelClass', array('token' => 'xsd:string', 'class' => 'xsd:string'), array('return' => 'xsd:string'), API_NS);
$soapsrv->register('doGetClasses', array('token' => 'xsd:string'), array('return' => 'tns:KlasyArray'), API_NS);

/**
 * CLASSROOM MANAGMENT API
 * clrmapi
 */
$soapsrv->register('doGetClassrooms', array('token' => 'xsd:string'), array('return' => 'tns:SaleArray'), API_NS);
$soapsrv->register('doAddClassroom', array('token' => 'xsd:string', 'classroom' => 'xsd:string'), array('return' => 'xsd:string'), API_NS);
$soapsrv->register('doDelClassroom', array('token' => 'xsd:string', 'classroom' => 'xsd:string'), array('return' => 'xsd:string'), API_NS);

/**
 * SYSTEM MANAGMENT API
 * sysapi
 */
$soapsrv->register('doGetRegistryKey', array('token' => 'xsd:string', 'key' => 'xsd:string'), array('return' => 'xsd:string'), API_NS);
$soapsrv->register('doSystemClean', array('token' => 'xsd:string', 'param' => 'xsd:string'), array('return' => 'xsd:string'), API_NS);

$HTTP_RAW_POST_DATA = isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : '  ';
$soapsrv->service($HTTP_RAW_POST_DATA);