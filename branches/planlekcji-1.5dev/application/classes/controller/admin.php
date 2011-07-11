<?php

/**
 * Intersys - Plan Lekcji
 * 
 * @author Michał Bocian <mhl.bocian@gmail.com>
 */
defined('SYSPATH') or die('No direct script access.');

/**
 * Kontroler: admin
 * 
 * Rola: Odpowiada za dostęp do trybu administratora
 */
class Controller_Admin extends Controller {

    /**
     *
     * @var nusoap_client Obiekt klienta NuSOAP 
     */
    public $wsdl;
    /**
     *
     * @var string Czas waznosci tokena 
     */
    public $token_time;
    /**
     *
     * @var string Token uzytkownika 
     */
    public $token;

    /**
     * Konstruktor tworzy obiekt sesji
     */
    public function __construct() {
        session_start();
        try {
            $this->wsdl = new nusoap_client(URL::base('http') . 'webapi.php?wsdl');
        } catch (Exception $e) {
            echo $e->getMessage();
            exit;
        }
    }

    /**
     * Sprawdza zalogowanie uzytkownika root
     *
     * @return string Komunikat
     */
    public function check_login() {
        if (!isset($_SESSION['token'])) {
            return false;
            Kohana_Request::factory()->redirect('admin/login');
            exit;
        } else {
            $auth = $this->wsdl->call('doShowAuthTime', array('token' => $_SESSION['token']), 'webapi.planlekcji.isf');
            if ($auth == 'auth:failed') {
                return false;
                Kohana_Request::factory()->redirect('admin/login');
                exit;
            }
            if ($_SESSION['user'] != 'root') {
                echo '<h1>Dostep dla innych niz root zabroniony</h1>';
                exit;
            }
            return true;
        }
    }

    /**
     * Sprawdza zalogowanie uzytkownika
     *
     * @return string Komunikat
     */
    public function check_user_login() {
        if (!isset($_SESSION['token'])) {
            return false;
            Kohana_Request::factory()->redirect('admin/login');
            exit;
        } else {
            $auth = $this->wsdl->call('doShowAuthTime', array('token' => $_SESSION['token']), 'webapi.planlekcji.isf');
            if ($auth == 'auth:failed') {
                return false;
                Kohana_Request::factory()->redirect('admin/login');
                exit;
            }
            return true;
        }
    }

    /**
     * Akcja: index
     * Rola: uruchamia glowna strone
     */
    public function action_index() {
        if ($this->check_user_login() == true) {
            Request::factory()->redirect('');
            exit;
        } else {
            Kohana_Request::factory()->redirect('admin/login');
            exit;
        }
    }

    /**
     * Akcja: login
     * Rola: logowanie do systemu
     *
     * @param boolean $pass poprawnosc logowania
     */
    public function action_login($pass='') {

        if ($this->check_user_login() == true) {
            Request::factory()->redirect('');
            exit;
        }

        $view = view::factory('main');
        $view2 = view::factory('admin_login');

        if (isset($pass)) {
            $view2->set('pass', $pass);
        } else {
            $view2->set('pass', '');
        }

        $view->set('content', $view2->render());
        echo $view->render();
    }

    /**
     * Akcja: dologin
     * Rola: odpowiada za walidacje danych do logowania
     */
    public function action_dologin() {
        $login = $_POST['inpLogin'];
        $haslo = $_POST['inpHaslo'];
        insert_log('admin.login', 'Uzytkownik ' . $login . ' proboje sie zalogowac');
        if ($login != 'root') {
            $msg = $this->wsdl->call('doUserLogin', array('login' => $login, 'haslo' => $haslo, 'token' => $_POST['inpToken']), 'webapi.planlekcji.isf');
        } else {
            $msg = $this->wsdl->call('doLogin', array('login' => $login, 'haslo' => $haslo), 'webapi.planlekcji.isf');
        }
        if ($msg != 'auth:failed' && $msg != 'auth:locked') {
            $_SESSION['token'] = $msg;
            $_SESSION['user'] = $login;
            if (isset($_POST['inpToken']) && $login != 'root') {
                $_SESSION['usr_token'] = $_POST['inpToken'];
            }
            $_SESSION['token_time'] = $this->wsdl->call('doShowAuthTime', array('token' => $msg), 'webapi.planlekcji.isf');
            setcookie('login', $login, null, '/');
            insert_log('admin.login', 'Uzytkownik ' . $login . ' zalogowal sie');
            Kohana_Request::factory()->redirect('');
        } else {
            if ($msg == 'auth:locked') {
                Kohana_Request::factory()->post('inpLogin', $login);
                Kohana_Request::factory()->redirect('admin/login/locked');
            } else {
                Kohana_Request::factory()->post('inpLogin', $login);
                Kohana_Request::factory()->redirect('admin/login/false');
            }
        }
    }

    /**
     * Akcja: zamknij
     * Rola: strona zamkniecia edycji sal, przedmiotow, etc
     */
    public function action_zamknij() {

        $this->check_login();

        $view = view::factory('main');
        $view2 = view::factory('admin_zamknij');

        $view->set('content', $view2->render());
        echo $view->render();
    }

