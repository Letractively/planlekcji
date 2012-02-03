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
 * Odpowiada za dostep do trybu administratora
 * 
 * @package admin
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
	try {
	    $this->wsdl = new nusoap_client(URL::base('http') . 'webapi.php?wsdl');
	} catch (Exception $e) {
	    echo $e->getMessage();
	    exit;
	}
    }

    /**
     * Sprawdza zalogowanie uzytkownika
     *
     * @param string $only_root Dostep tylko dla root
     * @return string Komunikat
     */
    public function check_login($only_root=true) {
	if (!isset($_SESSION['token'])) {
	    return false;
	    Kohana_Request::factory()->redirect('admin/login');
	    exit;
	} else {

	    $auth = $this->wsdl->call('doShowAuthTime', array('token' => $_SESSION['token']));

	    try {
		$res = Isf2::Connect()->Select('uzytkownicy', array('webapi_token'))
			->Where(array('login' => $_SESSION['user'], 'webapi_token' => $_SESSION['token']))
			->Execute();
	    } catch (Exception $e) {
		echo Core_Tools::ShowError($e->getMessage(), $e->getCode());
	    }

	    if (count($res) != 1) {
		session_destroy();
		Kohana_Request::factory()->redirect('admin/login/exist');
	    }

	    if (strtotime($_SESSION['token_time']) < time()) {
		$this->wsdl->call('doLogout', array('token' => $_SESSION['token']));
		session_destroy();
		Kohana_Request::factory()->redirect('admin/login/delay');
		exit;
	    }

	    if ($auth == 'auth:failed') {
		return false;
		Kohana_Request::factory()->redirect('admin/login');
		exit;
	    }

	    if ($_SESSION['user'] != 'root' && $only_root == true) {
		Kohana_Request::factory()->redirect('');
		exit;
	    }

	    return true;
	}
    }

    /**
     * uruchamia glowna strone
     */
    public function action_index() {
	$this->check_login(false);
    }

    /**
     * logowanie do systemu
     *
     * @param boolean $pass poprawnosc logowania
     */
    public function action_login($pass='') {

	if ($this->check_login(false) == true) {
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

	$isf = new Kohana_Isf();
	$isf->JQUi();

	$view->set('script', $isf->JQUi_MakeScript());
	$view->set('content', $view2->render());
	echo $view->render();
    }

    /**
     * odpowiada za walidacje danych do logowania
     */
    public function action_dologin() {
	$login = $_POST['inpLogin'];
	$haslo = $_POST['inpHaslo'];
	if (!isset($_POST['inpToken'])) {
	    $_POST['inpToken'] = '';
	}
	$msg = $this->wsdl->call('doLogin', array('login' => $login, 'haslo' => $haslo, 'token' => $_POST['inpToken']));
	if ($msg != 'auth:failed' && $msg != 'auth:locked') {
	    $_SESSION['token'] = $msg;
	    $_SESSION['user'] = $login;
	    if (isset($_POST['inpToken']) && $login != 'root') {
		$_SESSION['usr_token'] = $_POST['inpToken'];
	    }
	    $_SESSION['token_time'] = $this->wsdl->call('doShowAuthTime', array('token' => $msg));
	    insert_log('admin.login', 'Uzytkownik ' . $login . ' zalogowal sie');
	    Kohana_Request::factory()->redirect('');
	} else {
	    if ($msg == 'auth:locked') {
		Kohana_Request::factory()->post('inpLogin', $login);
		Kohana_Request::factory()->redirect('admin/login/locked');
		insert_log('admin.login', 'Nieudana próba zalogowania zablokowanego użytkownika ' . $login);
	    } else {
		Kohana_Request::factory()->post('inpLogin', $login);
		Kohana_Request::factory()->redirect('admin/login/false');
		insert_log('admin.login', 'Nieudana próba zalogowania użytkownika ' . $login);
	    }
	}
    }

    /**
     * strona zamkniecia edycji sal, przedmiotow, etc
     */
    public function action_zamknij() {

	$this->check_login();

	$view = view::factory('main');
	$view2 = view::factory('admin_zamknij');

	$isf = new Kohana_Isf();
	$isf->JQUi();

	$view->set('script', $isf->JQUi_MakeScript());
	$view->set('content', $view2->render());
	echo $view->render();
    }

    /**
     * strona zamkniecia edycji planow zajec
     */
    public function action_zamknij2() {

	$this->check_login(false);

	$view = view::factory('main');
	$view2 = view::factory('admin_zamknij2');

	$view->set('content', $view2->render());
	echo $view->render();
    }

    /**
     * Odnawia token
     */
    public function action_renew() {

	$this->check_login(false);
	insert_log('admin.renewtoken', 'Uzytkownik ' . $_SESSION['user'] . ' odnowil token');
	$this->wsdl->call('doRenewToken', array('token' => $_SESSION['token']));
	$_SESSION['token_time'] = $this->wsdl->call('doShowAuthTime', array('token' => $_SESSION['token']));
	Request::factory()->redirect('');
    }

    /**
     * potwierdza zamkniecie edycji sal, przedmiotow, etc
     */
    public function action_zamknijconfirm() {

	$this->check_login();
	if (isset($_POST)) {
	    $isf = new Kohana_Isf();
	    $isf->Connect(APP_DBSYS);
	    $isf->DbUpdate('rejestr', array('wartosc' => '0'), 'opcja=\'edycja_danych\'');
	    insert_log('admin.zamknij', 'Zamknięcie edycji systemu');
	    Kohana_Request::factory()->redirect('default/index');
	}
    }

    /**
     * potwierdza zamkniecie edycji planow
     */
    public function action_zamknijconfirm2() {

	$this->check_login(false);
	if (isset($_POST)) {
	    $isf = new Kohana_Isf();
	    $isf->Connect(APP_DBSYS);
	    $isf->DbUpdate('rejestr', array('wartosc' => '3'), 'opcja=\'edycja_danych\'');
	    App_Globals::writeXmlTimetables();
	    insert_log('admin.zamknij', 'Zamknięcie edycji planów');
	    Kohana_Request::factory()->redirect('default/index');
	}
    }

    /**
     * wylogowuje
     */
    public function action_logout() {
	$this->wsdl->call('doLogout', array('token' => $_SESSION['token']));
	unset($_SESSION['token']);
	setcookie('login', '', time() - 3600, '/');
	insert_log('admin.logout', 'Uzytkownik ' . $_SESSION['user'] . ' wylogował się');
	session_destroy();

	Kohana_Request::factory()->redirect('default/index');
    }

    /**
     * strona usuwania planow
     */
    public function action_planreset() {

	$this->check_login();

	if (!isset($_POST)) {
	    Kohana_Request::factory()->redirect('');
	    exit;
	}
	$isf = new Kohana_Isf();
	$isf->Connect(APP_DBSYS);
	$isf->DbDelete('planlek', 'klasa like \'%\'');
	$isf->DbDelete('plan_grupy', 'klasa like \'%\'');
	$isf->DbDelete('zast_id', 'zast_id like \'%\'');
	$isf->DbDelete('zastepstwa', 'zast_id like \'%\'');
	$isf->DbUpdate('rejestr', array('wartosc' => '0'), 'opcja=\'edycja_danych\'');
	Kohana_Request::factory()->redirect('default/index');
    }

    /**
     * strona usuwania danych jak sale, etc
     */
    public function action_reset() {

	$this->check_login();

	$isf = new Kohana_Isf();
	$isf->Connect(APP_DBSYS);

	$isf->JQUi();

	$view = view::factory('main');
	$view2 = view::factory('admin_reset');

	$view->set('script', $isf->JQUi_MakeScript());
	$view->set('content', $view2->render());
	echo $view->render();
    }

    /**
     * usuwa dane jak sale, etc
     */
    public function action_doreset() {

	$this->check_login();

	$isf = new Kohana_Isf();
	$isf->Connect(APP_DBSYS);
	$isf->DbDelete('planlek', 'klasa like \'%\'');
	$isf->DbDelete('plan_grupy', 'klasa like \'%\'');
	$isf->DbDelete('zast_id', 'zast_id like \'%\'');
	$isf->DbDelete('zastepstwa', 'zast_id like \'%\'');
	$isf->DbUpdate('rejestr', array('wartosc' => '1'), 'opcja=\'edycja_danych\'');
	if (isset($_POST['cl'])) {
	    $isf->DbDelete('klasy', 'klasa like \'%\'');
	    $isf->DbDelete('lek_godziny', 'lekcja like \'%\'');
	    $isf->DbDelete('nauczyciele', 'imie_naz like \'%\'');
	    $isf->DbDelete('nl_klasy', 'klasa like \'%\'');
	    $isf->DbDelete('nl_przedm', 'przedmiot like \'%\'');
	    $isf->DbDelete('przedmiot_sale', 'sala like \'%\'');
	    $isf->DbDelete('przedmioty', 'przedmiot like \'%\'');
	    $isf->DbDelete('sale', 'sala like \'%\'');
	    $isf->DbUpdate('rejestr', array('wartosc' => '1'), 'opcja=\'ilosc_godzin_lek\'');
	}
	Kohana_Request::factory()->redirect('');
    }

    /**
     * strona zmiana danych szkoly, strony glownej
     */
    public function action_zmiendane() {

	$this->check_login();

	$url = substr(URL::base(), 0, -1);
	/**
	 * Skrypt TinyMCE
	 */
	$script = <<< START
<script type='text/javascript' src='$url/lib/tiny_mce/tiny_mce.js'></script>
<script type='text/javascript'>
    tinyMCE.init({
        mode : "textareas",
	theme : "simple"
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
     * zmienia dane szkoly, strony glownej
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
	$isf->Connect(APP_DBSYS);

	$isf->DbUpdate('rejestr', array('wartosc' => $nazwa), 'opcja=\'nazwa_szkoly\'');
	$isf->DbUpdate('rejestr', array('wartosc' => $text), 'opcja=\'index_text\'', false);

	Kohana_Request::factory()->redirect('default/index');
    }

    /**
     * strona zmiany hasla
     */
    public function action_haslo($err=false) {

	$this->check_login(false);

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
     * zmienia haslo
     */
    public function action_chpass() {
	$this->check_login(false);

	insert_log('admin.chpass', 'Uzytkownik ' . $_SESSION['user'] . ' proboje zmienic haslo');

	if (isset($_POST)) {
	    $isf = new Kohana_Isf();
	    $isf->Connect(APP_DBSYS);

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
	    $act = $this->wsdl->call('doChangePass', $arr);
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
     * 
     * Wyswietla strone z uzytkownikami
     */
    public function action_users() {
	$this->check_login();
	$isf = new Kohana_Isf();
	$view = new View('main');
	$view2 = new View('admin_users');
	$isf->JQUi();
	$isf->JQUi_ButtonCreate('btnCUser');
	$view->set('script', $isf->JQUi_MakeScript());
	$view->set('content', $view2->render());

	echo $view->render();
    }

    /**
     * Wyswietla strone logow systemowych
     *
     * @param integer $page strona logow
     */
    public function action_logs($page=1) {
	$this->check_login();
	$view = new View('main');
	$view2 = new View('admin_logs');
	$view2->set('page', $page);
	$view->set('content', $view2->render());

	echo $view->render();
    }

    /**
     * 
     * Usuwa wszystkie logi systemowe
     */
    public function action_dellogs() {
	$this->check_login();
	$isf = new Kohana_Isf();
	$isf->Connect(APP_DBSYS);
	$isf->DbDelete('log', 'id like \'%\'');
	Kohana_Request::factory()->redirect('admin/logs');
    }

    /**
     * 
     * Wywietla strone generujaca tokeny dla uzytkownika o danym numerze id
     *
     * @param integer $user id uzytkownika
     */
    public function action_token($user) {
	$this->check_login();
	$view = View::factory('admin_token');
	$view->set('id', $user);
	echo $view->render();
    }

    /**
     * 
     * Usuwa uzytkownika o numerze id
     *
     * @param integer $uid numer uzytkownika
     */
    public function action_userdel($uid) {
	$this->check_login();
	$isf = new Kohana_Isf();
	$isf->Connect(APP_DBSYS);
	$u = $isf->DbSelect('uzytkownicy', array('*'), 'where uid=\'' . $uid . '\'');
	$isf->DbDelete('uzytkownicy', 'uid=\'' . $uid . '\'');
	$isf->DbDelete('tokeny', 'login=\'' . $u[0]['login'] . '\'');
	Kohana_Request::factory()->redirect('admin/users');
    }

    /**
     * 
     * Wyswietla strone dodawania uzytkownika
     *
     * @param string $err kod bledu do szablonu
     */
    public function action_adduser($err=null) {
	$this->check_login();
	$view = new View('main');
	$view2 = new View('admin_adduser');
	$view2->set('err', $err);
	$view->set('content', $view2->render());

	echo $view->render();
    }

    /**
     * 
     * Dodaje uzytkownika
     */
    public function action_douseradd() {
	$this->check_login();
	if (!isset($_POST)) {
	    Kohana_Request::factory()->redirect('');
	    exit;
	}
	$isf = new Kohana_Isf();
	$isf->Connect(APP_DBSYS);
	$login = $_POST['inpLogin'];
	$haslo = $_POST['inpHaslo'];
	$uid = $_POST['inpUid'];
	if (strlen($login) < 5 || strlen($haslo) < 6) {
	    Kohana_Request::factory()->redirect('admin/adduser/leng');
	    exit;
	}
	if (preg_match('/([!@#$;%^&*()+| ])/i', $login)) {
	    Kohana_Request::factory()->redirect('admin/adduser/data');
	    exit;
	}
	$arr = array(
	    'uid' => $uid,
	    'login' => $login,
	    'haslo' => md5('plan' . sha1('lekcji' . $haslo))
	);
	$isf->DbInsert('uzytkownicy', $arr);
	Kohana_Request::factory()->redirect('admin/users');
    }

    /**
     * Odblokowuje uzytkownika
     *
     * @param integer $uid ID uzytkownika
     */
    public function action_userublock($uid) {
	$db = new Kohana_Isf();
	$db->Connect(APP_DBSYS);
	$db->DbUpdate('uzytkownicy', array('ilosc_prob' => '0'), 'uid=\'' . $uid . '\'');
	Kohana_Request::factory()->redirect('admin/users');
    }

    /**
     * Wyswietla strone kopii zapasowej
     */
    public function action_backup() {
	$view = new View('main');
	$view2 = new View('admin_backup');
	$view->set('content', $view2->render());
	echo $view->render();
    }

    /**
     * Wykonuje kopie zapasowa
     */
    public function action_dobackup() {

	$isf = new Kohana_Isf();
	$isf->Connect(APP_DBSYS);
	$xml = new XMLWriter();
	$xml->openMemory();
	$xml->startDocument('1.0', 'UTF-8');
	$xml->setIndent(4);
	$xml->writeComment('---WYGENEROWANO APLIKACJA BACKUP PLAN LEKCJI');
	$xml->writeComment('---WERSJA: testing, dnia ' . date('d.m.Y'));
	$xml->startElement('backup');
	foreach ($isf->DbSelect('sqlite_master', array('name'), 'where type=\'table\' order by name') as $row) {
	    $xml->startElement('table');
	    $xml->startAttribute('name');
	    $xml->text($row['name']);
	    $xml->endAttribute();

	    foreach ($isf->DbSelect($row['name'], array('*')) as $rowx) {
		$xml->startElement('row');
		foreach ($rowx as $attr => $value) {
		    if (!is_numeric($attr)) {
			$xml->startElement($attr);
			$xml->text($value);
			$xml->endElement();
		    }
		}
		$xml->endElement();
	    }
	    $xml->endAttribute();
	    $xml->endElement();
	}
	$xml->endElement();
	$xml->endDocument();

	header("Content-Type: application/force-download");
	header("Content-Type: application/octet-stream");
	header("Content-Type: application/download");
	header("Content-Disposition: attachment; filename=backup.xml;");

	echo $xml->flush();
    }

}