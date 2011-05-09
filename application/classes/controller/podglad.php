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
        $view->set('klasa', $klasa);
        echo $view->render();
    }
    
}