    /**
     * Akcja: zamknij2
     * Rola: strona zamkniecia edycji planow zajec
     */
    public function action_zamknij2() {

        $this->check_user_login();

        $view = view::factory('main');
        $view2 = view::factory('admin_zamknij2');

        $view->set('content', $view2->render());
        echo $view->render();
    }

    public function action_renew() {

        $this->check_user_login();
        insert_log('admin.renewtoken', 'Uzytkownik ' . $_SESSION['user'] . ' odnowil token');
        $this->wsdl->call('doRenewToken', array('token' => $_SESSION['token']), 'webapi.planlekcji.isf');
        $_SESSION['token_time'] = $this->wsdl->call('doShowAuthTime', array('token' => $_SESSION['token']), 'webapi.planlekcji.isf');

        Request::factory()->redirect('');
    }

    /**
     * Akcja: zamknijconfirm
     * Rola: potwierdza zamkniecie edycji sal, przedmiotow, etc
     */
    public function action_zamknijconfirm() {

        $this->check_login();
        if (isset($_POST)) {
            $isf = new Kohana_Isf();
            $isf->DbConnect();
            $isf->DbUpdate('rejestr', array('wartosc' => '0'), 'opcja="edycja_danych"');
            Kohana_Request::factory()->redirect('default/index');
        }
    }

    /**
     * Akcja: zamknijconfirm2
     * Rola: potwierdza zamkniecie edycji planow
     */
    public function action_zamknijconfirm2() {

        $this->check_user_login();
        if (isset($_POST)) {
            $isf = new Kohana_Isf();
            $isf->DbConnect();
            $isf->DbUpdate('rejestr', array('wartosc' => '3'), 'opcja="edycja_danych"');
            Kohana_Request::factory()->redirect('default/index');
        }
    }

    /**
     * Akcja: logout
     * Rola: wylogowuje
     */
    public function action_logout() {
        $this->wsdl->call('doLogout', array('token' => $_SESSION['token']), 'webapi.planlekcji.isf');
        unset($_SESSION['token']);
        setcookie('login', '', time() - 3600, '/');
        insert_log('admin.chpass', 'Uzytkownik ' . $_SESSION['user'] . ' wylogowuje sie');
        session_destroy();

        Kohana_Request::factory()->redirect('default/index');
    }

    /**
     * Akcja: planreset
     * Rola: strona usuwania planow
     */
    public function action_planreset() {

        $this->check_login();

        if (!isset($_POST)) {
            Kohana_Request::factory()->redirect('');
            exit;
        }
        $isf = new Kohana_Isf();
        $isf->DbConnect();
        $isf->DbDelete('planlek', 'klasa like "%"');
        $isf->DbDelete('plan_grupy', 'klasa like "%"');
        $isf->DbDelete('zast_id', 'zast_id like \'%\'');
        $isf->DbDelete('zastepstwa', 'zast_id like \'%\'');
        $isf->DbUpdate('rejestr', array('wartosc' => '0'), 'opcja="edycja_danych"');
        Kohana_Request::factory()->redirect('default/index');
    }

    /**
     * Akcja: reset
     * Rola: strona usuwania danych jak sale, etc
     */
    public function action_reset() {

        $this->check_login();

        $isf = new Kohana_Isf();
        $isf->DbConnect();
        $view = view::factory('main');
        $view2 = view::factory('admin_reset');

        $view->set('content', $view2->render());
        echo $view->render();
    }

    /**
     * Akcja: doreset
     * Rola: usuwa dane jak sale, etc
     */
    public function action_doreset() {

        $this->check_login();

        $isf = new Kohana_Isf();
        $isf->DbConnect();
        $isf->DbDelete('planlek', 'klasa like "%"');
        $isf->DbDelete('plan_grupy', 'klasa like "%"');
        $isf->DbDelete('zast_id', 'zast_id like \'%\'');
        $isf->DbDelete('zastepstwa', 'zast_id like \'%\'');
        $isf->DbUpdate('rejestr', array('wartosc' => '1'), 'opcja="edycja_danych"');
        if (isset($_POST['cl'])) {
            $isf->DbDelete('klasy', 'klasa like "%"');
            $isf->DbDelete('lek_godziny', 'lekcja like "%"');
            $isf->DbDelete('nauczyciele', 'imie_naz like "%"');
            $isf->DbDelete('nl_klasy', 'klasa like "%"');
            $isf->DbDelete('nl_przedm', 'przedmiot like "%"');
            $isf->DbDelete('przedmiot_sale', 'sala like "%"');
            $isf->DbDelete('przedmioty', 'przedmiot like "%"');
            $isf->DbDelete('sale', 'sala like "%"');
            $isf->DbUpdate('rejestr', array('wartosc' => '1'), 'opcja="ilosc_godzin_lek"');
        }
        Kohana_Request::factory()->redirect('');
    }

