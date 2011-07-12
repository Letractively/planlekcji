<?php
/**
 * Intersys - Plan Lekcji
 * 
 * 
 * @author Michał Bocian <mhl.bocian@gmail.com>
 */
defined('SYSPATH') or die('No direct script access.');
/**
 * Kontroler: sale
 * 
 * Rola: Odpowiada za obsługę sal lekcyjnych
 */
class Controller_Sale extends Controller {

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
        $reg = $isf->DbSelect('rejestr', array('*'), 'where opcja="edycja_danych"');
        /**
         * Czy mozna edytowac dane
         */
        if ($reg[1]['wartosc'] != 1) {
            echo '<h1>Edycja danych zostala zamknieta</h1>';
            exit;
        }
    }

    public function action_index($err=null) {
        $isf = new Isf();
        $isf->DbConnect();

        $view = View::factory('main');

        $dbres = $isf->DbSelect('sale', array('*'), 'order by cast(sala as numeric) asc');

        $view2 = View::factory('sale_index');
        $view2->set('res', $dbres);
        $view2->set('_err', $err);


        $view->set('bodystr', 'onLoad="document.forms.form1.inpSala.focus()"');
        $view->set('content', $view2->render());
        echo $view->render();
    }

    public function action_usun($sala, $usun=null) {

        $isf = new Isf();
        $isf->DbConnect();

        if ($usun == null) {

            $view = View::factory('main');
            $view2 = view::factory('sale_usun');

            $view2->set('sala', $sala);

            $c = count($isf->DbSelect('przedmiot_sale', array('przedmiot'), 'where sala="' . $sala . '"'));

            if ($c == 0) {
                $view2->set('ilosc_przed', 0);
            } else {
                $view2->set('ilosc_przed', $c);
            }

            $view2->set('sala_przedm', $isf->DbSelect('przedmiot_sale', array('przedmiot'), 'where sala="' . $sala . '"'));

            $view->set('content', $view2->render());
            echo $view->render();
        } else {
            $isf->DbDelete('sale', 'sala="' . $sala . '"');
            $isf->DbDelete('przedmiot_sale', 'sala="' . $sala . '"');
            Kohana_Request::factory()->redirect('sale/index/usun');
        }
    }

    public function action_dodaj() {
        if (isset($_POST)) {

            $isf = new Kohana_Isf();
            $isf->DbConnect();

            if (count($isf->DbSelect('sale', array('*'), 'where sala="' . $_POST['inpSala'] . '"')) != 0) {
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

    public function action_przedmiot($sala) {
        $isf = new Kohana_Isf();
        $isf->DbConnect();

        $view = view::factory('main');
        $view2 = view::factory('sale_przedmiot');

        $res = $isf->DbSelect('przedmiot_sale', array('*'), 'where sala="' . $sala . '" order by przedmiot asc');
        $prz_res = $isf->DbSelect('przedmioty', array('przedmiot'), 'except select przedmiot from przedmiot_sale where sala="' . $sala . '"');

        $view2->set('sala', $sala);
        $view2->set('c', count($res));
        $view2->set('res', $res);
        $view2->set('przed', $prz_res);

        $view->set('content', $view2->render());
        echo $view->render();
    }

    public function action_dodajprzedm() {
        $sala = $_POST['formSala'];
        $przedmiot = $_POST['selPrzed'];
        $isf = new Kohana_Isf();
        $isf->DbConnect();
        $isf->DbInsert('przedmiot_sale', array(
            'przedmiot' => $przedmiot,
            'sala' => $sala,
        ));
        Kohana_Request::factory()->redirect('sale/przedmiot/' . $sala);
    }

    public function action_przedusun($sala, $przedmiot) {
        $isf = new Kohana_Isf();
        $isf->DbConnect();
        $isf->DbDelete('przedmiot_sale', 'przedmiot="' . $przedmiot . '" and sala="' . $sala . '"');
        Kohana_Request::factory()->redirect('sale/przedmiot/' . $sala);
    }

}
