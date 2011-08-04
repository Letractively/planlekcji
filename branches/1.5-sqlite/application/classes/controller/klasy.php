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
 * Odpowiada za obsluge klas
 * 
 * @package klasy
 */
class Controller_Klasy extends Controller {

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
     * Strona glowna
     */
    public function action_index($err=null) {
        $view = View::factory('main');
        $view2 = View::factory('klasy_index');

        $view2->set('_err', $err);
        $view->set('bodystr', 'onLoad=\'document.forms.form1.inpKlasa.focus()\'');
        $view->set('content', $view2->render());
        echo $view->render();
    }

    /**
     * Usuwa klase
     *
     * @param string $klasa klasa
     */
    public function action_usun($klasa) {
        $isf = new Kohana_Isf();
        $isf->DbConnect();
        $isf->DbDelete('klasy', 'klasa=\'' . $klasa . '\'');
        $isf->DbDelete('nl_klasy', 'klasa=\'' . $klasa . '\'');
        Kohana_Request::factory()->redirect('klasy/index/usun');
    }

    /**
     * Dodaje klase, waliduje dane
     */
    public function action_dodaj() {

        if (!isset($_POST)) {

            Kohana_Request::factory()->redirect('klasy/index');
            exit;
        } else {

            $isf = new Kohana_Isf();
            $isf->DbConnect();

            if (count($isf->DbSelect('klasy', array('*'), 'where klasa=\'' . $_POST['inpKlasa'] . '\'')) != 0) {
                Kohana_Request::factory()->redirect('klasy/index/e1');
                exit;
            }

            $m = preg_match('/([.!@#$;%^&*()_+|])/i', $_POST['inpKlasa']);

            if ($m == true) {
                Kohana_Request::factory()->redirect('klasy/index/e2');
                exit;
            }

            if ($_POST['inpKlasa'] == '' || $_POST['inpKlasa'] == null || empty($_POST['inpKlasa'])) {
                Kohana_Request::factory()->redirect('klasy/index/e3');
                exit;
            }

            $isf->DbInsert('klasy', array('klasa' => $_POST['inpKlasa']));
            Kohana_Request::factory()->redirect('klasy/index/pass');
        }
    }

    /**
     * Strona grup klasowych
     */
    public function action_grupyklasowe() {
        if (isset($_POST)) {
            $i = $_POST['grp'];
            $isf = new Kohana_Isf();
            $isf->DbConnect();
            $isf->DbUpdate('rejestr', array('wartosc' => $i), 'opcja=\'ilosc_grup\'');
        }
        Kohana_Request::factory()->redirect('klasy/index');
    }

}
