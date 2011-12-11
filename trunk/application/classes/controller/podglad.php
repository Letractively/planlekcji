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
 * Odpowiada za wyswietlanie planow
 * 
 * @package podglad
 */
class Controller_Podglad extends Controller {

    public $head;

    /**
     * Wywoluje sesje
     */
    public function __construct() {
	$this->head = '
            <!DOCTYPE html>
            <html>
            <head>
            <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
            <title>Plan lekcji - ' . App_Globals::getRegistryKey('nazwa_szkoly') . '</title>
            <link rel="stylesheet" type="text/css" href="' . URL::base() . 'lib/css/style.css"/>
            <link rel="stylesheet" type="text/css" href="' . URL::base() . 'lib/css/themes/{{theme}}.css"/>
            </head>
            <body>
            ';
    }

    /**
     * Wyswietla plan dla klasy
     *
     * @param string $klasa 
     */
    public function action_klasa($klasa) {
	$vm = view::factory('main');
	$view = view::factory('podglad_klasa');
	$view->set('klasa', $klasa);

	$vm->set('content', $view->render());
	echo $vm->render();
    }

    /**
     * Wyswietla plan dla sali
     *
     * @param string $sala 
     */
    public function action_sala($sala) {

	$main = View::factory('main');

	$view = view::factory('podglad_sala');
	$view->set('klasa', $sala);

	$main->set('content', $view->render());

	echo $main->render();
    }

    /**
     * Wyswietla plan dla nauczyciela
     *
     * @param string $nauczyciel
     */
    public function action_nauczyciel($nauczyciel) {

	$main = View::factory('main');

	$view = view::factory('podglad_nauczyciel');

	$isf = new Kohana_Isf();
	$isf->Connect(APP_DBSYS);

	$imienaz = $isf->DbSelect('nauczyciele', array('*'), 'where skrot=\'' . $nauczyciel . '\'');
	$imienaz = $imienaz[0]['imie_naz'];

	$view->set('skrot', $nauczyciel);
	$view->set('klasa', $imienaz);

	$main->set('content', $view->render());
	echo $main->render();
    }

    /**
     * Wyswietla zestawienie
     */
    public function action_zestawienie() {
	$view = View::factory('podglad_zestawienie');
	$out = str_replace('{{theme}}', $_SESSION['app_theme'], $view->render());
	echo $out;
    }

    /**
     * Wyswietla plan w trybie generatora
     * 
     * Czysty plan z HTML bez reszty systemu,menu
     */
    public function action_zzestawienie() {
	$view = View::factory('podglad_zestawienie');
	echo $view->render();
    }

    /**
     * Wyswietla plan w trybie generatora
     * 
     * Czysty plan z HTML bez reszty systemu,menu
     */
    public function action_sklasa($klasa) {
	echo $this->head;
	$view = view::factory('podglad_klasa');
	$view->set('klasa', $klasa);
	echo $view->render();
	echo '</body></html>';
    }

    /**
     * Wyswietla plan w trybie generatora
     * 
     * Czysty plan z HTML bez reszty systemu,menu
     */
    public function action_ssala($klasa) {
	echo $this->head;
	$view = view::factory('podglad_sala');
	$view->set('klasa', $klasa);
	echo $view->render();
	echo '</body></html>';
    }

    /**
     * Wyswietla plan w trybie generatora
     * 
     * Czysty plan z HTML bez reszty systemu,menu
     */
    public function action_snauczyciel($nauczyciel) {
	echo $this->head;
	$view = view::factory('podglad_nauczyciel');

	$isf = new Kohana_Isf();
	$isf->Connect(APP_DBSYS);

	$imienaz = $isf->DbSelect('nauczyciele', array('*'), 'where skrot=\'' . $nauczyciel . '\'');
	$imienaz = $imienaz[0]['imie_naz'];

	$view->set('skrot', $nauczyciel);
	$view->set('klasa', $imienaz);

	echo $view->render();
	echo '</body></html>';
    }

    /**
     * Wyswietla plan w trybie generatora
     * 
     * Czysty plan z HTML bez reszty systemu,menu
     */
    public function action_nx_zestawienie() {
	$view = View::factory('podglad_zestawienie');
	return $view->render();
    }

