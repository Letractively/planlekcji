<?php
/**
 * Intersys - Plan Lekcji
 * 
 * Wersja pierwsza - 1.0
 * 
 * @author Michał Bocian <mhl.bocian@gmail.com>
 */
defined('SYSPATH') or die('No direct script access.');
/**
 * Kontroler: klasy
 * 
 * Rola: Odpowiada za obsługę klas
 */
class Controller_Klasy extends Controller {

    public function __construct() {
        session_start();
        if (!isset($_SESSION['valid']) || !isset($_COOKIE['PHPSESSID'])) {
            Kohana_Request::factory()->redirect('admin/login');
            exit;
        }
        $isf = new Kohana_Isf();
        $isf->DbConnect();
        $reg = $isf->DbSelect('rejestr', array('*'), 'where opcja="edycja_danych"');
        if ($reg[1]['wartosc'] != 1) {
            echo '<h1>Edycja danych zostala zamknieta</h1>';
            exit;
        }
    }

    public function action_index($err=null) {
        $view = View::factory('main');
        $view2 = View::factory('klasy_index');

        $view2->set('_err', $err);

        $view->set('content', $view2->render());
        echo $view->render();
    }

    public function action_usun($klasa) {
        $isf = new Kohana_Isf();
        $isf->DbConnect();
        $isf->DbDelete('klasy', 'klasa="' . $klasa . '"');
        $isf->DbDelete('nl_klasy', 'klasa="' . $klasa . '"');
        Kohana_Request::factory()->redirect('klasy/index/usun');
    }

    public function action_dodaj() {

        if (!isset($_POST)) {

            Kohana_Request::factory()->redirect('klasy/index');
            exit;
        } else {

            $isf = new Kohana_Isf();
            $isf->DbConnect();

            if (count($isf->DbSelect('klasy', array('*'), 'where klasa="' . $_POST['inpKlasa'] . '"')) != 0) {
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

    public function action_grupyklasowe() {
        if (isset($_POST)) {
            $i = $_POST['grp'];
            $isf = new Kohana_Isf();
            $isf->DbConnect();
            $isf->DbUpdate('rejestr', array('wartosc' => $i), 'opcja="ilosc_grup"');
        }
        Kohana_Request::factory()->redirect('klasy/index');
    }

}