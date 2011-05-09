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
 * Kontroler: nauczyciele
 * 
 * Rola: Odpowiada za obsługę nauczycieli
 */
class Controller_Nauczyciele extends Controller {

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
        $view2 = View::factory('nauczyciele_index');

        $view2->set('_err', $err);

        $view->set('content', $view2->render());
        echo $view->render();
    }

    public function action_dodaj() {
        if (isset($_POST)) {

            $isf = new Kohana_Isf();
            $isf->DbConnect();

            if (count($isf->DbSelect('nauczyciele', array('*'), 'where imie_naz="' . $_POST['inpName'] . '"')) != 0) {
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

            $in = explode(' ', $_POST['inpName']);
            $sk = substr($in[0], 0, 3) . substr($in[1], 0, 3);
            $sk = strtoupper($sk);

            $isf->DbInsert('nauczyciele', array(
                'imie_naz' => $_POST['inpName'],
                'skrot' => $sk,
            ));

            Kohana_Request::factory()->redirect('nauczyciele/index/pass');
        } else {
            Kohana_Request::factory()->redirect('');
        }
    }

    public function action_zarzadzanie($nauczyciel) {
        $isf = new Kohana_Isf();
        $isf->DbConnect();
        $sk = $isf->DbSelect('nauczyciele', array('skrot'), 'where imie_naz="' . $nauczyciel . '"');
        $sk = $sk[1]['skrot'];

        $view = View::factory('main');
        $view2 = View::factory('nauczyciele_zarzadzanie');
        $view2->set('nauczyciel', $nauczyciel);
        $view2->set('nskr', $sk);

        $view->set('content', $view2->render());
        echo $view->render();
    }

    public function action_dodklasa() {
        $naucz = $_POST['Nauczyciel'];
        $klasa = $_POST['selKlasy'];
        $isf = new Kohana_Isf();
        $isf->DbConnect();
        $isf->DbInsert('nl_klasy', array(
            'nauczyciel' => $naucz,
            'klasa' => $klasa
        ));
        Kohana_Request::factory()->redirect('nauczyciele/zarzadzanie/' . $naucz);
    }

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
            $isf->DbDelete('nauczyciele', 'imie_naz="' . $nauczyciel . '"');
            $isf->DbDelete('nl_klasy', 'nauczyciel="' . $nauczyciel . '"');
            $isf->DbDelete('nl_przedm', 'nauczyciel="' . $nauczyciel . '"');
            Kohana_Request::factory()->redirect('nauczyciele/index/usun');
        }
    }

    public function action_dodprzed() {
        $naucz = $_POST['Nauczyciel'];
        $przedmiot = $_POST['selPrzedm'];
        $isf = new Kohana_Isf();
        $isf->DbConnect();
        $isf->DbInsert('nl_przedm', array(
            'nauczyciel' => $naucz,
            'przedmiot' => $przedmiot
        ));
        Kohana_Request::factory()->redirect('nauczyciele/zarzadzanie/' . $naucz);
    }

    public function action_klwyp($nauczyciel, $klasa) {
        $isf = new Kohana_Isf();
        $isf->DbConnect();
        $isf->DbDelete('nl_klasy', 'nauczyciel="' . $nauczyciel . '" and klasa="' . $klasa . '"');
        Kohana_Request::factory()->redirect('nauczyciele/zarzadzanie/' . $nauczyciel);
    }

    public function action_przwyp($nauczyciel, $przedmiot) {
        $isf = new Kohana_Isf();
        $isf->DbConnect();
        $isf->DbDelete('nl_przedm', 'nauczyciel="' . $nauczyciel . '" and przedmiot="' . $przedmiot . '"');
        Kohana_Request::factory()->redirect('nauczyciele/zarzadzanie/' . $nauczyciel);
    }

}