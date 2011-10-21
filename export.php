<?php

/**
 * Eksporter Planow Lekcji (EPL)
 * 
 * @author Michal Bocian <mhl.bocian@gmail.com>
 * @license GNU GPL v3
 * @package main\export
 */
date_default_timezone_set('Europe/Warsaw'); // ustawienie strefy czasowej
session_start(); // ustawienie sesji
set_time_limit(600);

define('DOCROOT', dirname(__FILE__) . DIRECTORY_SEPARATOR);

/**
 * Sprawadza obecnosc wymaganych modulow
 */
if (!extension_loaded('curl')) {
    die('Wymagana jest obsluga <b>cURL</b>');
}
if (!class_exists('ZipArchive')) {
    die('Wymagana jest obsluga <b>ZipArchive</b>');
}
if (!is_writable('export')) {
    die('Katalog <b>export</b> musi byc zapisywalny');
}
/**
 * Dolacza niezbedne pliki
 */
require_once 'config.php';
require_once 'modules/isf/classes/kohana/isf.php';
require_once 'lib/nusoap/nusoap.php';
require_once 'application/planlekcji/globals.app';

$GLOBALS['hostname'] = 'http://' . $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . $path;
$isf = new Kohana_Isf();
$isf->DbConnect();

try {
    $wsdl = new nusoap_client($GLOBALS['hostname'] . 'webapi.php?wsdl');
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

$reg = $isf->DbSelect('rejestr', array('*'), 'where opcja=\'edycja_danych\'');
if ($reg[1]['wartosc'] != 3) {
    die('Dopoki plany nie zostana zatwierdzone, nie mozna wygenerowac planu. <a href="index.php">Powrót</a>');
}

if (file_exists('export/planlekcji.zip')) {
    unlink('export/planlekcji.zip');
}

$zip = new ZipArchive();

if ($zip->open('export/planlekcji.zip', ZIPARCHIVE::CREATE) !== TRUE) {
    die('Nie udalo sie utworzyc pliku planlekcji.zip. <a href="index.php">Powrót</a>');
}

echo <<< START
<!doctype html>
<head>
<meta charset="UTF-8"/>
<title>Generowanie planu lekcji</title>
<link rel="stylesheet" type="text/css" href="lib/css/style.css"/>
<style>
body{
text-align: center;
}
</style>
</head>
<body>
<img src="lib/images/logo.png" alt=""/>
<h1>Eksportowanie planu lekcji</h1>
START;

if (!isset($_POST['btnSubmit'])) {
    echo <<< START
<p>Nastąpi uruchomienie kompilatora statycznego planu oraz spakowanie wszystkich plików
    do archiwum zip.</p>
    <p class="notice"><b>Uwaga:</b> ten proces może potrwać chwilę.</p>
<form action="" method="post">
<p>Proszę wybrać motyw: 
<select name="motyw">
START;
    foreach (App_Globals::getThemes() as $theme) {
        echo '<option>' . $theme . '</option>';
    }
    echo <<< AAA
</select></p>
<button type="submit" name="btnSubmit">Kompiluj plan i spakuj go</button>
</form >
AAA;

    echo '</body></html>';
    exit;
}

echo <<< START
<pre>
Trwa kompilacja planu zajec...

START;

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
$ns = $isf->DbSelect('rejestr', array('*'), 'where opcja=\'nazwa_szkoly\'');
$title = $ns[1]['wartosc'];
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
$file .= '<h1>Plan Lekcji - ' . $ns[1]['wartosc'] . '</h1><hr/>';

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

function klasafile($klasa) {
    $hostname = $GLOBALS['hostname'];
    $ch = curl_init($hostname . 'index.php/podglad/sklasa/' . $klasa);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $ret = curl_exec($ch);
    curl_close($ch);
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
    return $ret;
}

echo PHP_EOL . 'Kompilowanie klas';
foreach ($isf->DbSelect('klasy', array('*')) as $rid => $rcl) {
    echo '.';
    flush();
    ob_flush();
    $zip->addFromString('planlekcji/klasa/' . $rcl['klasa'] . '.html', klasafile($rcl['klasa']));
}

function salafile($sala) {
    $hostname = $GLOBALS['hostname'];
    $ch = curl_init($hostname . 'index.php/podglad/ssala/' . $sala);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $ret = curl_exec($ch);
    curl_close($ch);
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
    return $ret;
}

echo PHP_EOL . 'Kompilowanie sal';
foreach ($isf->DbSelect('sale', array('*')) as $rid => $rcl) {
    echo '.';
    flush();
    ob_flush();
    $zip->addFromString('planlekcji/sala/' . $rcl['sala'] . '.html', salafile($rcl['sala']));
}

function nlfile($skrot) {
    $hostname = $GLOBALS['hostname'];
    $ch = curl_init($hostname . 'index.php/podglad/snauczyciel/' . $skrot);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $ret = curl_exec($ch);
    curl_close($ch);
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
    return $ret;
}

echo PHP_EOL . 'Kompilowanie nauczycieli';
foreach ($isf->DbSelect('nauczyciele', array('*')) as $rid => $rcl) {
    echo '.';
    flush();
    ob_flush();
    $zip->addFromString('planlekcji/nauczyciel/' . $rcl['skrot'] . '.html', nlfile($rcl['skrot']));
}

function zfile() {
    $hostname = $GLOBALS['hostname'];
    $ch = curl_init($hostname . 'index.php/podglad/zzestawienie');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $ret = curl_exec($ch);
    curl_close($ch);
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
    return $ret;
}

echo PHP_EOL . 'Finalizowanie...' . PHP_EOL;

$zip->addFromString('planlekcji/nauczyciel/zestawienie.html', zfile());
$zip->close();

echo 'Kompilacja zakonczona</pre>';
echo '<h3><a href="export/planlekcji.zip">Pobierz archiwum z planem zajęć</a></h3></body></html>';