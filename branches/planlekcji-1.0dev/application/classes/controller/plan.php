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

    public function action_klasa($klasa) {
        $view = view::factory('plan_klasa');
        $view->set('klasa', $klasa);
        echo $view->render();
    }

    public function action_grupy($klasa) {
        $view = view::factory('plan_grupy');
        $view->set('klasa', $klasa);
        echo $view->render();
    }

    public function action_klasaajax($klasa) {
        $view = view::factory('plan_klasaajax');
        $view->set('klasa', $klasa);
        echo $view->render();
    }

    public function action_grupaajax($klasa) {
        $view = view::factory('plan_grupaajax');
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

    public function action_grupazatw() {
        $isf = new Kohana_Isf();
        $isf->DbConnect();
        $klasa = $_POST['klasa'];
        $isf->DbDelete('plan_grupy', 'klasa="' . $klasa . '"');
        foreach ($_POST['Poniedziałek'] as $lek => $przedlek) {

            foreach ($przedlek as $grupa => $przedm) {
                if ($przedm != '---') {
                    $przedm = explode(':', $przedm);
                    if (count($przedm) == 1) {
                        $colval = array(
                            'dzien' => 'Poniedziałek',
                            'klasa' => $klasa,
                            'lekcja' => $lek,
                            'grupa' => $grupa,
                            'przedmiot' => $przedm[0],
                        );
                    } else {
                        $colval = array(
                            'dzien' => 'Poniedziałek',
                            'klasa' => $klasa,
                            'lekcja' => $lek,
                            'grupa' => $grupa,
                            'przedmiot' => $przedm[0],
                            'sala' => $przedm[1],
                            'nauczyciel' => $przedm[2],
                        );
                    }
                    $isf->DbInsert('plan_grupy', $colval);
                }
            }

        }
        
        foreach ($_POST['Wtorek'] as $lek => $przedlek) {

            foreach ($przedlek as $grupa => $przedm) {
                if ($przedm != '---') {
                    $przedm = explode(':', $przedm);
                    if (count($przedm) == 1) {
                        $colval = array(
                            'dzien' => 'Wtorek',
                            'klasa' => $klasa,
                            'lekcja' => $lek,
                            'grupa' => $grupa,
                            'przedmiot' => $przedm[0],
                        );
                    } else {
                        $colval = array(
                            'dzien' => 'Wtorek',
                            'klasa' => $klasa,
                            'lekcja' => $lek,
                            'grupa' => $grupa,
                            'przedmiot' => $przedm[0],
                            'sala' => $przedm[1],
                            'nauczyciel' => $przedm[2],
                        );
                    }
                    $isf->DbInsert('plan_grupy', $colval);
                }
            }

        }
        
        foreach ($_POST['Środa'] as $lek => $przedlek) {

            foreach ($przedlek as $grupa => $przedm) {
                if ($przedm != '---') {
                    $przedm = explode(':', $przedm);
                    if (count($przedm) == 1) {
                        $colval = array(
                            'dzien' => 'Środa',
                            'klasa' => $klasa,
                            'lekcja' => $lek,
                            'grupa' => $grupa,
                            'przedmiot' => $przedm[0],
                        );
                    } else {
                        $colval = array(
                            'dzien' => 'Środa',
                            'klasa' => $klasa,
                            'lekcja' => $lek,
                            'grupa' => $grupa,
                            'przedmiot' => $przedm[0],
                            'sala' => $przedm[1],
                            'nauczyciel' => $przedm[2],
                        );
                    }
                    $isf->DbInsert('plan_grupy', $colval);
                }
            }

        }
        
        foreach ($_POST['Czwartek'] as $lek => $przedlek) {

            foreach ($przedlek as $grupa => $przedm) {
                if ($przedm != '---') {
                    $przedm = explode(':', $przedm);
                    if (count($przedm) == 1) {
                        $colval = array(
                            'dzien' => 'Czwartek',
                            'klasa' => $klasa,
                            'lekcja' => $lek,
                            'grupa' => $grupa,
                            'przedmiot' => $przedm[0],
                        );
                    } else {
                        $colval = array(
                            'dzien' => 'Czwartek',
                            'klasa' => $klasa,
                            'lekcja' => $lek,
                            'grupa' => $grupa,
                            'przedmiot' => $przedm[0],
                            'sala' => $przedm[1],
                            'nauczyciel' => $przedm[2],
                        );
                    }
                    $isf->DbInsert('plan_grupy', $colval);
                }
            }

        }
        
        foreach ($_POST['Piątek'] as $lek => $przedlek) {

            foreach ($przedlek as $grupa => $przedm) {
                if ($przedm != '---') {
                    $przedm = explode(':', $przedm);
                    if (count($przedm) == 1) {
                        $colval = array(
                            'dzien' => 'Piątek',
                            'klasa' => $klasa,
                            'lekcja' => $lek,
                            'grupa' => $grupa,
                            'przedmiot' => $przedm[0],
                        );
                    } else {
                        $colval = array(
                            'dzien' => 'Piątek',
                            'klasa' => $klasa,
                            'lekcja' => $lek,
                            'grupa' => $grupa,
                            'przedmiot' => $przedm[0],
                            'sala' => $przedm[1],
                            'nauczyciel' => $przedm[2],
                        );
                    }
                    $isf->DbInsert('plan_grupy', $colval);
                }
            }

        }

        echo '<html><head><script type="text/javascript">window.close();</script></head><body><a href="' . URL::site('') . '">[ powrót ]</a></body></html>';
    }

}