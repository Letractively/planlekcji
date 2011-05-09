<?php
defined('SYSPATH') or die('No direct script access.');

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