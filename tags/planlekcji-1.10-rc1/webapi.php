<?php

/**
 * Plik serwera uslug sieci Web
 * 
 * @author Michal Bocian <mhl.bocian@gmail.com>
 * @license GNU GPL v3
 * @package main\webapi
 */
require_once 'config.php';
require_once 'lib/nusoap/nusoap.php';
require_once 'modules/isf/classes/kohana/isf.php';
require_once 'application/planlekcji/webapi.app';

$namespace = 'webapi.planlekcji.isf';

$soapsrv = new soap_server();

$soapsrv->configureWSDL('planlekcji-webapi', $namespace);

$soapsrv->wsdl->addComplexType(
        'Klasy', 'complexType', 'struct', 'sequence', '', array(
    'klasa' => array(
        'name' => 'klasa',
        'type' => 'xsd:string'
    ),
        )
);

$soapsrv->wsdl->addComplexType(
'KlasyArray',
'complexType',
'array','',
'SOAP-ENC:Array',
array(),
array(
array('ref'=>'SOAP-ENC:arrayType','wsdl:arrayType'=>'tns:Klasy[]')
),
'tns:Klasy'
);

$soapsrv->register('doLogin', array('username' => 'xsd:string', 'password' => 'xsd:string', 'token' => 'xsd:string'), array('return' => 'xsd:string'), $namespace, false, false, 'literal');
$soapsrv->register('doShowAuthTime', array('token' => 'xsd:string'), array('return' => 'xsd:string'), $namespace, false, false, 'literal');
$soapsrv->register('doRenewToken', array('token' => 'xsd:string'), array('return' => 'xsd:string'), $namespace, false, false, 'literal');
$soapsrv->register('doAddClassroom', array('token' => 'xsd:string', 'class' => 'xsd:string'), array('return' => 'xsd:string'), $namespace, false, false, 'literal');
$soapsrv->register('doGetRegistryKey', array('token' => 'xsd:string', 'key' => 'xsd:string'), array('return' => 'xsd:string'), $namespace, false, false, 'literal');
$soapsrv->register('doChangePass', array('token' => 'xsd:string', 'old' => 'xsd:string', 'new' => 'xsd:string'), array('return' => 'xsd:string'), $namespace, false, false, 'literal');
$soapsrv->register('doLogout', array('token' => 'xsd:string'), array('return' => 'xsd:string'), $namespace, false, false, 'literal');

$soapsrv->register('doShowClasses', array('token' => 'xsd:string'), array('return' => 'tns:KlasyArray'), $namespace, false, false, 'literal');

$HTTP_RAW_POST_DATA = isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : '  ';
$soapsrv->service($HTTP_RAW_POST_DATA);