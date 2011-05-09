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
 * Kontroler: plan
 * 
 * Rola: Odpowiada za obsługę planów zajęć
 */
class Controller_Plan extends Controller {

    public function __construct() {
        session_start();
        if (!isset($_SESSION['valid']) || !isset($_COOKIE['PHPSESSID'])) {
            Kohana_Request::factory()->redirect('admin/login');
            exit;
        }
        $isf = new Kohana_Isf();
        $isf->DbConnect();
        $reg = $isf->DbSelect('rejestr', array('*'), 'where opcja="edycja_danych"');
        if ($reg[1]['wartosc'] != 0) {
            echo '<h1>Edycja danych nie zostala zamknieta</h1>';
            exit;
        }
    }

    public function action_edycja($klasa) {
        $view = view::factory('plan_edycja');
        $view->set('klasa', $klasa);
        echo $view->render();
    }

    public function action_zatwierdz() {
        $isf = new Kohana_Isf();
        $isf->DbConnect();
        $klasa = $_POST['klasa'];
        $isf->DbDelete('plan_grupy', 'klasa="' . $klasa . '"');
        foreach ($_POST['Poniedziałek'] as $lek => $przedm) {
            if ($przedm == '---') {
                $isf->DbDelete('planlek', 'dzien="Poniedziałek" and lekcja="' . $lek . '" and klasa="' . $klasa . '"');
            } else {
                $przedm = explode(':', $przedm);
                $isf->DbDelete('planlek', 'dzien="Poniedziałek" and lekcja="' . $lek . '" and klasa="' . $klasa . '"');
                if (count($przedm) == 1) {
                    $colval = array(
                        'dzien' => 'Poniedziałek',
                        'klasa' => $klasa,
                        'lekcja' => $lek,
                        'przedmiot' => $przedm[0],
                    );
                } else {
                    $colval = array(
                        'dzien' => 'Poniedziałek',
                        'klasa' => $klasa,
                        'lekcja' => $lek,
                        'przedmiot' => $przedm[0],
                        'sala' => $przedm[1],
                        'nauczyciel' => $przedm[2],
                    );
                }
                $isf->DbInsert('planlek', $colval);
            }
        }

        foreach ($_POST['Wtorek'] as $lek => $przedm) {
            if ($przedm == '---') {
                $isf->DbDelete('planlek', 'dzien="Wtorek" and lekcja="' . $lek . '" and klasa="' . $klasa . '"');
            } else {
                $isf->DbDelete('planlek', 'dzien="Wtorek" and lekcja="' . $lek . '" and klasa="' . $klasa . '"');
                $przedm = explode(':', $przedm);
                if (count($przedm) == 1) {
                    $colval = array(
                        'dzien' => 'Wtorek',
                        'klasa' => $klasa,
                        'lekcja' => $lek,
                        'przedmiot' => $przedm[0],
                    );
                } else {
                    $colval = array(
                        'dzien' => 'Wtorek',
                        'klasa' => $klasa,
                        'lekcja' => $lek,
                        'przedmiot' => $przedm[0],
                        'sala' => $przedm[1],
                        'nauczyciel' => $przedm[2],
                    );
                }
                $isf->DbInsert('planlek', $colval);
            }
        }

        foreach ($_POST['Środa'] as $lek => $przedm) {
            if ($przedm == '---') {
                $isf->DbDelete('planlek', 'dzien="Środa" and lekcja="' . $lek . '" and klasa="' . $klasa . '"');
            } else {
                $isf->DbDelete('planlek', 'dzien="Środa" and lekcja="' . $lek . '" and klasa="' . $klasa . '"');
                $przedm = explode(':', $przedm);
                if (count($przedm) == 1) {
                    $colval = array(
                        'dzien' => 'Środa',
                        'klasa' => $klasa,
                        'lekcja' => $lek,
                        'przedmiot' => $przedm[0],
                    );
                } else {
                    $colval = array(
                        'dzien' => 'Środa',
                        'klasa' => $klasa,
                        'lekcja' => $lek,
                        'przedmiot' => $przedm[0],
                        'sala' => $przedm[1],
                        'nauczyciel' => $przedm[2],
                    );
                }
                $isf->DbInsert('planlek', $colval);
            }
        }

        foreach ($_POST['Czwartek'] as $lek => $przedm) {
            if ($przedm == '---') {
                $isf->DbDelete('planlek', 'dzien="Czwartek" and lekcja="' . $lek . '" and klasa="' . $klasa . '"');
            } else {
                $isf->DbDelete('planlek', 'dzien="Czwartek" and lekcja="' . $lek . '" and klasa="' . $klasa . '"');
                $przedm = explode(':', $przedm);
                if (count($przedm) == 1) {
                    $colval = array(
                        'dzien' => 'Czwartek',
                        'klasa' => $klasa,
                        'lekcja' => $lek,
                        'przedmiot' => $przedm[0],
                    );
                } else {
                    $colval = array(
                        'dzien' => 'Czwartek',
                        'klasa' => $klasa,
                        'lekcja' => $lek,
                        'przedmiot' => $przedm[0],
                        'sala' => $przedm[1],
                        'nauczyciel' => $przedm[2],
                    );
                }
                $isf->DbInsert('planlek', $colval);
            }
        }

        foreach ($_POST['Piątek'] as $lek => $przedm) {
            if ($przedm == '---') {
                $isf->DbDelete('planlek', 'dzien="Piątek" and lekcja="' . $lek . '" and klasa="' . $klasa . '"');
            } else {
                $isf->DbDelete('planlek', 'dzien="Piątek" and lekcja="' . $lek . '" and klasa="' . $klasa . '"');
                $przedm = explode(':', $przedm);
                if (count($przedm) == 1) {
                    $colval = array(
                        'dzien' => 'Piątek',
                        'klasa' => $klasa,
                        'lekcja' => $lek,
                        'przedmiot' => $przedm[0],
                    );
                } else {
                    $colval = array(
                        'dzien' => 'Piątek',
                        'klasa' => $klasa,
                        'lekcja' => $lek,
                        'przedmiot' => $przedm[0],
                        'sala' => $przedm[1],
                        'nauczyciel' => $przedm[2],
                    );
                }
                $isf->DbInsert('planlek', $colval);
            }
        }

        echo '<html><head><script type="text/javascript">window.close();</script></head><body><a href="' . URL::site('') . '">[ powrót ]</a></body></html>';
    }

