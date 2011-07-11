<?php
/**
 * Intersys - Plan Lekcji
 * 
 * 
 * @author Michał Bocian <mhl.bocian@gmail.com>
 */
defined('SYSPATH') or die('No direct script access.');
/**
 * Kontroler: godziny
 * 
 * Rola: Odpowiada za dostęp moduł godzin lekcyjnych
 */
class Controller_Godziny extends Controller {
    
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
    /**
     * Strona godzin lekcyjnych
     */
    public function action_index() {
        $view = view::factory('main');
        $view2 = view::factory('godziny_index');

        $isf = new Kohana_Isf();
        $isf->DbConnect();
        $res = $isf->DbSelect('rejestr', array('*'), 'where opcja="ilosc_godzin_lek"');
        $isf->JQUi();
        for ($i = 1; $i <= $res[1]['wartosc']; $i++):
            $isf->JQUi_CustomFunction('$(\'#lekcja' . $i . '\').timepicker({showHour:false});');
        endfor;

        $view->set('script', $isf->JQUi_MakeScript());
        $view->set('content', $view2->render());
        echo $view->render();
    }
    /**
     * Ustawia ilosc godzin lekcyjnych
     */
    public function action_ustaw() {
        $isf = new Kohana_Isf();
        $isf->DbConnect();
        $ilosc = $_POST['iloscgodzin'];
        $isf->DbUpdate('rejestr', array('wartosc' => $ilosc), 'opcja="ilosc_godzin_lek"');
        $isf->DbUpdate('rejestr', array('wartosc' => $_POST['dlugosclekcji']), 'opcja="dlugosc_lekcji"');
        $isf->DbUpdate('lek_godziny', array('godzina'=>'wymagane jest ponowne ustawienie'), 'lekcja like "%"');
        Kohana_Request::factory()->redirect('godziny/index');
    }
    /**
     * Ustawia czas godzin lekcyjnych
     */
    public function action_lekcje() {
        $isf = new Kohana_Isf();
        $isf->DbConnect();
        $czaslek = $isf->DbSelect('rejestr', array('wartosc'), 'where opcja="dlugosc_lekcji"');
        $czaslek = $czaslek[1]['wartosc'];

        $isf->DbDelete('lek_godziny', 'lekcja like "%"');
        $g1;
        $g2;
        foreach ($_POST['lekcja'] as $nrlek => $dlprz) {
            if ($nrlek == 1) {
                $g1 = '08:00';
            } else {
                $g1 = explode(':', $g2);
                $nl = $nrlek-1;
                $cp = explode(':', $_POST['lekcja'][$nl]);
                $g1 = date('H:i', mktime($g1[0], $g1[1] + $cp[1]));
            }
            $g2 = explode(':', $g1);
            $g2 = date('H:i', mktime($g2[0], $g2[1] + $czaslek));
            $res = $g1 . ' - ' . $g2;
            $isf->DbInsert('lek_godziny', array(
                'lekcja'=>$nrlek,
                'godzina'=>$res,
                'dl_prz'=>$dlprz,
            ));
        }
        Kohana_Request::factory()->redirect('godziny/index');
    }

}