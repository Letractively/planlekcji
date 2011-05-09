<?php
/*
 * Plik instalacyjny Planu Lekcji
 */
require_once 'modules/isf/classes/kohana/isf.php';
$isf = new Kohana_Isf();
$isf->DbConnect();
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
<?php if ($r==1): ?>
    <html>
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
            <title>Instalacja Intersys Plan Lekcji</title>
            <link rel="stylesheet" type="text/css" href="lib/style.css"/>
        </head>
        <body>
            <img src="lib/logo.png"/>
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
                <link rel="stylesheet" type="text/css" href="lib/style.css"/>
            </head>
            <body>
                <img src="lib/logo.png"/>
                <h1>Instalator Intersys Plan Lekcji</h1>
                <h3>Uruchom plik <b>index.php</b> używając wiersza poleceń i php</h3>
                <p class="info">Więcej informacji w dokumentacji projektu</p>
            </body>
        </html>
    <?php else: ?>
        <?php
        print <<< START

+ + + + + + + + + + + + + + + + + + + +
+                                     +
+    I   N   T   E    R   S   Y   S   +  Wersja 1.0
+    P  L  A  N   L  E  K  C  J  I    +  BETA 3
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
            'nauczyciel' => 'text'
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
            'sala' => 'text'
        ));

        print <<< START
Tworzenie tabeli: lek_godziny
START;

        $isf->DbTblCreate('lek_godziny', array(
            'lekcja' => 'text',
            'godzina' => 'text'
        ));

        print <<< START
Tworzenie rejestru...
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
            'wartosc' => '<h1>Witaj w Planie Lekcji</h1><p>Prosze zmienic tresc tej strony</p>'
                ), false);

        $isf->DbInsert('rejestr', array(
            'opcja' => 'ilosc_grup',
            'wartosc' => '0'
        ));

        $isf->DbInsert('rejestr', array(
            'opcja' => 'installed',
            'wartosc' => '1'
        ));

        $pass = substr(md5(date()), 0, 8);

        print <<< START
Tworzenie administratora...
START;

        $isf->DbInsert('uzytkownicy', array(
            'login' => 'administrator',
            'haslo' => md5($pass)
        ));

        print <<< START

Dane administratora:

Login: administrator
Hasło: $pass
   
Instalacja zakonczona!
START;
        ?>
    <?php endif; ?>
<?php endif; ?>