    /**
     * Akcja: zmiendane
     * Rola: strona zmiana danych szkoly, strony glownej
     */
    public function action_zmiendane() {

        $this->check_login();

        $url = substr(URL::base(), 0, -1);
        /**
         * Skrypt TinyMCE
         */
        $script = <<< START
<script type="text/javascript" src="$url/lib/tiny_mce/tiny_mce.js"></script>
<script type="text/javascript">
    tinyMCE.init({
        // General options
        mode : "textareas",
        theme : "advanced",
        plugins : "pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,wordcount,advlist,autosave",

        // Theme options
        theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect",
        theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
        theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
        theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak,restoredraft",
        theme_advanced_toolbar_location : "top",
        theme_advanced_toolbar_align : "left",
        theme_advanced_statusbar_location : "bottom",
        theme_advanced_resizing : true,

        // Example content CSS (should be your site CSS)
        // using false to ensure that the default browser settings are used for best Accessibility
        // ACCESSIBILITY SETTINGS
        content_css : false,
        // Use browser preferred colors for dialogs.
        browser_preferred_colors : true,
        detect_highcontrast : true,

        // Drop lists for link/image/media/template dialogs
        template_external_list_url : "lists/template_list.js",
        external_link_list_url : "lists/link_list.js",
        external_image_list_url : "lists/image_list.js",
        media_external_list_url : "lists/media_list.js",

        // Style formats
        style_formats : [
            {title : 'Bold text', inline : 'b'},
            {title : 'Red text', inline : 'span', styles : {color : '#ff0000'}},
            {title : 'Red header', block : 'h1', styles : {color : '#ff0000'}},
            {title : 'Example 1', inline : 'span', classes : 'example1'},
            {title : 'Example 2', inline : 'span', classes : 'example2'},
            {title : 'Table styles'},
            {title : 'Table row 1', selector : 'tr', classes : 'tablerow1'}
        ],

        // Replace values for the template plugin
        template_replace_values : {
            username : "Some User",
            staffid : "991234"
        }
    });
</script>
START;

        $view = view::factory('main');
        $view2 = view::factory('admin_zmiendane');

        $view->set('script', $script);
        $view->set('content', $view2->render());
        echo $view->render();
    }

    /**
     * Akcja: dochange
     * Rola: zmienia dane szkoly, strony glownej
     */
    public function action_dochange() {

        $this->check_login();

        if (!isset($_POST)) {
            Kohana_Request::factory()->redirect('');
            exit;
        }
        $nazwa = $_POST['inpNazwa'];
        $text = $_POST['txtMsg'];

        $isf = new Kohana_Isf();
        $isf->DbConnect();

        $isf->DbUpdate('rejestr', array('wartosc' => $nazwa), 'opcja="nazwa_szkoly"');
        $isf->DbUpdate('rejestr', array('wartosc' => $text), 'opcja="index_text"', false);

        Kohana_Request::factory()->redirect('default/index');
    }

    /**
     * Akcja: haslo
     * Rola: strona zmiany hasla
     */
    public function action_haslo($err=false) {

        $this->check_user_login();

        $view = view::factory('main');
        $view2 = view::factory('admin_haslo');

        if ($err != false) {
            $view2->set('_tplerr', $err);
        } else {
            $view2->set('_tplerr', '');
        }

        $view->set('content', $view2->render());
        echo $view->render();
    }

    /**
     * Akcja: chpass
     * Rola: zmienia haslo
     */
    public function action_chpass() {
        $this->check_user_login();

        insert_log('admin.chpass', 'Uzytkownik ' . $_SESSION['user'] . ' proboje zmienic haslo');

        if (isset($_POST)) {
            $isf = new Kohana_Isf();
            $isf->DbConnect();

            $s = $_POST['inpSH'];
            $n = $_POST['inpNH'];
            $p = $_POST['inpPH'];

            if (strlen($_POST['inpNH']) < 6) {
                Kohana_Request::factory()->redirect('admin/haslo/false');
                exit;
            }

            if ($n != $p) {
                Kohana_Request::factory()->redirect('admin/haslo/false');
                exit;
            }
            $arr['token'] = $_SESSION['token'];
            $arr['old'] = $s;
            $arr['new'] = $n;
            $act = $this->wsdl->call('doChangePass', $arr, 'webapi.planlekcji.isf');
            if ($act == 'auth:failed') {
                Kohana_Request::factory()->redirect('admin/haslo/false');
            } else {
                insert_log('admin.chpass', 'Uzytkownik ' . $_SESSION['user'] . ' zmienil haslo');
                Kohana_Request::factory()->redirect('admin/haslo/pass');
            }
        } else {
            Kohana_Request::factory()->redirect('');
        }
    }

    /**
     * Akcja: users
     * Rola: zarzadzanie uzytkownikami
     */
    public function action_users() {
        $this->check_login();
        $view = new View('main');
        $view2 = new View('admin_users');
        $view->set('content', $view2->render());

        echo $view->render();
    }
    
    public function action_token($user){
        $this->check_login();
        $view = View::factory('admin_token');
        $view->set('id', $user);
        echo $view->render();
    }

}