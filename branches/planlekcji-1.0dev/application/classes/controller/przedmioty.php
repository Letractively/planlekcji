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
 * Kontroler: przedmioty
 * 
 * Rola: Odpowiada za obsługę przedmiotów
 */
class Controller_Przedmioty extends Controller {

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

        $isf = new Kohana_Isf();
        $isf->DbConnect();

        $view = view::factory('main');
        $view2 = view::factory('przedmioty_index');
        
        $view2->set('_err', $err);
        $view2->set('res', $isf->DbSelect('przedmioty', array('przedmiot'), 'order by przedmiot asc'));
        
        $view->set('content', $view2->render());
        echo $view->render();
    }

    public function action_usun($przedmiot, $usun=null) {

        $isf = new Isf();
        $isf->DbConnect();

        if ($usun == null) {
            $isf->JQUi();
            $view = View::factory('main');
            $view->set('script', $isf->JQUi_MakeScript());
            $view2 = view::factory('przedmioty_usun');
            $view2->set('przedmiot', $przedmiot);
            $c = count($isf->DbSelect('przedmiot_sale', array('sala'), 'where przedmiot="' . $przedmiot . '"'));
            if ($c == 0) {
                $view2->set('ilosc_sal', 0);
            } else {
                $view2->set('ilosc_sal', $c);
            }
            $view2->set('sala_przedm', $isf->DbSelect('przedmiot_sale', array('sala'), 'where przedmiot="' . $przedmiot . '"'));

            $view->set('content', $view2->render());
            echo $view->render();
        } else {
            $isf->DbDelete('przedmioty', 'przedmiot="' . $przedmiot . '"');
            $isf->DbDelete('przedmiot_sale', 'przedmiot="' . $przedmiot . '"');
            $isf->DbDelete('nl_przedm', 'przedmiot="' . $przedmiot . '"');
            Kohana_Request::factory()->redirect('przedmioty/index');
        }
    }

    public function action_dodaj() {
        if (isset($_POST)) {

            $isf = new Kohana_Isf();
            $isf->DbConnect();

            if (count($isf->DbSelect('przedmioty', array('*'), 'where przedmiot="' . $_POST['inpPrzedmiot'] . '"')) != 0) {
                Kohana_Request::factory()->redirect('przedmioty/index/e1');
                exit;
            }

            $m = preg_match('/([.!@;#$%^&*()_+|])/i', $_POST['inpPrzedmiot']);

            if ($m == true) {
                Kohana_Request::factory()->redirect('przedmioty/index/e2');
                exit;
            }

            if ($_POST['inpPrzedmiot'] == '' || $_POST['inpPrzedmiot'] == null || empty($_POST['inpPrzedmiot'])) {
                Kohana_Request::factory()->redirect('przedmioty/index/e3');
                exit;
            }

            $isf->DbInsert('przedmioty', array(
                'przedmiot' => $_POST['inpPrzedmiot'],
            ));

            Kohana_Request::factory()->redirect('przedmioty/index/pass');
        } else {
            Kohana_Request::factory()->redirect('');
        }
    }

    public function action_sale($przedmiot) {
        $isf = new Kohana_Isf();
        $isf->DbConnect();
        $isf->JQUi();
        $view = view::factory('main');
        $view2 = view::factory('przedmioty_sale');

        $res = $isf->DbSelect('przedmiot_sale', array('sala'), 'where przedmiot="' . $przedmiot . '" order by cast(sala as numeric) asc');
        $sale_res = $isf->DbSelect('sale', array('sala'), 'except select sala from przedmiot_sale where przedmiot="' . $przedmiot . '" order by sala asc');

        $view2->set('przedmiot', $przedmiot);
        $view2->set('c', count($res));
        $view2->set('res', $res);
        $view2->set('sale', $sale_res);
        $view->set('script', $isf->JQUi_MakeScript());
        $view->set('content', $view2->render());
        echo $view->render();
    }

    public function action_dodajsale() {
        $przedmiot = $_POST['formPrzedmiot'];
        $sala = $_POST['selSale'];
        $isf = new Kohana_Isf();
        $isf->DbConnect();
        $isf->DbInsert('przedmiot_sale', array(
            'przedmiot' => $przedmiot,
            'sala' => $sala,
        ));
        Kohana_Request::factory()->redirect('przedmioty/sale/' . $przedmiot);
    }

    public function action_przypisusun($przedmiot, $sala) {
        $isf = new Kohana_Isf();
        $isf->DbConnect();
        $isf->DbDelete('przedmiot_sale', 'przedmiot="' . $przedmiot . '" and sala="' . $sala . '"');
        Kohana_Request::factory()->redirect('przedmioty/sale/' . $przedmiot);
    }

    public function action_zarzadzanie($przedmiot) {
        $view = view::factory('main');
        $view2 = view::factory('przedmioty_zarzadzanie');

        $view2->set('przedmiot', $przedmiot);

        $view->set('content', $view2->render());
        echo $view->render();
    }

    public function action_wypisz($przedmiot, $nauczyciel) {
        $isf = new Kohana_Isf();
        $isf->DbConnect();
        $isf->DbDelete('nl_przedm', 'nauczyciel="' . $nauczyciel . '" and przedmiot="' . $przedmiot . '"');
        Kohana_Request::factory()->redirect('przedmioty/zarzadzanie/' . $przedmiot);
    }

    public function action_nlprzyp() {
        $nauczyciel = $_POST['selNaucz'];
        $przedm = $_POST['przedmiot'];
        $isf = new Kohana_Isf();
        $isf->DbConnect();
        $isf->DbInsert('nl_przedm', array(
            'nauczyciel' => $nauczyciel,
            'przedmiot' => $przedm,
        ));
        Kohana_Request::factory()->redirect('przedmioty/zarzadzanie/' . $przedm);
    }

}