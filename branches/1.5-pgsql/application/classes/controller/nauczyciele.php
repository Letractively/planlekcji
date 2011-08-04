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
 * Odpowiada za obsluge nauczycieli
 * 
 * @package nauczyciele
 */
class Controller_Nauczyciele extends Controller {

    /**
     *
     * @var nusoap_client instancja klasy nusoap
     */
    public $wsdl;

    /**
     * Tworzy obiekt sesji i sprawdza czy zalogowany
     */
    public function __construct() {
        session_start();
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
        $isf->DbConnect();
        $reg = $isf->DbSelect('rejestr', array('*'), 'where opcja=\'edycja_danych\'');
        /**
         * Czy mozna edytowac dane
         */
        if ($reg[1]['wartosc'] != 1) {
            echo '<h1>Edycja danych zostala zamknieta</h1>';
            exit;
        }
    }

    /**
     * Wyswietla strone z nauczycielami
     *
     * @param string $err kod bledu w szablonie
     */
    public function action_index($err=null) {
        $view = View::factory('main');
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
            $isf->DbConnect();

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
        $isf->DbConnect();
        $nauczyciel = $isf->DbSelect('nauczyciele', array('*'), 'where skrot=\'' . $skrot . '\'');
        $nauczyciel = $nauczyciel[1]['imie_naz'];

        $view = View::factory('main');
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
        $isf->DbConnect();
        $nl = $isf->DbSelect('nauczyciele', array('*'), 'where imie_naz=\''.$naucz.'\'');
        $nl = $nl[1]['skrot'];
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
            $view = view::factory('main');
            $view2 = view::factory('nauczyciele_usun');
            $view2->set('nauczyciel', $nauczyciel);

            $view->set('content', $view2->render());
            echo $view->render();
        } else {
            $isf = new Kohana_Isf();
            $isf->DbConnect();
            $nl = $isf->DbSelect('nauczyciele', array('*'), 'where skrot=\'' . $nauczyciel . '\'');
            $isf->DbDelete('nauczyciele', 'skrot=\'' . $nauczyciel . '\'');
            $nauczyciel = $nl[1]['imie_naz'];
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
        $isf->DbConnect();
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
        $isf->DbConnect();
        $nl = $isf->DbSelect('nauczyciele', array('*'), 'where skrot=\''.$nauczyciel.'\'');
        $nl = $nl[1]['imie_naz'];
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
        $isf->DbConnect();
        $nl = $isf->DbSelect('nauczyciele', array('*'), 'where skrot=\'' . $nauczyciel . '\'');
        $nl = $nl[1]['imie_naz'];
        $isf->DbDelete('nl_przedm', 'nauczyciel=\'' . $nl . '\' and przedmiot=\'' . $przedmiot . '\'');
        Kohana_Request::factory()->redirect('nauczyciele/zarzadzanie/' . $nauczyciel);
    }

}
