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
 * Rejestr systemowy
 * 
 * @package regedit
 */
class Controller_Regedit extends Controller {
    /**
     *
     * @var nusoap_client instancja klasy NuSOAP
     */
    public $wsdl;

    /**
     * Tworzy obiekt sesji i sprawdza czy zalogowany
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
            if ($auth == 'auth:failed' || $_SESSION['user'] != 'root') {
                Kohana_Request::factory()->redirect('admin/login');
                exit;
            }
        }
    }
    /**
     * Strona glowna rejestru
     */
    public function action_index() {
        $view = view::factory('main');
        $view2 = view::factory('regedit_index');

        $view->set('content', $view2->render());
        echo $view->render();
    }

}

?>