    public function action_grupy() {
        $view = view::factory('main');
        $view2 = view::factory('plan_grupy');

        $view->set('content', $view2->render());
        echo $view->render();
    }

    public function action_grpedit() {
        if (isset($_POST)) {
            $klasa = $_POST['sKlasa'];
            $lekcja = $_POST['sLekcja'];
            $dzien = $_POST['sDzien'];
            $view = view::factory('main');
            $view2 = view::factory('plan_grpedit');

            $view2->set('dzien', $dzien);
            $view2->set('klasa', $klasa);
            $view2->set('lekcja', $lekcja);

            $isf = new Kohana_Isf();
            $isf->JQUi();
            $view->set('script', $isf->JQUi_MakeScript());
            $view->set('content', $view2->render());
            echo $view->render();
        } else {
            Kohana_Request::factory()->redirect('');
        }
    }

    public function action_grpplan() {
        if (isset($_POST)) {
            $isf = new Kohana_Isf();
            $isf->DbConnect();
            $klasa = $_POST['klasa'];
            $dzien = $_POST['dzien'];
            $lekcja = $_POST['lekcja'];
            $isf->DbDelete('plan_grupy', 'dzien="' . $dzien . '" and klasa="' . $klasa . '" and lekcja="' . $lekcja . '"');
            $isf->DbDelete('planlek', 'dzien="' . $dzien . '" and klasa="' . $klasa . '" and lekcja="' . $lekcja . '"');
            foreach ($_POST['grupa'] as $id => $val) {
                if ($val == '---') {
                    
                } else {
                    $val = explode(':', $val);
                    if (count($val) == 1) {
                        $col_val = array(
                            'dzien' => $dzien,
                            'klasa' => $klasa,
                            'lekcja' => $lekcja,
                            'grupa' => $id,
                            'przedmiot' => $val[0],
                        );
                    } else {
                        $col_val = array(
                            'dzien' => $dzien,
                            'lekcja' => $lekcja,
                            'klasa' => $klasa,
                            'grupa' => $id,
                            'przedmiot' => $val[0],
                            'sala' => $val[1],
                            'nauczyciel' => $val[2],
                        );
                    }
                    $isf->DbInsert('plan_grupy', $col_val);

                }
            }
        }
        Kohana_Request::factory()->redirect('plan/grupy');
    }

    public function action_grpdel($dzien, $lekcja, $klasa, $grupa) {
        $isf = new Kohana_Isf();
        $isf->DbConnect();
        $isf->DbDelete('plan_grupy', 'dzien="' . $dzien . '" and lekcja="' . $lekcja . '" and klasa="' . $klasa . '" and grupa="' . $grupa . '"');
        Kohana_Request::factory()->redirect('plan/grupy');
    }

}