<?php
/**
 * Intersys - Plan Lekcji
 * 
 * 
 * @author Michał Bocian <mhl.bocian@gmail.com>
 */
defined('SYSPATH') or die('No direct script access.');
/**
 * Kontroler: default
 * 
 * Rola: Główny kontroler i domyślny podczas uruchomienia
 */
class Controller_Default extends Controller {
    /**
     * Tworzy obiekt sesji
     */
    public function __construct() {
        session_start();
    }
    /**
     * Wyswietal strone glowna
     */
    public function action_index() {
        $isf = new Kohana_Isf();
        $isf->DbConnect();
        $view = View::factory('main');
        
        $content = $isf->DbSelect('rejestr', array('*'), 'where opcja = \'index_text\'');
        $content = $content[1]['wartosc'];
        
        $view->set('content', $content);
        echo $view->render();
    }

}