    /**
     * Wyswietla plan w trybie generatora
     * 
     * Czysty plan z HTML bez reszty systemu,menu
     */
    public function action_nx_klasa($klasa) {
	$out = $this->head;
	$view = view::factory('podglad_klasa');
	$view->set('klasa', $klasa);
	$out .= $view->render();
	$out .= '</body></html>';
	return $out;
    }

    /**
     * Wyswietla plan w trybie generatora
     * 
     * Czysty plan z HTML bez reszty systemu,menu
     */
    public function action_nx_sala($klasa) {
	$out = $this->head;
	$view = view::factory('podglad_sala');
	$view->set('klasa', $klasa);
	$out .= $view->render();
	$out .= '</body></html>';
	return $out;
    }

    /**
     * Wyswietla plan w trybie generatora
     * 
     * Czysty plan z HTML bez reszty systemu,menu
     */
    public function action_nx_nauczyciel($nauczyciel) {
	$out = $this->head;
	$view = view::factory('podglad_nauczyciel');

	$isf = new Kohana_Isf();
	$isf->Connect(APP_DBSYS);

	$imienaz = $isf->DbSelect('nauczyciele', array('*'), 'where skrot=\'' . $nauczyciel . '\'');
	$imienaz = $imienaz[0]['imie_naz'];

	$view->set('skrot', $nauczyciel);
	$view->set('klasa', $imienaz);

	$out .= $view->render();
	$out .= '</body></html>';

	return $out;
    }

