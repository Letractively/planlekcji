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
    /**
     * Wywoluje sesje
     */
    public function __construct() {
        session_start();
    }
    /**
     * Wyswietla plan dla klasy
     *
     * @param string $klasa 
     */
    public function action_klasa($klasa){
        $view = view::factory('podglad_klasa');
        $view->set('klasa', $klasa);
        echo $view->render();
    }
    /**
     * Wyswietla plan dla sali
     *
     * @param string $sala 
     */
    public function action_sala($sala){
        $view = view::factory('podglad_sala');
        $view->set('klasa', $sala);
        echo $view->render();
    }
    /**
     * Wyswietla plan dla nauczyciela
     *
     * @param string $nauczyciel
     */
    public function action_nauczyciel($nauczyciel)
    {
        $view = view::factory('podglad_nauczyciel');
        
        $isf = new Kohana_Isf();
        $isf->DbConnect();
        
        $imienaz = $isf->DbSelect('nauczyciele', array('*'), 'where skrot=\''.$nauczyciel.'\'');
        $imienaz = $imienaz[1]['imie_naz'];
        
        $view->set('skrot', $nauczyciel);
        $view->set('klasa', $imienaz);
        echo $view->render();
    }
    /**
     * Wyswietla zestawienie
     */
    public function action_zestawienie(){
        $view = View::factory('podglad_zestawienie');
        echo $view->render();
    }
    
}