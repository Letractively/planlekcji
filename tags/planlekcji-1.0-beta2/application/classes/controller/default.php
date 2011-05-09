<?php

defined('SYSPATH') or die('No direct script access.');

class Controller_Default extends Controller {

    public function __construct() {
        session_start();
    }
    
    public function action_index() {
        $isf = new Kohana_Isf();
        $isf->DbConnect();
        $view = View::factory('main');
        
        $content = $isf->DbSelect('rejestr', array('*'), 'where opcja="index_text"');
        $content = $content[1]['wartosc'];
        $view->set('content', $content);
        echo $view->render();
    }

}