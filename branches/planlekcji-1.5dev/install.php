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
            <h3>Usuń plik <b>install.php</b> oraz <b>unixinstall.php</b>
                i zaloguj się, używając
                danych podanych przez instalator</h3>
            <?php if (!file_exists('config.php')): ?>
                <?php
                $r = $_SERVER['REQUEST_URI'];
                $r = str_replace('index.php', '', $r);
                $r = str_replace('install.php', '', $r);
                ?>
                <h3>Plik config.php nie istnieje! Proszę go utworzyć</h3>
                <p>Treść pliku config.php</p>
                <pre>
<?php echo htmlspecialchars('<?php') . PHP_EOL; ?>
<?php echo htmlspecialchars('$path = \'' . $r . '\';') . PHP_EOL; ?>
<?php echo htmlspecialchars('?>'); ?>
                </pre>
            <?php endif; ?>
        </body>
    </html>
    <?php exit; ?>
<?php else: ?>
    <?php if (!isset($_POST['step2'])): ?>
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
                <?php if ($_SERVER['SERVER_NAME'] != 'localhost' && $_SERVER['SERVER_NAME'] != '127.0.0.1'): ?>
                    <p class="error">
                        Aplikacja może zostać zainstalowana tylko wtedy, gdy ta
                        strona jest wywołana z komputera lokalnego.
                    </p>
                <?php else: ?>
                    <?php
                    $r = $_SERVER['REQUEST_URI'];
                    $r = str_replace('index.php', '', $r);
                    $r = str_replace('install.php', '', $r);
                    $r = str_replace('?err', '', $r);
                    ?>
                    <h3>Krok 1</h3>
                    <form action="" method="post">
                        <b>Nazwa szkoły: </b>
                        <input type="text" name="inpSzkola" size="80"/><p/>
                        <b>Ścieżka aplikacji*: </b>
                        <input type="text" name="inpPath" size="50" value="<?php echo $r; ?>"/>
                        <input type="hidden" name="step2" value="true"/><p/>
                        <p class="info">
                            Ścieżka aplikacji to ciąg znaków po nazwie hosta w pasku adresu
                            przeglądarki. System automatycznie dopasuje odpowiednią wartość.
                            Proszę nie zmieniać wartości tego pola chyba, że jest ona nieprawidłowa.
                        </p>
                        <button type="submit" name="btnSubmit">Zainstaluj aplikację</button>
                    </form>
                    <?php if (isset($_GET['err'])): ?>
                        <p class="error">Żadne pole nie może być puste!</p>
                    <?php endif; ?>
                <?php endif; ?>
            </body>
        </html>
    <?php else: ?>
        <?php
        if (empty($_POST['inpSzkola']) || $_POST['inpSzkola'] == ''):
            header('Location: install.php?err');
            exit;
        endif;
        echo '<link rel="stylesheet" type="text/css" href="lib/css/style.css"/>';
        echo '<h1>Proces instalacji</h1><p class="info">Na dole strony znajduja sie dane
            do logowania!</p><pre>';
        $szkola = $_POST['inpSzkola'];
        $a = fopen('config.php', 'w');
        if (!$a) {
            $ferr = true;
        } else {
            $file = '<?php' . PHP_EOL . '$path = \'' . $_POST['inpPath'] . '\';' . PHP_EOL . '?>';
            fputs($a, $file);
            fclose($a);
        }
        print <<< START

+ + + + + + + + + + + + + + + + + + + +
+                                     +
+    I   N   T   E    R   S   Y   S   +  Wersja 1.5
+    P  L  A  N   L  E  K  C  J  I    +  UNSTABLE
+                                     +
+ + + + + + + + + + + + + + + + + + + +


Trwa instalacja systemu Intersys Plan Lekcji...

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
Tworzenie tabeli: zast_id

START;

        $isf->DbTblCreate('zast_id', array(
            'zast_id' => 'integer primary key autoincrement not null',
            'dzien' => 'text',
            'za_nl' => 'text',
            'info' => 'text',
        ));

        print <<< START
Tworzenie tabeli: zastepstwa

START;

        $isf->DbTblCreate('zastepstwa', array(
            'zast_id' => 'text',
            'lekcja' => 'text',
            'przedmiot' => 'text',
            'nauczyciel' => 'text',
            'sala' => 'text',
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
            'wartosc' => '1.5 testing'
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
        
Prosze zapisac ponizsze dane, aby uzyskac dostep do panelu administratora

    Login: <b>administrator</b>
    Haslo: <b>$pass</b>

<a href="index.php">Strona glowna</a>
START;
        if ($ferr == true) {
echo '<br/><b>BŁĄD ZAPISU: config.php</b><br/>Prosze utworzyc plik config.php<br/>';
echo htmlspecialchars('<?php') . PHP_EOL;
echo htmlspecialchars('$path = \'' . $r . '\';') . PHP_EOL;
echo htmlspecialchars('?>');
        }
        echo '</pre>';
        ?>
    <?php endif; ?>
<?php endif; ?>