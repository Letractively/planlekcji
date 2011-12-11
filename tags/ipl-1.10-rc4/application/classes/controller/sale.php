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
        $isf = new Kohana_Isf();
	$isf->Connect(APP_DBSYS);
        $reg = $isf->DbSelect('rejestr', array('*'), 'where opcja=\'edycja_danych\'');
        /**
         * Czy mozna edytowac dane
         */
        if ($reg[0]['wartosc'] != 1) {
            echo '<h1>Edycja danych zostala zamknieta</h1>';
            exit;
        }
    }
    /**
     * Wyswietla strone glowna z salami
     *
     * @param string $err kod bledu
     */
    public function action_index($err=null) {
        $isf = new Isf();
        $isf->Connect(APP_DBSYS);

        $view = View::factory('main');

        $dbres = $isf->DbSelect('sale', array('*'), 'order by sala asc');

        $view2 = View::factory('sale_index');
        $view2->set('res', $dbres);
        $view2->set('_err', $err);


        $view->set('bodystr', 'onLoad=\'document.forms.form1.inpSala.focus()\'');
        $view->set('content', $view2->render());
        echo $view->render();
    }
    /**
     * Usuwa sale
     *
     * @param string $sala sala
     * @param mixed $usun czy usunac
     */
    public function action_usun($sala, $usun=null) {

        $isf = new Isf();
        $isf->Connect(APP_DBSYS);

        if ($usun == null) {

            $view = View::factory('main');
            $view2 = view::factory('sale_usun');

            $view2->set('sala', $sala);

            $c = count($isf->DbSelect('przedmiot_sale', array('przedmiot'), 'where sala=\'' . $sala . '\''));

            if ($c == 0) {
                $view2->set('ilosc_przed', 0);
            } else {
                $view2->set('ilosc_przed', $c);
            }

            $view2->set('sala_przedm', $isf->DbSelect('przedmiot_sale', array('przedmiot'), 'where sala=\'' . $sala . '\''));

            $view->set('content', $view2->render());
            echo $view->render();
        } else {
            $isf->DbDelete('sale', 'sala=\'' . $sala . '\'');
            $isf->DbDelete('przedmiot_sale', 'sala=\'' . $sala . '\'');
            Kohana_Request::factory()->redirect('sale/index/usun');
        }
    }
    /**
     * Dodaje sale
     */
    public function action_dodaj() {
        if (isset($_POST)) {

            $isf = new Kohana_Isf();
            $isf->Connect(APP_DBSYS);

            if (count($isf->DbSelect('sale', array('*'), 'where sala=\'' . $_POST['inpSala'] . '\'')) != 0) {
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

            $isf->DbInsert('sale', array('sala' => $_POST['inpSala']));
            Kohana_Request::factory()->redirect('sale/index/pass');
        }

        Kohana_Request::factory()->redirect('sale/index');
    }
    /**
     * Strona przypisania sali przedmiotow
     *
     * @param string $sala sala
     */
    public function action_przedmiot($sala) {
        $isf = new Kohana_Isf();
        $isf->Connect(APP_DBSYS);

        $view = view::factory('main');
        $view2 = view::factory('sale_przedmiot');

        $res = $isf->DbSelect('przedmiot_sale', array('*'), 'where sala=\'' . $sala . '\' order by przedmiot asc');
        $prz_res = $isf->DbSelect('przedmioty', array('przedmiot'), 'except select przedmiot from przedmiot_sale where sala=\'' . $sala . '\' order by przedmiot asc');

        $view2->set('sala', $sala);
        $view2->set('c', count($res));
        $view2->set('res', $res);
        $view2->set('przed', $prz_res);

        $view->set('content', $view2->render());
        echo $view->render();
    }
    /**
     * Dodaje do sali przedmiot
     */
    public function action_dodajprzedm() {
        $sala = $_POST['formSala'];
        $przedmiot = $_POST['selPrzed'];
        $isf = new Kohana_Isf();
        $isf->Connect(APP_DBSYS);
        $isf->DbInsert('przedmiot_sale', array(
            'przedmiot' => $przedmiot,
            'sala' => $sala,
        ));
        Kohana_Request::factory()->redirect('sale/przedmiot/' . $sala);
    }
    /**
     * Usuwa przypisanie sali przedmiotu
     *
     * @param string $sala sala
     * @param string $przedmiot przedmiot
     */
    public function action_przedusun($sala, $przedmiot) {
        $isf = new Kohana_Isf();
        $isf->Connect(APP_DBSYS);
        $isf->DbDelete('przedmiot_sale', 'przedmiot=\'' . $przedmiot . '\' and sala=\'' . $sala . '\'');
        Kohana_Request::factory()->redirect('sale/przedmiot/' . $sala);
    }

}
