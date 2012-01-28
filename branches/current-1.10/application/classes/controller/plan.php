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
 * Odpowiada za obsluge planow zajec
 * 
 * @package plan
 */
class Controller_Plan extends Controller {

    /**
     *
     * @var nusoap_client instancja klasy nusoap
     */
    protected $wsdl;

    /**
     * Sprawdza zalogowanie uzytkownika
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
	$isf = new Kohana_Isf();
	$isf->Connect(APP_DBSYS);
	$reg = $isf->DbSelect('rejestr', array('*'), 'where opcja=\'edycja_danych\'');
	if ($reg[0]['wartosc'] == 1) {
	    echo '<h1>Edycja danych nie zostala zamknieta</h1>';
	    exit;
	}
    }

    /**
     * Wyswietla edycje planu dla klasy AJAX
     *
     * @param string $klasa klasa
     */
    public function action_klasa($klasa) {
	$view = view::factory('plan_klasa');
	$view->set('klasa', $klasa);
	echo $view->render();
    }

    /**
     * Wyswietla plan grupowy dla klasy AJAX
     *
     * @param string $klasa klasa
     */
    public function action_grupy($klasa) {
	$view = view::factory('plan_grupy');
	$view->set('klasa', $klasa);
	echo $view->render();
    }

    /**
     * Wyswietla tresc strony dla wywolania AJAX
     * 
     * W przypadku przegladarki Internet Explorer wyswietlany jest ten
     * surowy szablon
     *
     * @param string $klasa klasa
     * @param boolean $alternative wyswietlanie klasycznej strony
     */
    public function action_klasaajax($klasa, $alternative=false) {
	$view = view::factory('plan_klasaajax');
	$view->set('alternative', $alternative);
	$view->set('klasa', $klasa);
	echo $view->render();
    }

    /**
     * Wyswietla tresc strony dla wywolania AJAX
     * 
     * W przypadku przegladarki Internet Explorer wyswietlany jest ten
     * surowy szablon
     * 
     * @param string $klasa klasa
     * @param boolean $alternative wyswietlanie klasycznej strony
     */
    public function action_grupaajax($klasa, $alternative=false) {
	$view = view::factory('plan_grupaajax');
	$view->set('alternative', $alternative);
	$view->set('klasa', $klasa);
	echo $view->render();
    }

    /**
     * Wprowadza zmiany do planu klasy
     */
    public function action_zatwierdz() {
	$isf = new Kohana_Isf();
	$isf->Connect(APP_DBSYS);
	$klasa = $_POST['klasa'];
	$isf->DbDelete('plan_grupy', 'klasa=\'' . $klasa . '\'');

	$dni = array('Poniedziałek', 'Wtorek', 'Środa', 'Czwartek', 'Piątek');

	foreach ($dni as $dzien) {
	    foreach ($_POST[$dzien] as $lek => $przedm) {
		if ($przedm == '---') {
		    $isf->DbDelete('planlek', 'dzien=\'' . $dzien . '\' and lekcja=\'' . $lek . '\' and klasa=\'' . $klasa . '\'');
		} else {
		    $przedm = explode(':', $przedm);
		    $isf->DbDelete('planlek', 'dzien=\'' . $dzien . '\' and lekcja=\'' . $lek . '\' and klasa=\'' . $klasa . '\'');
		    if (count($przedm) == 1) {
			$colval = array(
			    'dzien' => $dzien,
			    'klasa' => $klasa,
			    'lekcja' => $lek,
			    'przedmiot' => $przedm[0],
			);
		    } else {
			$nl_s = $isf->DbSelect('nauczyciele', array('skrot'), 'where imie_naz=\'' . $przedm[2] . '\'');
			$nl_s = $nl_s[0]['skrot'];
			$colval = array(
			    'dzien' => $dzien,
			    'klasa' => $klasa,
			    'lekcja' => $lek,
			    'przedmiot' => $przedm[0],
			    'sala' => $przedm[1],
			    'nauczyciel' => $przedm[2],
			    'skrot' => $nl_s,
			);
		    }
		    $isf->DbInsert('planlek', $colval);
		}
	    }
	}

	Kohana_Request::factory()->redirect();
    }

    /**
     * Wprowadza zmiany do planu grupowego
     */
    public function action_grupazatw() {
	$isf = new Kohana_Isf();
	$isf->Connect(APP_DBSYS);
	$klasa = $_POST['klasa'];
	$isf->DbDelete('plan_grupy', 'klasa=\'' . $klasa . '\'');

	$dni = array('Poniedziałek', 'Wtorek', 'Środa', 'Czwartek', 'Piątek');

	$err = '';

	foreach ($dni as $dzien) {
	    foreach ($_POST[$dzien] as $lek => $przedlek) {

		foreach ($przedlek as $grupa => $przedm) {
		    if ($przedm != '---') {
			$przedm = explode(':', $przedm);
			if (count($przedm) == 1) {
			    $colval = array(
				'dzien' => $dzien,
				'klasa' => $klasa,
				'lekcja' => $lek,
				'grupa' => $grupa,
				'przedmiot' => $przedm[0],
			    );
			} else {
			    $nl_s = $isf->DbSelect('nauczyciele', array('skrot'), 'where imie_naz=\'' . $przedm[2] . '\'');
			    $nl_s = $nl_s[0]['skrot'];

			    $valid_cond = 'where dzien=\'' . $dzien . '\' and lekcja=\'' . $lek . '\' and nauczyciel=\'' . $przedm[2] . '\' and sala!=\'' . $przedm[1] . '\'';
			    $valid = $isf->DbSelect('plan_grupy', array('*'), $valid_cond);
			    if (count($valid) > 0) {
				$err .= '<p>Nauczyciel ' . $przedm[2] . ' prowadzi juz zajecia z <b>' . $valid[0]['przedmiot'] . '</b> w
				    <b>' . $dzien . '</b> na lekcji ' . $lek . '
					w sali ' . $valid[0]['sala'] . ' (wybrana sala: <b>' . $przedm[1] . '</b>, przedmiot:
					    <b>' . $przedm[0] . '</b>)<br/>
					Pominieto: <b>' . $dzien . '</b> lek:<b>' . $lek . '</b>
					    klasa:<b>' . $klasa . '</b> gr:<b>' . $grupa . ' ' . $przedm[0] . '</b></p>---';
			    } else {

				$colval = array(
				    'dzien' => $dzien,
				    'klasa' => $klasa,
				    'lekcja' => $lek,
				    'grupa' => $grupa,
				    'przedmiot' => $przedm[0],
				    'sala' => $przedm[1],
				    'nauczyciel' => $przedm[2],
				    'skrot' => $nl_s,
				);
			    }
			    $isf->DbInsert('plan_grupy', $colval);
			}
		    }
		}
	    }
	}
	if ($err == '') {
	    Kohana_Request::factory()->redirect();
	} else {
	    $view = view::factory('main');
	    $view2 = view::factory('plan_error');
	    $view2->set('content', $err);
	    $view->set('content', $view2->render());
	    echo $view;
	}
    }

    /**
     * Wyswietla strone eksportu
     */
    public function action_export() {
	$view = view::factory('main');
	$view2 = view::factory('plan_export');

	$view->set('content', $view2->render());
	echo $view->render();
    }

}