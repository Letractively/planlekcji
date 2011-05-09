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
 * Kontroler: admin
 * 
 * Rola: Odpowiada za dostęp do trybu administratora
 */
class Controller_Admin extends Controller {

    public function __construct() {
        session_start();
    }

    public function action_index() {
        if (!isset($_SESSION['valid']) || !isset($_COOKIE['PHPSESSID'])) {
            Kohana_Request::factory()->redirect('admin/login');
            exit;
        }
    }

    public function action_login() {
        if (isset($_SESSION['valid']) && isset($_COOKIE['PHPSESSID'])) {
            Kohana_Request::factory()->redirect('');
            exit;
        }
        $view = view::factory('main');
        $view2 = view::factory('admin_login');

        $view->set('content', $view2->render());
        echo $view->render();
    }

    public function action_dologin() {
        $isf = new Kohana_Isf();
        $isf->DbConnect();
        $login = $_POST['inpLogin'];
        $haslo = md5($_POST['inpHaslo']);

        $res = $isf->DbSelect('uzytkownicy', array('*'), 'where login=\'' . $login . '\' and haslo=\'' . $haslo . '\'');
        if (count($res) == 1) {
            $_SESSION['valid'] = true;
            setcookie('login', $res[1]['login'], null, '/');
            Kohana_Request::factory()->redirect('');
        } else {
            echo 'Zle dane! <a href="' . URL::site('admin/login') . '">[ powrot ]</a>';
        }
    }

    public function action_zamknij() {

        if (!isset($_SESSION['valid']) || !isset($_COOKIE['PHPSESSID'])) {
            Kohana_Request::factory()->redirect('admin/login');
            exit;
        }

        $view = view::factory('main');
        $view2 = view::factory('admin_zamknij');

        $view->set('content', $view2->render());
        echo $view->render();
    }

    public function action_zamknijconfirm() {

        if (!isset($_SESSION['valid']) || !isset($_COOKIE['PHPSESSID'])) {
            Kohana_Request::factory()->redirect('admin/login');
            exit;
        }

        $isf = new Kohana_Isf();
        $isf->DbConnect();
        $isf->DbUpdate('rejestr', array('wartosc' => '0'), 'opcja="edycja_danych"');
        Kohana_Request::factory()->redirect('default/index');
    }

    public function action_logout() {
        unset($_SESSION['valid']);
        setcookie('login');
        Kohana_Request::factory()->redirect('default/index');
    }

    public function action_reset() {
        if (!isset($_SESSION['valid']) || !isset($_COOKIE['PHPSESSID'])) {
            Kohana_Request::factory()->redirect('admin/login');
            exit;
        }
        $isf = new Kohana_Isf();
        $isf->DbConnect();
        $view = view::factory('main');
        $view2 = view::factory('admin_reset');

        $view->set('content', $view2->render());
        echo $view->render();
    }

    public function action_doreset() {
        if (!isset($_SESSION['valid']) || !isset($_COOKIE['PHPSESSID'])) {
            Kohana_Request::factory()->redirect('admin/login');
            exit;
        }
        $isf = new Kohana_Isf();
        $isf->DbConnect();
        $isf->DbDelete('planlek', 'klasa like "%"');
        $isf->DbDelete('plan_grupy', 'klasa like "%"');
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

    public function action_zmiendane() {
        if (!isset($_SESSION['valid']) || !isset($_COOKIE['PHPSESSID'])) {
            Kohana_Request::factory()->redirect('admin/login');
            exit;
        }

        $url = substr(URL::base(), 0, -1);
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

    public function action_dochange() {
        if (!isset($_SESSION['valid']) || !isset($_COOKIE['PHPSESSID'])) {
            Kohana_Request::factory()->redirect('admin/login');
            exit;
        }
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

        Kohana_Request::factory()->redirect('admin/zmiendane');
    }

    public function action_haslo($err=false) {
        if (!isset($_SESSION['valid']) || !isset($_COOKIE['PHPSESSID'])) {
            Kohana_Request::factory()->redirect('admin/login');
            exit;
        }
        $view = view::factory('main');
        $view2 = view::factory('admin_haslo');

        if ($err != false) {
            $view2->set('_tplerr', $err);
        }else{
            $view2->set('_tplerr', '');
        }

        $view->set('content', $view2->render());
        echo $view->render();
    }

    public function action_chpass() {
        if (!isset($_SESSION['valid']) || !isset($_COOKIE['PHPSESSID'])) {
            Kohana_Request::factory()->redirect('admin/login');
            exit;
        }
        if (isset($_POST)) {
            $isf = new Kohana_Isf();
            $isf->DbConnect();

            $s = md5($_POST['inpSH']);
            $n = md5($_POST['inpNH']);
            $p = md5($_POST['inpPH']);

            $stare = $isf->DbSelect('uzytkownicy', array('*'), 'where login="' . $_COOKIE['login'] . '"');
            $stare = $stare[1]['haslo'];
            
            if(strlen($s)<6||strlen($n)<6||strlen($p)<6){
                Kohana_Request::factory()->redirect('admin/haslo/false');
                exit;
            }
            
            if ($s != $stare){
                Kohana_Request::factory()->redirect('admin/haslo/false');
                exit;
            }
            
            if ($n != $p){
                Kohana_Request::factory()->redirect('admin/haslo/false');
                exit;
            }
            
            $isf->DbUpdate('uzytkownicy', array('haslo'=>$n), 'login="' . $_COOKIE['login'] . '"');
            Kohana_Request::factory()->redirect('admin/haslo/pass');
        }
    }

}