<?php
date_default_timezone_set('Europe/Warsaw');
if (isset($_SERVER['HTTP_USER_AGENT'])) {
    die('Program do uruchomienia w konsoli'.PHP_EOL);
}
if(!file_exists('index.php')){
   die('Musisz uruchomic z poziomu katalogu aplikacji'.PHP_EOL);
}
if (function_exists('posix_getuid') && posix_getuid() != 0) {
    die('Na systemie UNIX musisz uruchomic z prawami roota'.PHP_EOL);
}
if(!isset($argv[1])||!isset($argv[2])){
    die('php export.php [http://adres] [adres_eksportu]'.PHP_EOL);
}
require_once 'modules/isf/classes/kohana/isf.php';
require_once 'config.php';
$GLOBALS['hostname'] = $argv[1].'/';
$isf = new Kohana_Isf();
$isf->DbConnect();
if (!extension_loaded('curl')) {
    die('Wymagana jest obsluga cURL'.PHP_EOL);
}
$dirp = realpath($argv[2]).DIRECTORY_SEPARATOR;
if(!file_exists($argv[2])){
    die('Katalog '.$argv[2].' nie istnieje!');
}
echo <<< START

=======================
= K O M P I L A T O R = Wersja 1.5
=     P L A N O W     = (C) Michal Bocian, 2011
=      Z A J E C      =
=======================


START;
$GLOBALS['dirp']=$dirp;
@copy('lib/css/style.css', $dirp.'style.css');
@copy('lib/images/printer.png', $dirp.'printer.png');
@copy('lib/css/style_print.css', $dirp.'style_print.css');
@mkdir($dirp.'nauczyciel');
@mkdir($dirp.'klasa');
@mkdir($dirp.'sala');
echo 'Trwa kompilowanie...' . PHP_EOL;
/**
 * Utworzenie index
 */
$ns = $isf->DbSelect('rejestr', array('*'), 'where opcja=\'nazwa_szkoly\'');
$title = $ns[1]['wartosc'];
$file = <<<START
<!doctype html>
<html lang="pl">
<head>
<meta charset="UTF-8"/>
<link rel="stylesheet" type="text/css" href="style.css"/>
<title>Plan Lekcji - $title</title>
</head>
<body bgcolor="#E0FFFF">
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
$file .= '</p><h3><a href="nauczyciel/zestawienie.html">Zestawienie planów</a></h3>';

$file .= '<hr style="margin-top:10px;"/><p class="grplek">Wygenerowano aplikacją Plan Lekcji, dnia ' . date('d.m.Y')."</p>";
$file .= <<<START
</body>
</html>
START;
$f = fopen($dirp.'index.html', 'w');
fwrite($f, $file);

function klasafile($klasa) {
    $dirp = $GLOBALS['dirp'];
    $hostname = $GLOBALS['hostname'];
    $ch = curl_init($hostname . 'index.php/podglad/klasa/' . $klasa);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $ret = curl_exec($ch);
    curl_close($ch);
    $ret = str_replace('/index.php/podglad', '..', $ret);
    $ret = str_replace('/lib/css', '..', $ret);
    $ret = str_replace('/lib/images', '..', $ret);
    $ret = str_replace('<body>', '<body bgcolor="#E0FFFF">', $ret);
    $ret = preg_replace('/(nauczyciel\/)(\w+)/e', '"$1$2".".html"', $ret);
    $ret = preg_replace('/(klasa\/)(\w+)/e', '"$1$2".".html"', $ret);
    $ret = preg_replace('/(sala\/)(\w+)/e', '"$1$2".".html"', $ret);
    $fh = fopen($dirp.'klasa/' . $klasa . '.html', 'w');
    fwrite($fh, $ret);
}

foreach ($isf->DbSelect('klasy', array('*')) as $rid => $rcl) {
    klasafile($rcl['klasa']);
}

function salafile($sala) {
    $dirp = $GLOBALS['dirp'];
    $hostname = $GLOBALS['hostname'];
    $ch = curl_init($hostname . 'index.php/podglad/sala/' . $sala);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $ret = curl_exec($ch);
    curl_close($ch);
    $ret = str_replace('/index.php/podglad', '..', $ret);
    $ret = str_replace('/lib/css', '..', $ret);
    $ret = str_replace('/lib/images', '..', $ret);
    $ret = str_replace('<body>', '<body bgcolor="#E0FFFF">', $ret);
    $ret = preg_replace('/(nauczyciel\/)(\w+)/e', '"$1$2".".html"', $ret);
    $ret = preg_replace('/(klasa\/)(\w+)/e', '"$1$2".".html"', $ret);
    $ret = preg_replace('/(sala\/)(\w+)/e', '"$1$2".".html"', $ret);
    $fh = fopen($dirp.'sala/' . $sala . '.html', 'w');
    fwrite($fh, $ret);
}

foreach ($isf->DbSelect('sale', array('*')) as $rid => $rcl) {
    salafile($rcl['sala']);
}

function nlfile($skrot) {
    $dirp = $GLOBALS['dirp'];
    $hostname = $GLOBALS['hostname'];
    $ch = curl_init($hostname . 'index.php/podglad/nauczyciel/' . $skrot);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $ret = curl_exec($ch);
    curl_close($ch);
    $ret = str_replace('/index.php/podglad', '..', $ret);
    $ret = str_replace('/lib/css', '..', $ret);
    $ret = str_replace('/lib/images', '..', $ret);
    $ret = str_replace('<body>', '<body bgcolor="#E0FFFF">', $ret);
    $ret = preg_replace('/(nauczyciel\/)(\w+)/e', '"$1$2".".html"', $ret);
    $ret = preg_replace('/(klasa\/)(\w+)/e', '"$1$2".".html"', $ret);
    $ret = preg_replace('/(sala\/)(\w+)/e', '"$1$2".".html"', $ret);
    $fh = fopen($dirp.'nauczyciel/' . $skrot . '.html', 'w');
    fwrite($fh, $ret);
}

foreach ($isf->DbSelect('nauczyciele', array('*')) as $rid => $rcl) {
    nlfile($rcl['skrot']);
}

function zfile() {
    $dirp = $GLOBALS['dirp'];
    $hostname = $GLOBALS['hostname'];
    $ch = curl_init($hostname . 'index.php/podglad/zestawienie');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $ret = curl_exec($ch);
    curl_close($ch);
    $ret = str_replace('/index.php/podglad', '..', $ret);
    $ret = str_replace('/lib/css', '..', $ret);
    $ret = str_replace('/lib/images', '..', $ret);
    $ret = str_replace('<body>', '<body bgcolor="#E0FFFF">', $ret);
    $ret = preg_replace('/(nauczyciel\/)(\w+)/e', '"$1$2".".html"', $ret);
    $ret = preg_replace('/(klasa\/)(\w+)/e', '"$1$2".".html"', $ret);
    $ret = preg_replace('/(sala\/)(\w+)/e', '"$1$2".".html"', $ret);
    $fh = fopen($dirp.'nauczyciel/zestawienie.html', 'w');
    fwrite($fh, $ret);
}

zfile();

echo 'Kompilacja zakonczona'.PHP_EOL;
