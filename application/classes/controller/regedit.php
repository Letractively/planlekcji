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
 * Kontroler: regedit
 * 
 * Rola: Rejestr systemowy
 */
class Controller_Regedit extends Controller {

    public function __construct() {
        session_start();
        if (!isset($_SESSION['valid']) || !isset($_COOKIE['PHPSESSID'])) {
            Kohana_Request::factory()->redirect('admin/login');
            exit;
        }
    }

    public function action_index() {
        $view = view::factory('main');
        $view2 = view::factory('regedit_index');

        $view->set('content', $view2->render());
        echo $view->render();
    }

}

?>