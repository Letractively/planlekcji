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
 * Odpowiada za obsluge planow zajec
 * 
 * @package plan
 */
class Controller_Plan extends Controller {

    /**
     *
     * @var nusoap_client instancja klasy nusoap
     */
    protected $wsdl;

    /**
     * Sprawdza zalogowanie uzytkownika
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
        $isf->DbConnect();
        $reg = $isf->DbSelect('rejestr', array('*'), 'where opcja=\'edycja_danych\'');
        if ($reg[1]['wartosc'] != 0) {
            echo '<h1>Edycja danych nie zostala zamknieta</h1>';
            exit;
        }
    }

    /**
     * Wyswietla edycje planu dla klasy AJAX
     *
     * @param string $klasa klasa
     */
    public function action_klasa($klasa) {
        $view = view::factory('plan_klasa');
        $view->set('klasa', $klasa);
        echo $view->render();
    }

    /**
     * Wyswietla plan grupowy dla klasy AJAX
     *
     * @param string $klasa klasa
     */
    public function action_grupy($klasa) {
        $view = view::factory('plan_grupy');
        $view->set('klasa', $klasa);
        echo $view->render();
    }

    /**
     * Wyswietla tresc strony dla wywolania AJAX
     * 
     * W przypadku przegladarki Internet Explorer wyswietlany jest ten
     * surowy szablon
     *
     * @param string $klasa klasa
     * @param boolean $alternative wyswietlanie klasycznej strony
     */
    public function action_klasaajax($klasa, $alternative=false) {
        $view = view::factory('plan_klasaajax');
        $view->set('alternative', $alternative);
        $view->set('klasa', $klasa);
        echo $view->render();
    }

