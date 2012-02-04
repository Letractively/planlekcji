<?php

/**
 * Intersys - Plan Lekcji
 * 
 * @author Michal Bocian <mhl.bocian@gmail.com>
 * @license GNU GPL v3
 * @package logic
 */
defined('SYSPATH') or die('No direct script access.');

/**
 * 
 * Glowny kontroler i domyslny podczas uruchomienia
 * 
 * @package default
 */
class Controller_Default extends Controller {

    /**
     *
     * @var nusoap_client instancja klasy nusoap
     */
    public $wsdl;

    /**
     * Tworzy obiekt sesji i sprawdza system RAND_TOKEN
     */
    public function __construct() {
	if (isset($_SESSION['token'])) {
	    try {
		$this->wsdl = new nusoap_client(URL::base('http') . 'webapi.php?wsdl');
	    } catch (Exception $e) {
		echo $e->getMessage();
		exit;
	    }
	    if (strtotime($_SESSION['token_time']) < time()) {
		$this->wsdl->call('doLogout', array('token' => $_SESSION['token']), 'webapi.planlekcji.isf');
		session_destroy();
		Kohana_Request::factory()->redirect('admin/login/delay');
		exit;
	    }
	}
    }

    /**
     * Wyswietla strone glowna
     */
    public function action_index($param=false) {
	if ($param == 'nomobile') {
	    setcookie('_nomobile', true, time() + 3600 * 24, '/');
	}
	$view = View::factory('main');

	$view->set('content', App_Globals::getRegistryKey('index_text'));
	echo $view->render();
    }

    /**
     * Zmienia temat strony
     */
    public function action_look() {
	if (!isset($_POST)) {
	    Kohana_Request::factory()->redirect();
	    exit;
	}
	$_SESSION['app_theme'] = $_POST['look'];
	Kohana_Request::factory()->redirect($_POST['site']);
    }

    /**
     * Informacje o systemie
     */
    public function action_about() {
	$view = View::factory('main');
	$view2 = View::factory('default_about');

	$view->set('content', $view2->render());
	echo $view->render();
    }

    public function action_experimental() {
	$view = View::factory('test_plan');
	echo $view->render();
    }

}