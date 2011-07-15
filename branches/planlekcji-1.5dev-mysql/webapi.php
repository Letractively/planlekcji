<?php
/**
 * Plik serwera uslug sieci Web
 * 
 * @author Michal Bocian <mhl.bocian@gmail.com>
 * @license GNU GPL v3
 * @package main\webapi
 */

require_once 'lib/nusoap/nusoap.php';
require_once 'system/webapi.app';
require_once 'modules/isf/classes/kohana/isf.php';

$namespace = 'webapi.planlekcji.isf';

$soapsrv = new soap_server();

$soapsrv->configureWSDL('planlekcji-webapi', $namespace);

$soapsrv->register('doLogin', array('username' => 'xsd:string', 'password' => 'xsd:string', 'token'=>'xsd:string'), array('return' => 'xsd:string'), $namespace);
$soapsrv->register('doUserLogin', array('username' => 'xsd:string', 'password' => 'xsd:string', 'token'=>'xsd:string'), array('return' => 'xsd:string'), $namespace);
$soapsrv->register('doShowAuthTime', array('token' => 'xsd:string'), array('return' => 'xsd:string'), $namespace);
$soapsrv->register('doRenewToken', array('token'=>'xsd:string'), array('return'=>'xsd:string'), $namespace);
$soapsrv->register('doAddClassroom', array('token'=>'xsd:string', 'class'=>'xsd:string'), array('return'=>'xsd:string'), $namespace);
$soapsrv->register('doGetRegistryKey', array('token' => 'xsd:string', 'key' => 'xsd:string'), array('return' => 'xsd:string'), $namespace);
$soapsrv->register('doChangePass', array('token' => 'xsd:string', 'old'=>'xsd:string', 'new'=>'xsd:string'), array('return' => 'xsd:string'), $namespace);
$soapsrv->register('doLogout', array('token' => 'xsd:string'), array('return' => 'xsd:string'), $namespace);

$HTTP_RAW_POST_DATA = isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : '';
$soapsrv->service($HTTP_RAW_POST_DATA);