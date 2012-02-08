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
 * Odpowiada za obsluge nauczycieli
 * 
 * @package nauczyciele
 */
class Controller_Nauczyciele extends Controller {

    /**
     * Tworzy obiekt sesji i sprawdza czy zalogowany
     */
    public function __construct() {

	App_Auth::isLogged();

	if (App_Globals::getRegistryKey('edycja_danych') != 1) {
	    Kohana_Request::factory()->redirect('');
	    exit;
	}
    }

    /**
     * Wyswietla strone z nauczycielami
     *
     * @param string $err kod bledu w szablonie
     */
    public function action_index($err=null) {
	$view = View::factory('_root_template');
	$view2 = View::factory('nauczyciele_index');

	$view2->set('_err', $err);

	$view->set('bodystr', 'onLoad=\'document.forms.form1.inpName.focus()\'');
	$view->set('content', $view2->render());
	echo $view->render();
    }

    /**
     * Dodaje nauczyciela
     */
    public function action_dodaj() {
	if (isset($_POST)) {

	    $isf = new Kohana_Isf();
	    $isf->Connect(APP_DBSYS);

	    if (count($isf->DbSelect('nauczyciele', array('*'), 'where imie_naz=\'' . $_POST['inpName'] . '\'')) != 0) {
		Kohana_Request::factory()->redirect('nauczyciele/index/e1');
		exit;
	    }

	    $m = preg_match('/([.!@#$%;^&*()_+|])/i', $_POST['inpName']);

	    if ($m == true) {
		Kohana_Request::factory()->redirect('nauczyciele/index/e2');
		exit;
	    }

	    if ($_POST['inpName'] == '' || $_POST['inpName'] == null || empty($_POST['inpName'])) {
		Kohana_Request::factory()->redirect('nauczyciele/index/e3');
		exit;
	    }

	    $lit = substr($_POST['inpName'], 0, 1);
	    $rsl = $isf->DbSelect('nauczyciele', array('*'), 'where imie_naz like \'' . $lit . '%\'');
	    $nr = count($rsl) + 1;
	    $sk = strtoupper($lit . $nr);

	    $isf->DbInsert('nauczyciele', array(
		'imie_naz' => $_POST['inpName'],
		'skrot' => $sk,
	    ));

	    Kohana_Request::factory()->redirect('nauczyciele/index/pass');
	} else {
	    Kohana_Request::factory()->redirect('');
	}
    }

    /**
     * Strona zarzadzania nauczycielem
     *
     * @param string $skrot kod nauczyciela
     */
    public function action_zarzadzanie($skrot) {
	$isf = new Kohana_Isf();
	$isf->Connect(APP_DBSYS);
	$nauczyciel = $isf->DbSelect('nauczyciele', array('*'), 'where skrot=\'' . $skrot . '\'');
	$nauczyciel = $nauczyciel[0]['imie_naz'];

	$view = View::factory('_root_template');
	$view2 = View::factory('nauczyciele_zarzadzanie');
	$view2->set('nauczyciel', $nauczyciel);
	$view2->set('nskr', $skrot);

	$view->set('content', $view2->render());
	echo $view->render();
    }

    /**
     * Przypisuje nauczycielowi klase
     */
    public function action_dodklasa() {
	$naucz = $_POST['nauczyciel'];
	$klasa = $_POST['selKlasy'];
	$isf = new Kohana_Isf();
	$isf->Connect(APP_DBSYS);
	$nl = $isf->DbSelect('nauczyciele', array('*'), 'where imie_naz=\'' . $naucz . '\'');
	$nl = $nl[0]['skrot'];
	$isf->DbInsert('nl_klasy', array(
	    'nauczyciel' => $naucz,
	    'klasa' => $klasa
	));
	Kohana_Request::factory()->redirect('nauczyciele/zarzadzanie/' . $nl);
    }

    /**
     * Usuwa nauczyciela
     *
     * @param string $nauczyciel kod nauczyciela
     * @param boolean $confirm czy usunac
     */
    public function action_usun($nauczyciel, $confirm=false) {
	if ($confirm == false) {
	    $view = View::factory('_root_template');
	    $view2 = view::factory('nauczyciele_usun');
	    $view2->set('nauczyciel', $nauczyciel);

	    $view->set('content', $view2->render());
	    echo $view->render();
	} else {
	    $isf = new Kohana_Isf();
	    $isf->Connect(APP_DBSYS);
	    $nl = $isf->DbSelect('nauczyciele', array('*'), 'where skrot=\'' . $nauczyciel . '\'');
	    $isf->DbDelete('nauczyciele', 'skrot=\'' . $nauczyciel . '\'');
	    $nauczyciel = $nl[0]['imie_naz'];
	    $isf->DbDelete('nl_klasy', 'nauczyciel=\'' . $nauczyciel . '\'');
	    $isf->DbDelete('nl_przedm', 'nauczyciel=\'' . $nauczyciel . '\'');
	    Kohana_Request::factory()->redirect('nauczyciele/index');
	}
    }

    /**
     * Przypisuje przedmiot nauczycielowi
     */
    public function action_dodprzed() {
	$skr = $_POST['skrot'];
	$naucz = $_POST['nauczyciel'];
	$przedmiot = $_POST['selPrzedm'];
	$isf = new Kohana_Isf();
	$isf->Connect(APP_DBSYS);
	$isf->DbInsert('nl_przedm', array(
	    'nauczyciel' => $naucz,
	    'przedmiot' => $przedmiot
	));
	Kohana_Request::factory()->redirect('nauczyciele/zarzadzanie/' . $skr);
    }

    /**
     * Wypisuje klase nauczycielowi
     *
     * @param string $nauczyciel kod nauczyciela
     * @param string $klasa klasa klasa
     */
    public function action_klwyp($nauczyciel, $klasa) {
	$isf = new Kohana_Isf();
	$isf->Connect(APP_DBSYS);
	$nl = $isf->DbSelect('nauczyciele', array('*'), 'where skrot=\'' . $nauczyciel . '\'');
	$nl = $nl[0]['imie_naz'];
	$isf->DbDelete('nl_klasy', 'nauczyciel=\'' . $nl . '\' and klasa=\'' . $klasa . '\'');
	Kohana_Request::factory()->redirect('nauczyciele/zarzadzanie/' . $nauczyciel);
    }

    /**
     * Wypisuje przedmiot nauczycielowi
     *
     * @param string $nauczyciel kod nauczyciela
     * @param string $przedmiot przedmiot do wypisania
     */
    public function action_przwyp($nauczyciel, $przedmiot) {
	$isf = new Kohana_Isf();
	$isf->Connect(APP_DBSYS);
	$nl = $isf->DbSelect('nauczyciele', array('*'), 'where skrot=\'' . $nauczyciel . '\'');
	$nl = $nl[0]['imie_naz'];
	$isf->DbDelete('nl_przedm', 'nauczyciel=\'' . $nl . '\' and przedmiot=\'' . $przedmiot . '\'');
	Kohana_Request::factory()->redirect('nauczyciele/zarzadzanie/' . $nauczyciel);
    }

}
