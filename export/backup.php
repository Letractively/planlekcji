<?php

require_once '../modules/isf/classes/kohana/isf.php';
require_once 'common.php';

if (!isset($_POST['doBackup'])) {
    $img1 = HTTP_ADDR . 'lib/images/gplv3.png';
    $csspath = HTTP_ADDR . 'lib/css/style.css';
    $phpself = $_SERVER['PHP_SELF'];

    echo <<< START
<!DOCTYPE html>
<head>
    <meta charset="UTF-8"/>
    <link rel="stylesheet" type="text/css" href="$csspath"/>
    <title>Kopia zapasowa systemu</title>
</head>
<body>
<h1>Kopia zapasowa systemu</h1>
<p>System wykona cały zrzut danych systemowych do pliku XML.</p>
<form action="$phpself" method="post">
    <button type="submit" name="doBackup">Rozpocznij kopię zapasową</button>
</form>
<div id="foot">
<p>
<img src="$img1" alt="GNU GPL v3 logo"/>
<b>Kopia zapasowa BETA - <a href="source.php?file=backup.php" target="_blank">kod źródłowy</a> |
<a href="http://planlekcji.googlecode.com" target="_blank">strona projektu Plan Lekcji</a></p>
</div>
</body>
START;
    exit;
}

function writeln($string) {
    echo $string . PHP_EOL;
}

$isf = new Kohana_Isf();
$isf->DbConnect();
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

exit;