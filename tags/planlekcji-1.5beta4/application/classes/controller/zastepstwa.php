<?php

/**
 * Intersys - Plan Lekcji
 * 
 * 
 * @author Michał Bocian <mhl.bocian@gmail.com>
 */
defined('SYSPATH') or die('No direct script access.');

/**
 * Kontroler: zastepstwa
 * 
 * Rola: Odpowiada za obsługę zastepstw
 */
class Controller_Zastepstwa extends Controller {

    public function action_index() {
        session_start();
        $view = view::factory('main');
        $view2 = view::factory('zastepstwa_index');

        $view->set('content', $view2->render());
        echo $view->render();
    }

    public function checklogin() {
        session_start();
        if (!isset($_SESSION['valid']) || !isset($_COOKIE['PHPSESSID'])) {
            Kohana_Request::factory()->redirect('admin/login');
            exit;
        }
        $isf = new Kohana_Isf();
        $isf->DbConnect();
        $reg = $isf->DbSelect('rejestr', array('*'), 'where opcja="edycja_danych"');
        if ($reg[1]['wartosc'] != 3) {
            echo '<h1>Edycja danych nie zostala zamknieta</h1>';
            exit;
        }
    }

    public function action_edycja($blad=false) {

        $this->checklogin();
        $isf = new Kohana_Isf();
        $isf->JQUi();
        $isf->JQUi_CustomFunction('$("#inpDate").datepicker({beforeShowDay: $.datepicker.noWeekends, "dateFormat": "yy-mm-dd"});');

        $view = view::factory('main');
        $view2 = view::factory('zastepstwa_edycja');
        $view2->set('blad', $blad);

        $view->set('script', $isf->JQUi_MakeScript());
        $view->set('content', $view2->render());
        echo $view->render();
    }

    public function action_wypeln() {
        $this->checklogin();
        if (!isset($_POST)) {
            Request::factory()->redirect('default/index');
            exit;
        }

        if (empty($_POST['inpDate']) || empty($_POST['selNl'])) {
            Request::factory()->redirect('zastepstwa/edycja/true');
            exit;
        }

        $enpl_days = array(
            'Monday' => 'Poniedziałek',
            'Tuesday' => 'Wtorek',
            'Wednesday' => 'Środa',
            'Thursday' => 'Czwartek',
            'Friday' => 'Piątek',
            'Saturday' => 'Sobota',
            'Sunday' => 'Niedziela',
        );
        $day = date('l', strtotime($_POST['inpDate']));

        if ($enpl_days[$day] == 'Sobota' || $enpl_days[$day] == 'Niedziela') {
            Request::factory()->redirect('zastepstwa/edycja/day');
            exit;
        }

        $date = strtotime(date('Y-m-d'));
        if ($date > strtotime($_POST['inpDate'])) {
            Request::factory()->redirect('zastepstwa/edycja/data');
            exit;
        }

        $view = view::factory('main');
        $view2 = view::factory('zastepstwa_wypeln');

        if (!isset($_POST['inpComment'])) {
            $_POST['inpComment'] == '';
        }

        $view2->set('dzien', $enpl_days[$day]);
        $view2->set('nauczyciel', $_POST['selNl']);
        $view2->set('data', $_POST['inpDate']);
        $view2->set('komentarz', $_POST['inpComment']);

        $view->set('content', $view2->render());
        echo $view->render();
    }

    public function action_przeglad($id) {
        session_start();
        $view = view::factory('main');
        $view2 = view::factory('zastepstwa_przeglad');

        $isf = new Kohana_Isf();
        $isf->DbConnect();
        $res = $isf->DbSelect('zast_id', array('*'), 'where zast_id=\'' . $id . '\'');

        $enpl_days = array(
            'Monday' => 'Poniedziałek',
            'Tuesday' => 'Wtorek',
            'Wednesday' => 'Środa',
            'Thursday' => 'Czwartek',
            'Friday' => 'Piątek',
            'Saturday' => 'Sobota',
            'Sunday' => 'Niedziela',
        );
        $day = date('l', strtotime($res[1]['dzien']));

        $view2->set('nauczyciel', $res[1]['za_nl']);
        $view2->set('data', $res[1]['dzien']);
        $view2->set('dzien', $enpl_days[$day]);
        $view2->set('komentarz', $res[1]['info']);
        $view2->set('zast_id', $id);

        $view->set('content', $view2->render());
        echo $view->render();
    }

    public function action_zatwierdz() {
        $this->checklogin();
        if (!isset($_POST)) {
            Request::factory()->redirect('default/index');
            exit;
        }

        $isf = new Kohana_Isf();
        $isf->DbConnect();

        $isf->DbInsert('zast_id', array(
            'dzien' => $_POST['dzien'],
            'za_nl' => $_POST['za_nl'],
            'info' => $_POST['info'],
        ));

        $id = $isf->DbSelect('zast_id', array('*'), 'where dzien=\'' . $_POST['dzien'] . '\' and za_nl=\'' . $_POST['za_nl'] . '\'');
        $id = $id[1]['zast_id'];

        foreach ($_POST['zast'] as $lekcja => $zast) {
            $str = explode(':', $zast);
            if (count($str) == 1) {
                $isf->DbInsert('zastepstwa', array(
                    'zast_id' => $id,
                    'lekcja' => $lekcja,
                    'przedmiot' => $zast,
                ));
            } else {
                $isf->DbInsert('zastepstwa', array(
                    'zast_id' => $id,
                    'lekcja' => $lekcja,
                    'przedmiot' => $str[0],
                    'nauczyciel' => $str[2],
                    'sala' => $str[1],
                ));
            }
        }

        Kohana_Request::factory()->redirect('zastepstwa/index');
    }
    
    public function action_usun($id){
        $this->checklogin();
        $isf = new Kohana_Isf();
        $isf->DbConnect();
        $isf->DbDelete('zast_id', 'zast_id=\''.$id.'\'');
        $isf->DbDelete('zastepstwa', 'zast_id=\''.$id.'\'');
        
        Kohana_Request::factory()->redirect('zastepstwa/index');
        
    }
    
    public function action_drukuj(){
        $this->checklogin();
        if(!isset($_POST)){
            Request::factory()->redirect('default/index');
            exit;
        }
        $view = View::factory('zastepstwa_drukuj');
        echo $view->render();
    }

}