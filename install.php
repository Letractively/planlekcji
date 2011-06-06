<?php
/*
 * Plik instalacyjny Planu Lekcji
 */
if(!file_exists('index.php')){
    die('Musisz uruchomic plik z poziomu katalogu aplikacji');
}
require_once 'modules/isf/classes/kohana/isf.php';
$isf = new Kohana_Isf();
$isf->DbConnect();
$err = '';
if (file_exists('modules/isf/isf_resources/default.sqlite') && !is_writable('modules/isf/isf_resources/default.sqlite')) {
    $err .= 'Plik modules/isf/isf_resources/default.sqlite musi byc zapisywalny! Instalacja przerwana ';
}
if (!empty($err)) {
    die($err);
}
$ctb = $isf->DbSelect('sqlite_master', array('*'), 'where name="rejestr"');
if (count($ctb) != 0) {
    $res = $isf->DbSelect('rejestr', array('*'), 'where opcja="installed"');
    if (count($res) >= 1) {
        $r = 1;
    }
} else {
    $r = 0;
}
?>
<?php if ($r == 1): ?>
    <html>
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
            <title>Instalacja Intersys Plan Lekcji</title>
            <link rel="stylesheet" type="text/css" href="lib/css/style.css"/>
        </head>
        <body>
            <img src="lib/images/logo.png"/>
            <h1>Instalacja zakończona powodzeniem!</h1>
            <h3>Usuń plik <b>install.php</b> i zaloguj się, używając
                danych podanych przez instalator</h3>
        </body>
    </html>
    <?php exit; ?>
<?php else: ?>
    <?php if (isset($_SERVER['HTTP_USER_AGENT'])): ?>
        <html>
            <head>
                <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
                <title>Instalacja Intersys Plan Lekcji</title>
                <link rel="stylesheet" type="text/css" href="lib/css/style.css"/>
                <style type="text/css">
                    pre{
                        font-size: 10pt;
                    }
                </style>
            </head>
            <body>
                <img src="lib/images/logo.png"/>
                <h1>Instalator Intersys Plan Lekcji</h1>
                <h3 class="notice">Uruchom plik install.php w konsoli z poziomu katalogu aplikacji</h3>
                <?php if (PHP_OS == "WINNT"): ?>
                    <p><b>Na systemie Windows, gdy ścieżka do PHP istnieje w zmiennej PATH</b></p>
                    <pre>
<b>C:\></b> cd <?php echo __DIR__.DIRECTORY_SEPARATOR ?><br/>
<b><?php echo __DIR__.DIRECTORY_SEPARATOR ?>></b> php install.php
                    </pre>
                <?php else: ?>
                    <p><b>Na systemie UNIX</b></p>
                    <pre>
<b></b> cd <?php echo __DIR__.DIRECTORY_SEPARATOR ?> $<br/>
<b><?php echo __DIR__.DIRECTORY_SEPARATOR ?> $</b> php install.php
                    </pre>
                <?php endif; ?>
                <p class="info">Więcej informacji w dokumentacji projektu</p>
            </body>
        </html>
    <?php else: ?>
        <?php
        print <<< START

+ + + + + + + + + + + + + + + + + + + +
+                                     +
+    I   N   T   E    R   S   Y   S   +  Wersja 1.0
+    P  L  A  N   L  E  K  C  J  I    + 
+                                     +
+ + + + + + + + + + + + + + + + + + + +

Prosze podac nazwe szkoly: 
START;
        $szkola = fopen('php://stdin', 'r');
        $szkola = trim(fgets($szkola));
        print <<< START

Dziekuje, trwa instalacja systemu Intersys Plan Lekcji...

START;

        print <<< START
Tworzenie tabeli: przedmioty

START;

        $isf->DbTblCreate('przedmioty', array(
            'przedmiot' => 'text not null'
        ));

        print <<< START
Tworzenie tabeli: sale

START;

        $isf->DbTblCreate('sale', array(
            'sala' => 'text not null'
        ));

        print <<< START
Tworzenie tabeli: przedmiot_sale

START;

        $isf->DbTblCreate('przedmiot_sale', array(
            'przedmiot' => 'text not null',
            'sala' => 'text not null'
        ));

        print <<< START
Tworzenie tabeli: klasy

START;

        $isf->DbTblCreate('klasy', array(
            'klasa' => 'text not null'
        ));

        print <<< START
Tworzenie tabeli: nauczyciele

