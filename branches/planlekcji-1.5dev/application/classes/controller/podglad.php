<?php
/**
 * Intersys - Plan Lekcji
 * 
 * 
 * @author Michał Bocian <mhl.bocian@gmail.com>
 */
defined('SYSPATH') or die('No direct script access.');
/**
 * Kontroler: podglad
 * 
 * Rola: Odpowiada za wyswietlanie danych
 */
class Controller_Podglad extends Controller {
    
    public function __construct() {
        session_start();
    }
    
    public function action_klasa($klasa){
        $view = view::factory('podglad_klasa');
        $view->set('klasa', $klasa);
        echo $view->render();
    }
    
    public function action_sala($klasa){
        $view = view::factory('podglad_sala');
        $view->set('klasa', $klasa);
        echo $view->render();
    }
    
    public function action_nauczyciel($klasa)
    {
        $view = view::factory('podglad_nauczyciel');
        
        $isf = new Kohana_Isf();
        $isf->DbConnect();
        
        $imienaz = $isf->DbSelect('nauczyciele', array('*'), 'where skrot=\''.$klasa.'\'');
        $imienaz = $imienaz[1]['imie_naz'];
        
        $view->set('skrot', $klasa);
        $view->set('klasa', $imienaz);
        echo $view->render();
    }
    
    public function action_zestawienie(){
        $view = View::factory('podglad_zestawienie');
        echo $view->render();
    }
    
}