    public function action_export() {
	if (!isset($_POST)) {
	    Kohana_Request::factory()->redirect('');
	    exit;
	}
	/**
	 * Sprawadza obecnosc wymaganych modulow
	 */
	if (!class_exists('ZipArchive')) {
	    die('Wymagana jest obsluga <b>ZipArchive</b>');
	}
	if (!is_writable('export')) {
	    die('Katalog <b>export</b> musi byc zapisywalny');
	}

	$isf = new Kohana_Isf();
	$isf->Connect(APP_DBSYS);
	
	define('FILE_PATH', DOCROOT . 'export' . DIRECTORY_SEPARATOR . 'planlekcji.zip');
	
	try {
	    $wsdl = new nusoap_client(URL::base() . 'webapi.php?wsdl');
	    if (!isset($_SESSION['token'])) {
		header("Location: index.php");
		exit;
	    } else {
		$auth = $wsdl->call('doShowAuthTime', array('token' => $_SESSION['token']), 'webapi.planlekcji.isf');
		if (strtotime($_SESSION['token_time']) < time()) {
		    $wsdl->call('doLogout', array('token' => $_SESSION['token']), 'webapi.planlekcji.isf');
		    session_destroy();
		    Kohana_Request::factory()->redirect('admin/login/delay');
		    exit;
		}
		if ($auth == 'auth:failed') {
		    header("Location: index.php");
		    exit;
		}
	    }
	} catch (Exception $e) {
	    echo $e->getMessage();
	    exit;
	}

	$edycja_danych = App_Globals::getRegistryKey('edycja_danych');

	if ($edycja_danych != 3) {
	    Kohana_Request::factory()->redirect('');
	    exit;
	}

	if (file_exists(FILE_PATH)) {
	    unlink(FILE_PATH);
	}

	$zip = new ZipArchive();
	
	if ($zip->open(FILE_PATH, ZIPARCHIVE::CREATE) !== TRUE) {
	    die('Nie udalo sie utworzyc pliku planlekcji.zip. <a href="index.php">Powrót</a>');
	}
	$url = URL::base() . 'lib/css/style.css';
	$img = URL::base() . 'lib/images/logo.png';

	$zip->setArchiveComment('Wygenerowano aplikacja Plan Lekcji, dnia ' . date('d.m.Y'));
	$zip->addEmptyDir('planlekcji');
	$zip->addEmptyDir('planlekcji/nauczyciel');
	$zip->addEmptyDir('planlekcji/klasa');
	$zip->addEmptyDir('planlekcji/sala');

	$zip->addFile('lib/css/style.css', 'planlekcji/style.css');
	$zip->addFile('lib/images/printer.png', 'planlekcji/printer.png');
	$zip->addFile('lib/css/style_print.css', 'planlekcji/style_print.css');
	$zip->addFile('lib/css/themes/' . $_POST['motyw'] . '.css', 'planlekcji/' . $_POST['motyw'] . '.css');

	/**
	 * Utworzenie index
	 */
	$title = App_Globals::getRegistryKey('nazwa_szkoly');
	
	$thm = $_POST['motyw'] . '.css';
	$file = <<<START
<!doctype html>
<html lang="pl">
<head>
<meta charset="UTF-8"/>
<link rel="stylesheet" type="text/css" href="style.css"/>
<link rel="stylesheet" type="text/css" href="$thm"/>
<title>Plan Lekcji - $title</title>
    <style>
    body{
    margin: 10px;
    }
    </style>
</head>
<body class="a_light_menu">
START;
	$file .= '<h1>Plan Lekcji - ' . $ns[0]['wartosc'] . '</h1><hr/>';

	$file .= '<h3>Klasy</h3><p class="grplek">';
	foreach ($isf->DbSelect('klasy', array('*'), 'order by klasa asc') as $rowid => $rowcol) {
	    $file .= '<a target="_blank" href="klasa/' . $rowcol['klasa'] . '.html">' . $rowcol['klasa'] . '</a>&emsp;';
	}
	$file .= '</p>';

	$file .= '<h3>Sale</h3><p class="grplek">';
	foreach ($isf->DbSelect('sale', array('*'), 'order by sala asc') as $rowid => $rowcol) {
	    $file .= '<a target="_blank" href="sala/' . $rowcol['sala'] . '.html">' . $rowcol['sala'] . '</a>&emsp;';
	}
	$file .= '</p>';

	$file .= '<h3>Nauczyciele</h3><p class="grplek">';
	foreach ($isf->DbSelect('nauczyciele', array('*'), 'order by imie_naz asc') as $rowid => $rowcol) {
	    $file .= '(' . $rowcol['skrot'] . ') <a target="_blank" href="nauczyciel/' . $rowcol['skrot'] . '.html">' . $rowcol['imie_naz'] . '</a>&emsp;';
	}
	$file .= '</p><h3><a target="_blank" href="nauczyciel/zestawienie.html">Zestawienie planów</a></h3>';

	$file .= '<hr style="margin-top:10px;"/><p class="grplek">Wygenerowano aplikacją Plan Lekcji, dnia ' . date('d.m.Y') . '</p>';
	$file .= <<<START
</body>
</html>
START;
	$zip->addFromString('planlekcji/index.html', $file);

	foreach ($isf->DbSelect('klasy', array('*')) as $rid => $rcl) {
	    ob_start();
	    $ret = $this->action_nx_klasa($rcl['klasa']);
	    $ret = str_replace('<body>', '<body class="a_light_menu">', $ret);
	    $ret = str_replace('{{theme}}', $_POST['motyw'], $ret);
	    $ret = str_replace('/index.php/podglad', '..', $ret);
	    $ret = str_replace('/lib/css', '..', $ret);
	    $ret = str_replace('/themes', '', $ret);
	    $ret = str_replace('/lib/images', '..', $ret);
	    $ret = preg_replace('/(nauczyciel\/)(\w+)/e', '"$1$2".".html"', $ret);
	    $ret = preg_replace('/(klasa\/)(\w+)/e', '"$1$2".".html"', $ret);
	    $ret = preg_replace('/(sala\/)(\w+)/e', '"$1$2".".html"', $ret);
	    $ret = str_replace(array('</body>', '</html>'), '', $ret);
	    $ret .= '<hr style="margin-top:10px;"/><p class="grplek">Wygenerowano aplikacją Plan Lekcji, dnia ' . date('d.m.Y') . '</p></body></html>';
	    echo $ret;
	    $return = ob_get_contents();
	    ob_clean();
	    ob_end_clean();
	    $zip->addFromString('planlekcji/klasa/' . $rcl['klasa'] . '.html', $return);
	    flush();
	    ob_flush();
	}

	foreach ($isf->DbSelect('sale', array('*')) as $rid => $rcl) {
	    ob_start();
	    $ret = $this->action_nx_sala($rcl['sala']);
	    $ret = str_replace('<body>', '<body class="a_light_menu">', $ret);
	    $ret = str_replace('{{theme}}', $_POST['motyw'], $ret);
	    $ret = str_replace('/index.php/podglad', '..', $ret);
	    $ret = str_replace('/lib/css', '..', $ret);
	    $ret = str_replace('/lib/images', '..', $ret);
	    $ret = str_replace('/themes', '', $ret);
	    $ret = preg_replace('/(nauczyciel\/)(\w+)/e', '"$1$2".".html"', $ret);
	    $ret = preg_replace('/(klasa\/)(\w+)/e', '"$1$2".".html"', $ret);
	    $ret = preg_replace('/(sala\/)(\w+)/e', '"$1$2".".html"', $ret);
	    $ret = str_replace(array('</body>', '</html>'), '', $ret);
	    $ret .= '<hr style="margin-top:10px;"/><p class="grplek">Wygenerowano aplikacją Plan Lekcji, dnia ' . date('d.m.Y') . '</p></body></html>';
	    echo $ret;
	    $return = ob_get_contents();
	    ob_clean();
	    ob_end_clean();
	    $zip->addFromString('planlekcji/sala/' . $rcl['sala'] . '.html', $return);
	    flush();
	    ob_flush();
	}

	foreach ($isf->DbSelect('nauczyciele', array('*')) as $rid => $rcl) {
	    ob_start();
	    $ret = $this->action_nx_nauczyciel($rcl['skrot']);
	    $ret = str_replace('<body>', '<body class="a_light_menu">', $ret);
	    $ret = str_replace('{{theme}}', $_POST['motyw'], $ret);
	    $ret = str_replace('/index.php/podglad', '..', $ret);
	    $ret = str_replace('/lib/css', '..', $ret);
	    $ret = str_replace('/lib/images', '..', $ret);
	    $ret = str_replace('/themes', '', $ret);
	    $ret = preg_replace('/(nauczyciel\/)(\w+)/e', '"$1$2".".html"', $ret);
	    $ret = preg_replace('/(klasa\/)(\w+)/e', '"$1$2".".html"', $ret);
	    $ret = preg_replace('/(sala\/)(\w+)/e', '"$1$2".".html"', $ret);
	    $ret = str_replace(array('</body>', '</html>'), '', $ret);
	    $ret .= '<hr style="margin-top:10px;"/><p class="grplek">Wygenerowano aplikacją Plan Lekcji, dnia ' . date('d.m.Y') . '</p></body></html>';
	    echo $ret;
	    $return = ob_get_contents();
	    ob_clean();
	    ob_end_clean();
	    $zip->addFromString('planlekcji/nauczyciel/' . $rcl['skrot'] . '.html', $return);
	}

	ob_start();
	$ret = $this->action_nx_zestawienie();
	$ret = str_replace('<body>', '<body class="a_light_menu">', $ret);
	$ret = str_replace('{{theme}}', $_POST['motyw'], $ret);
	$ret = str_replace('/index.php/podglad', '..', $ret);
	$ret = str_replace('/lib/css', '..', $ret);
	$ret = str_replace('/lib/images', '..', $ret);
	$ret = str_replace('/themes', '', $ret);
	$ret = preg_replace('/(nauczyciel\/)(\w+)/e', '"$1$2".".html"', $ret);
	$ret = preg_replace('/(klasa\/)(\w+)/e', '"$1$2".".html"', $ret);
	$ret = preg_replace('/(sala\/)(\w+)/e', '"$1$2".".html"', $ret);
	$ret = str_replace(array('</body>', '</html>'), '', $ret);
	$ret .= '<hr style="margin-top:10px;"/><p class="grplek">Wygenerowano aplikacją Plan Lekcji, dnia ' . date('d.m.Y') . '</p></body></html>';
	echo $ret;
	$zestawienie = ob_get_contents();
	ob_end_clean();

	$zip->addFromString('planlekcji/nauczyciel/zestawienie.html', $zestawienie);
	$zip->close();

	header("Content-Type: application/force-download");
	header("Content-Type: application/octet-stream");
	header("Content-Type: application/download");
	header("Content-Disposition: attachment; filename=planlekcji" . date('dmY') . ".zip;");
	header("Content-length: " . filesize(FILE_PATH));

	ob_start();
	readfile(FILE_PATH);
	ob_end_flush();
	exit;
    }

}