START;

        $isf->DbTblCreate('nauczyciele', array(
            'imie_naz' => 'text not null',
            'skrot' => 'text not null'
        ));

        print <<< START
Tworzenie tabeli: nl_przedm

START;

        $isf->DbTblCreate('nl_przedm', array(
            'nauczyciel' => 'text not null',
            'przedmiot' => 'text not null'
        ));

        print <<< START
Tworzenie tabeli: nl_klasy

START;

        $isf->DbTblCreate('nl_klasy', array(
            'nauczyciel' => 'text not null',
            'klasa' => 'text not null'
        ));

        print <<< START
Tworzenie tabeli: rejestr

START;

        $isf->DbTblCreate('rejestr', array(
            'opcja' => 'text not null',
            'wartosc' => 'text'
        ));

        print <<< START
Tworzenie tabeli: planlek

START;

        $isf->DbTblCreate('planlek', array(
            'dzien' => 'text',
            'klasa' => 'text',
            'lekcja' => 'text',
            'przedmiot' => 'text',
            'sala' => 'text',
            'nauczyciel' => 'text',
            'skrot' => 'text'
        ));

        print <<< START
Tworzenie tabeli: uzytkownicy

START;

        $isf->DbTblCreate('uzytkownicy', array(
            'uid' => 'integer primary key autoincrement not null',
            'login' => 'text not null',
            'haslo' => 'text not null'
        ));

        print <<< START
Tworzenie tabeli: plan_grupy

START;

        $isf->DbTblCreate('plan_grupy', array(
            'dzien' => 'text',
            'lekcja' => 'text',
            'klasa' => 'text',
            'grupa' => 'text',
            'przedmiot' => 'text',
            'nauczyciel' => 'text',
            'skrot' => 'text',
            'sala' => 'text'
        ));

        print <<< START
Tworzenie tabeli: lek_godziny

Zakonczono tworzenie bazy danych!

START;

        $isf->DbTblCreate('lek_godziny', array(
            'lekcja' => 'text',
            'godzina' => 'text',
            'dl_prz' => 'text'
        ));

        print <<< START
Wypelnianie rejestru...

START;

        $isf->DbInsert('rejestr', array(
            'opcja' => 'edycja_danych',
            'wartosc' => '1'
        ));

        $isf->DbInsert('rejestr', array(
            'opcja' => 'ilosc_godzin_lek',
            'wartosc' => '1'
        ));

        $isf->DbInsert('rejestr', array(
            'opcja' => 'dlugosc_lekcji',
            'wartosc' => '45'
        ));

        $isf->DbInsert('rejestr', array(
            'opcja' => 'nazwa_szkoly',
            'wartosc' => $szkola
        ));

        $isf->DbInsert('rejestr', array(
            'opcja' => 'index_text',
            'wartosc' => '<h1>Witaj w Planie Lekcji</h1><p>Na początek proszę zmienić hasła do panelu administracyjnego
                oraz zmienić treść tej strony w górnym panelu użytkownika.</p><p>Dziękuję za skorzystanie z systemu Plan Lekcji</p>'
                ), false);

        $isf->DbInsert('rejestr', array(
            'opcja' => 'ilosc_grup',
            'wartosc' => '0'
        ));

        $isf->DbInsert('rejestr', array(
            'opcja' => 'installed',
            'wartosc' => '1'
        ));
        
        $isf->DbInsert('rejestr', array(
            'opcja' => 'app_ver',
            'wartosc' => '1.0.1'
        ));

        $pass = substr(md5(@date('Y:m:d')), 0, 8);

        print <<< START
Utworzenie administratora...
    
START;

        $isf->DbInsert('uzytkownicy', array(
            'login' => 'administrator',
            'haslo' => md5($pass = (rand(1, 100) . $pass))
        ));

        print <<< START

INSTALACJA ZAKONCZONA POWODZENIEM!
        
* Prosze zapisac dane oraz usunac plik install.php,
    aby kontynuowac prace z systemem
* Jezeli dostep do aplikacji jest poprzez inny adres niz http://[nazwa_hosta]/,
    prosze zmienic zmienna \$path w pliku index.php
* Dostep do panelu administracyjnego: http://[...]/index.php/admin
        
Prosze zapisac ponizsze dane, aby uzyskac dostep do panelu administratora

    Login: administrator
    Haslo: $pass

START;
        fgets(fopen('php://stdin', 'r'));
        ?>
    <?php endif; ?>
<?php endif; ?>