    /**
     * Wyswietla tresc strony dla wywolania AJAX
     * 
     * W przypadku przegladarki Internet Explorer wyswietlany jest ten
     * surowy szablon
     * 
     * @param string $klasa klasa
     * @param boolean $alternative wyswietlanie klasycznej strony
     */
    public function action_grupaajax($klasa, $alternative=false) {
        $view = view::factory('plan_grupaajax');
        $view->set('alternative', $alternative);
        $view->set('klasa', $klasa);
        echo $view->render();
    }
    /**
     * Wprowadza zmiany do planu klasy
     */
    public function action_zatwierdz() {
        $isf = new Kohana_Isf();
        $isf->DbConnect();
        $klasa = $_POST['klasa'];
        $isf->DbDelete('plan_grupy', 'klasa=\'' . $klasa . '\'');
        foreach ($_POST['Poniedziałek'] as $lek => $przedm) {
            if ($przedm == '---') {
                $isf->DbDelete('planlek', 'dzien=\'Poniedziałek\' and lekcja=\'' . $lek . '\' and klasa=\'' . $klasa . '\'');
            } else {
                $przedm = explode(':', $przedm);
                $isf->DbDelete('planlek', 'dzien=\'Poniedziałek\' and lekcja=\'' . $lek . '\' and klasa=\'' . $klasa . '\'');
                if (count($przedm) == 1) {
                    $colval = array(
                        'dzien' => 'Poniedziałek',
                        'klasa' => $klasa,
                        'lekcja' => $lek,
                        'przedmiot' => $przedm[0],
                    );
                } else {
                    $nl_s = $isf->DbSelect('nauczyciele', array('skrot'), 'where imie_naz=\'' . $przedm[2] . '\'');
                    $nl_s = $nl_s[1]['skrot'];
                    $colval = array(
                        'dzien' => 'Poniedziałek',
                        'klasa' => $klasa,
                        'lekcja' => $lek,
                        'przedmiot' => $przedm[0],
                        'sala' => $przedm[1],
                        'nauczyciel' => $przedm[2],
                        'skrot' => $nl_s,
                    );
                }
                $isf->DbInsert('planlek', $colval);
            }
        }

        foreach ($_POST['Wtorek'] as $lek => $przedm) {
            if ($przedm == '---') {
                $isf->DbDelete('planlek', 'dzien=\'Wtorek\' and lekcja=\'' . $lek . '\' and klasa=\'' . $klasa . '\'');
            } else {
                $isf->DbDelete('planlek', 'dzien=\'Wtorek\' and lekcja=\'' . $lek . '\' and klasa=\'' . $klasa . '\'');
                $przedm = explode(':', $przedm);
                if (count($przedm) == 1) {
                    $colval = array(
                        'dzien' => 'Wtorek',
                        'klasa' => $klasa,
                        'lekcja' => $lek,
                        'przedmiot' => $przedm[0],
                    );
                } else {
                    $nl_s = $isf->DbSelect('nauczyciele', array('skrot'), 'where imie_naz=\'' . $przedm[2] . '\'');
                    $nl_s = $nl_s[1]['skrot'];
                    $colval = array(
                        'dzien' => 'Wtorek',
                        'klasa' => $klasa,
                        'lekcja' => $lek,
                        'przedmiot' => $przedm[0],
                        'sala' => $przedm[1],
                        'nauczyciel' => $przedm[2],
                        'skrot' => $nl_s,
                    );
                }
                $isf->DbInsert('planlek', $colval);
            }
        }

        foreach ($_POST['Środa'] as $lek => $przedm) {
            if ($przedm == '---') {
                $isf->DbDelete('planlek', 'dzien=\'Środa\' and lekcja=\'' . $lek . '\' and klasa=\'' . $klasa . '\'');
            } else {
                $isf->DbDelete('planlek', 'dzien=\'Środa\' and lekcja=\'' . $lek . '\' and klasa=\'' . $klasa . '\'');
                $przedm = explode(':', $przedm);
                if (count($przedm) == 1) {
                    $colval = array(
                        'dzien' => 'Środa',
                        'klasa' => $klasa,
                        'lekcja' => $lek,
                        'przedmiot' => $przedm[0],
                    );
                } else {
                    $nl_s = $isf->DbSelect('nauczyciele', array('skrot'), 'where imie_naz=\'' . $przedm[2] . '\'');
                    $nl_s = $nl_s[1]['skrot'];
                    $colval = array(
                        'dzien' => 'Środa',
                        'klasa' => $klasa,
                        'lekcja' => $lek,
                        'przedmiot' => $przedm[0],
                        'sala' => $przedm[1],
                        'nauczyciel' => $przedm[2],
                        'skrot' => $nl_s,
                    );
                }
                $isf->DbInsert('planlek', $colval);
            }
        }

        foreach ($_POST['Czwartek'] as $lek => $przedm) {
            if ($przedm == '---') {
                $isf->DbDelete('planlek', 'dzien=\'Czwartek\' and lekcja=\'' . $lek . '\' and klasa=\'' . $klasa . '\'');
            } else {
                $isf->DbDelete('planlek', 'dzien=\'Czwartek\' and lekcja=\'' . $lek . '\' and klasa=\'' . $klasa . '\'');
                $przedm = explode(':', $przedm);
                if (count($przedm) == 1) {
                    $colval = array(
                        'dzien' => 'Czwartek',
                        'klasa' => $klasa,
                        'lekcja' => $lek,
                        'przedmiot' => $przedm[0],
                    );
                } else {
                    $nl_s = $isf->DbSelect('nauczyciele', array('skrot'), 'where imie_naz=\'' . $przedm[2] . '\'');
                    $nl_s = $nl_s[1]['skrot'];
                    $colval = array(
                        'dzien' => 'Czwartek',
                        'klasa' => $klasa,
                        'lekcja' => $lek,
                        'przedmiot' => $przedm[0],
                        'sala' => $przedm[1],
                        'nauczyciel' => $przedm[2],
                        'skrot' => $nl_s,
                    );
                }
                $isf->DbInsert('planlek', $colval);
            }
        }

        foreach ($_POST['Piątek'] as $lek => $przedm) {
            if ($przedm == '---') {
                $isf->DbDelete('planlek', 'dzien=\'Piątek\' and lekcja=\'' . $lek . '\' and klasa=\'' . $klasa . '\'');
            } else {
                $isf->DbDelete('planlek', 'dzien=\'Piątek\' and lekcja=\'' . $lek . '\' and klasa=\'' . $klasa . '\'');
                $przedm = explode(':', $przedm);
                if (count($przedm) == 1) {
                    $colval = array(
                        'dzien' => 'Piątek',
                        'klasa' => $klasa,
                        'lekcja' => $lek,
                        'przedmiot' => $przedm[0],
                    );
                } else {
                    $nl_s = $isf->DbSelect('nauczyciele', array('skrot'), 'where imie_naz=\'' . $przedm[2] . '\'');
                    $nl_s = $nl_s[1]['skrot'];
                    $colval = array(
                        'dzien' => 'Piątek',
                        'klasa' => $klasa,
                        'lekcja' => $lek,
                        'przedmiot' => $przedm[0],
                        'sala' => $przedm[1],
                        'nauczyciel' => $przedm[2],
                        'skrot' => $nl_s,
                    );
                }
                $isf->DbInsert('planlek', $colval);
            }
        }

        echo '<html><head><script type=\'text/javascript\'>window.close();</script></head><body><a href=\'' . URL::site('') . '\'>[ powrót ]</a></body></html>';
    }
    /**
     * Wprowadza zmiany do planu grupowego
     */
    public function action_grupazatw() {
        $isf = new Kohana_Isf();
        $isf->DbConnect();
        $klasa = $_POST['klasa'];
        $isf->DbDelete('plan_grupy', 'klasa=\'' . $klasa . '\'');
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
                        $nl_s = $isf->DbSelect('nauczyciele', array('skrot'), 'where imie_naz=\'' . $przedm[2] . '\'');
                        $nl_s = $nl_s[1]['skrot'];

                        $valid = $isf->DbSelect('plan_grupy', array('*'), 'where dzien=\'Poniedziałek\' and lekcja=\'' . $lek . '\' and nauczyciel=\'' . $przedm[2] . '\' and sala!=\'' . $przedm[1] . '\'');
                        if (count($valid) > 0) {
                            echo 'Nauczyciel ' . $przedm[2] . ' prowadzi juz zajecia w poniedziałek na lekcji ' . $lek . ' w sali ' . $przedm[1];
                            exit;
                        }

                        $colval = array(
                            'dzien' => 'Poniedziałek',
                            'klasa' => $klasa,
                            'lekcja' => $lek,
                            'grupa' => $grupa,
                            'przedmiot' => $przedm[0],
                            'sala' => $przedm[1],
                            'nauczyciel' => $przedm[2],
                            'skrot' => $nl_s,
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
                        $nl_s = $isf->DbSelect('nauczyciele', array('skrot'), 'where imie_naz=\'' . $przedm[2] . '\'');
                        $nl_s = $nl_s[1]['skrot'];

                        $valid = $isf->DbSelect('plan_grupy', array('*'), 'where dzien=\'Wtorek\' and lekcja=\'' . $lek . '\' and nauczyciel=\'' . $przedm[2] . '\' and sala!=\'' . $przedm[1] . '\'');
                        if (count($valid) > 0) {
                            echo 'Nauczyciel ' . $przedm[2] . ' prowadzi juz zajecia we wtorek na lekcji ' . $lek . ' w sali ' . $przedm[1];
                            exit;
                        }

                        $colval = array(
                            'dzien' => 'Wtorek',
                            'klasa' => $klasa,
                            'lekcja' => $lek,
                            'grupa' => $grupa,
                            'przedmiot' => $przedm[0],
                            'sala' => $przedm[1],
                            'nauczyciel' => $przedm[2],
                            'skrot' => $nl_s,
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
                        $nl_s = $isf->DbSelect('nauczyciele', array('skrot'), 'where imie_naz=\'' . $przedm[2] . '\'');
                        $nl_s = $nl_s[1]['skrot'];

                        $valid = $isf->DbSelect('plan_grupy', array('*'), 'where dzien=\'Środa\' and lekcja=\'' . $lek . '\' and nauczyciel=\'' . $przedm[2] . '\' and sala!=\'' . $przedm[1] . '\'');
                        if (count($valid) > 0) {
                            echo 'Nauczyciel ' . $przedm[2] . ' prowadzi juz zajecia w środę na lekcji ' . $lek . ' w sali ' . $przedm[1];
                            exit;
                        }

                        $colval = array(
                            'dzien' => 'Środa',
                            'klasa' => $klasa,
                            'lekcja' => $lek,
                            'grupa' => $grupa,
                            'przedmiot' => $przedm[0],
                            'sala' => $przedm[1],
                            'nauczyciel' => $przedm[2],
                            'skrot' => $nl_s,
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
                        $nl_s = $isf->DbSelect('nauczyciele', array('skrot'), 'where imie_naz=\'' . $przedm[2] . '\'');
                        $nl_s = $nl_s[1]['skrot'];

                        $valid = $isf->DbSelect('plan_grupy', array('*'), 'where dzien=\'Czwartek\' and lekcja=\'' . $lek . '\' and nauczyciel=\'' . $przedm[2] . '\' and sala!=\'' . $przedm[1] . '\'');
                        if (count($valid) > 0) {
                            echo 'Nauczyciel ' . $przedm[2] . ' prowadzi juz zajecia w czwartek na lekcji ' . $lek . ' w sali ' . $przedm[1];
                            exit;
                        }

                        $colval = array(
                            'dzien' => 'Czwartek',
                            'klasa' => $klasa,
                            'lekcja' => $lek,
                            'grupa' => $grupa,
                            'przedmiot' => $przedm[0],
                            'sala' => $przedm[1],
                            'nauczyciel' => $przedm[2],
                            'skrot' => $nl_s,
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
                        $nl_s = $isf->DbSelect('nauczyciele', array('skrot'), 'where imie_naz=\'' . $przedm[2] . '\'');
                        $nl_s = $nl_s[1]['skrot'];

                        $valid = $isf->DbSelect('plan_grupy', array('*'), 'where dzien=\'Piątek\' and lekcja=\'' . $lek . '\' and nauczyciel=\'' . $przedm[2] . '\' and sala!=\'' . $przedm[1] . '\'');
                        if (count($valid) > 0) {
                            echo 'Nauczyciel ' . $przedm[2] . ' prowadzi juz zajecia w piątek na lekcji ' . $lek . ' w sali ' . $przedm[1];
                            exit;
                        }

                        $colval = array(
                            'dzien' => 'Piątek',
                            'klasa' => $klasa,
                            'lekcja' => $lek,
                            'grupa' => $grupa,
                            'przedmiot' => $przedm[0],
                            'sala' => $przedm[1],
                            'nauczyciel' => $przedm[2],
                            'skrot' => $nl_s,
                        );
                    }
                    $isf->DbInsert('plan_grupy', $colval);
                }
            }
        }

        echo '<html><head><script type=\'text/javascript\'>window.close();</script></head><body><a href=\'' . URL::site('') . '\'>[ powrót ]</a></body></html>';
    }

}