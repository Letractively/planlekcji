<?php

/**
 * Internetowy Plan Lekcji
 * 
 * @author Michal Bocian <mhl.bocian@gmail.com>
 * @license GNU GPL v3
 * @package ipl\logic
 */
defined('SYSPATH') or die('No direct script access.');

/**
 * 
 * Odpowiada za obssluge sal lekcyjnych
 * 
 * @package sale
 */
class Controller_Sale extends Controller {

    /**
     *
     * @var nusoap_client instancja klasy NuSOAP
     */
    public $wsdl;

    /**
     * Tworzy obiekt sesji i sprawdza czy zalogowany
     */
    public function __construct() {
	try {
	    $this->wsdl = new nusoap_client(URL::base('http') . 'webapi.php?wsdl');
	} catch (Exception $e) {
	    echo $e->getMessage();
	    exit;
	}
	if (!isset($_SESSION['token'])) {
	    Kohana_Request::factory()->redirect('admin/login');
	    exit;
	} else {
	    $auth = $this->wsdl->call('doShowAuthTime', array('token' => $_SESSION['token']), 'webapi.planlekcji.isf');
	    if (strtotime($_SESSION['token_time']) < time()) {
		$this->wsdl->call('doLogout', array('token' => $_SESSION['token']), 'webapi.planlekcji.isf');
		session_destroy();
		Kohana_Request::factory()->redirect('admin/login/delay');
		exit;
	    }
	    if ($auth == 'auth:failed') {
		Kohana_Request::factory()->redirect('admin/login');
		exit;
	    }
	}

	/**
	 * Czy mozna edytowac dane
	 */
	if (App_Globals::getRegistryKey('edycja_danych') != 1 || $_SESSION['user'] != 'root') {
	    Kohana_Request::factory()->redirect('');
	    exit;
	}
    }

    /**
     * Wyswietla strone glowna z salami
     *
     * @param string $err kod bledu
     */
    public function action_index($err=null) {
	$view = View::factory('_root_template');

	$dbres = Isf2::Connect()->Select('sale')
			->OrderBy(array('sala' => 'asc'))
			->Execute()->fetchAll();

	$view2 = View::factory('sale_index');
	$view2->set('res', $dbres);
	$view2->set('_err', $err);


	$view->set('bodystr', 'onLoad=\'document.forms.form1.inpSala.focus()\'');
	$view->set('content', $view2->render());
	echo $view->render();
    }
    /**
     * Operacje na dodawaniu/usuwaniu sal
     * 
     */
    public function action_commit() {
	if (!isset($_POST)) {
	    Kohana_Request::factory()->redirect('');
	    exit;
	}
	if (isset($_POST['btnAdd'])) {
	    $sala_exist = Isf2::Connect()->Select('sale')->
			    Where(array('sala' => $_POST['inpSala']))->
			    Execute()->fetchAll();
	    if (count($sala_exist) != 0) {
		Kohana_Request::factory()->redirect('sale/index/e1');
		exit;
	    }

	    $m = preg_match('/([.!@#$;%^&*()_+|])/i', $_POST['inpSala']);

	    if ($m == true) {
		Kohana_Request::factory()->redirect('sale/index/e2');
		exit;
	    }

	    if ($_POST['inpSala'] == '' || $_POST['inpSala'] == null || empty($_POST['inpSala'])) {
		Kohana_Request::factory()->redirect('sale/index/e3');
		exit;
	    }
	    try {
		Isf2::Connect()->Insert('sale', array('sala' => $_POST['inpSala']))
			->Execute();
	    } catch (Exception $e) {
		Core_Tools::ShowError($e->getMessage(), $e->getCode());
	    }
	    Kohana_Request::factory()->redirect('sale/index/pass');
	}
	if (isset($_POST['btnDelClasses'])) {
	    if (!isset($_POST['sDel'])) {
		Kohana_Request::factory()->redirect('sale/index/nchk');
	    }
	    foreach ($_POST['sDel'] as $id => $sala) {
		Isf2::Connect()->Delete('sale')
			->Where(array('sala' => $sala))
			->Execute();
		Isf2::Connect()->Delete('przedmiot_sale')
			->Where(array('sala' => $sala))
			->Execute();
	    }
	    Kohana_Request::factory()->redirect('sale/index/usun');
	}
	if (isset($_POST['btnEditClasses'])) {
	    if (!isset($_POST['rdClassroom'])) {
		Kohana_Request::factory()->redirect('sale/index/nchkc');
	    }
	    $this->action_przedmiot($_POST['rdClassroom']);
	}
    }
    /**
     * Wypisuje przedmiot z sali
     */
    public function action_wypisz() {
	if (isset($_POST)) {
	    if (isset($_POST['rdPrzedmiot']))
		$this->action_przedusun($_POST['sala'], $_POST['rdPrzedmiot']);
	    $this->action_przedmiot($_POST['sala']);
	}else {
	    Kohana_Request::factory()->redirect('');
	}
    }

    /**
     * Dodaje do sali przedmiot
     */
    public function action_dodaj() {
	$sala = $_POST['formSala'];
	$przedmiot = $_POST['selPrzed'];
	$isf = new Kohana_Isf();
	$isf->Connect(APP_DBSYS);
	$isf->DbInsert('przedmiot_sale', array(
	    'przedmiot' => $przedmiot,
	    'sala' => $sala,
	));
	$this->action_przedmiot($_POST['sala']);
    }

    /**
     * Usuwa przypisanie sali do przedmiotow
     *
     * @param string $sala sala
     * @param string $przedmiot przedmiot
     */
    private function action_przedusun($sala, $przedmiot) {
	$isf = new Kohana_Isf();
	$isf->Connect(APP_DBSYS);
	$isf->DbDelete('przedmiot_sale', 'przedmiot=\'' . $przedmiot . '\' and sala=\'' . $sala . '\'');
    }

    /**
     * Przypisanie sali do przedmiotow
     *
     * @param string $sala sala
     */
    private function action_przedmiot($sala) {
	$isf = new Kohana_Isf();
	$isf->Connect(APP_DBSYS);

	$view = View::factory('_root_template');
	$view2 = view::factory('sale_przedmiot');
	$view2->set('sala', $sala);

	$view->set('content', $view2->render());
	echo $view->render();
    }

}
