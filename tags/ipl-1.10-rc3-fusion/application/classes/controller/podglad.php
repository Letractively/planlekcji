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
 * Odpowiada za wyswietlanie planow
 * 
 * @package podglad
 */
class Controller_Podglad extends Controller {

    public $head;

    /**
     * Wywoluje sesje
     */
    public function __construct() {
        $this->head = '
            <!DOCTYPE html>
            <html>
            <head>
            <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
            <title>Plan lekcji - ' . App_Globals::getRegistryKey('nazwa_szkoly') . '</title>
            <link rel="stylesheet" type="text/css" href="' . URL::base() . 'lib/css/style.css"/>
            <link rel="stylesheet" type="text/css" href="' . URL::base() . 'lib/css/themes/{{theme}}.css"/>
            </head>
            <body>
            ';
    }

    /**
     * Wyswietla plan dla klasy
     *
     * @param string $klasa 
     */
    public function action_klasa($klasa) {
        $vm = view::factory('main');
        $view = view::factory('podglad_klasa');
        $view->set('klasa', $klasa);

        $vm->set('content', $view->render());
        echo $vm->render();
    }

    /**
     * Wyswietla plan dla sali
     *
     * @param string $sala 
     */
    public function action_sala($sala) {

        $main = View::factory('main');

        $view = view::factory('podglad_sala');
        $view->set('klasa', $sala);

        $main->set('content', $view->render());

        echo $main->render();
    }

    /**
     * Wyswietla plan dla nauczyciela
     *
     * @param string $nauczyciel
     */
    public function action_nauczyciel($nauczyciel) {

        $main = View::factory('main');

        $view = view::factory('podglad_nauczyciel');

        $isf = new Kohana_Isf();
        $isf->Connect(APP_DBSYS);

        $imienaz = $isf->DbSelect('nauczyciele', array('*'), 'where skrot=\'' . $nauczyciel . '\'');
        $imienaz = $imienaz[0]['imie_naz'];

        $view->set('skrot', $nauczyciel);
        $view->set('klasa', $imienaz);

        $main->set('content', $view->render());
        echo $main->render();
    }

    /**
     * Wyswietla zestawienie
     */
    public function action_zestawienie() {
        $view = View::factory('podglad_zestawienie');
        $out = str_replace('{{theme}}', $_SESSION['app_theme'], $view->render());
        echo $out;
    }
    
    /**
     * Wyswietla plan w trybie generatora
     * 
     * Czysty plan z HTML bez reszty systemu,menu
     */
    public function action_zzestawienie() {
        $view = View::factory('podglad_zestawienie');
        echo $view->render();
    }

    /**
     * Wyswietla plan w trybie generatora
     * 
     * Czysty plan z HTML bez reszty systemu,menu
     */
    public function action_sklasa($klasa) {
        echo $this->head;
        $view = view::factory('podglad_klasa');
        $view->set('klasa', $klasa);
        echo $view->render();
        echo '</body></html>';
    }

    /**
     * Wyswietla plan w trybie generatora
     * 
     * Czysty plan z HTML bez reszty systemu,menu
     */
    public function action_ssala($klasa) {
        echo $this->head;
        $view = view::factory('podglad_sala');
        $view->set('klasa', $klasa);
        echo $view->render();
        echo '</body></html>';
    }

    /**
     * Wyswietla plan w trybie generatora
     * 
     * Czysty plan z HTML bez reszty systemu,menu
     */
    public function action_snauczyciel($nauczyciel) {
        echo $this->head;
        $view = view::factory('podglad_nauczyciel');
        
        $isf = new Kohana_Isf();
        $isf->Connect(APP_DBSYS);

        $imienaz = $isf->DbSelect('nauczyciele', array('*'), 'where skrot=\'' . $nauczyciel . '\'');
        $imienaz = $imienaz[0]['imie_naz'];

        $view->set('skrot', $nauczyciel);
        $view->set('klasa', $imienaz);
        
        echo $view->render();
        echo '</body></html>';
    }

}