<?php
/**
 * Instalator Planu Lekcji
 * 
 * @author Michał Bocian <mhl.bocian@gmail.com>
 * @version 1.5
 * @license GNU GPL v3
 * @package main\install
 */
require_once 'modules/isf/classes/kohana/isf.php'; # pobiera framework ISF
$isf = new Kohana_Isf();
$isf->DbConnect();
/**
 * Sprawdza czy istnieje tabela rejestr
 */
$ctb = $isf->DbSelect('sqlite_master', array('*'), 'where name="rejestr"');
if (count($ctb) != 0) { // gdy istnieje
    $res = $isf->DbSelect('rejestr', array('*'), 'where opcja="installed"');
    if (count($res) >= 1) {
        $r = 1;
    }
} else { // gdy nie istnieje
    $r = 0;
}
if (isset($_GET['reinstall'])) {
    $r = 0;
}
?>
<?php
/**
 * Gdy istnieje tabela rejestr, oznacza ze zostal pakiet zainstalowany
 */
if ($r == 1):
    ?>
    <html>
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
            <title>Instalacja Internetowy Plan Lekcji</title>
            <link rel="stylesheet" type="text/css" href="lib/css/style.css"/>
            <style type="text/css">
                span{
                    font-size: 10pt;
                }
            </style>
        </head>
        <body>
            <img src="lib/images/logo.png" style="height: 80px;"/>
            <h1 class="notice">Instalacja zakończona powodzeniem!</h1>
            <p class="info">Usuń plik <b>install.php</b> oraz <b>unixinstall.php</b>
                i zaloguj się, używając
                danych podanych przez instalator</p>
            <?php if (!file_exists('config.php')): ?>
                <?php
                $r = $_SERVER['REQUEST_URI'];
                $r = str_replace('index.php', '', $r);
                $r = str_replace('install.php', '', $r);
                $r = str_replace('?reinstall', '', $r);
                ?>
                <fieldset style="max-width: 50%;">
                    <legend>
                        <p class="error">
                            Błąd zapisu pliku <b>config.php</b>
                        </p>
                    </legend>
                    <?php
                    $str = <<< START
   <?php
   \$path = '$r';
   define('APP_PATH', \$path);
   ?>
START;
                    highlight_string($str);
                    ?>
                    <p>Proszę utworzyć plik config.php w katalogu głównym
                        aplikacji o powyższej treści.</p>
                </fieldset>
            <?php endif; ?>
            <div id="foot">
                <p>
                    <img src="lib/images/gplv3.png" alt="GNU GPL v3 logo"/>
                    <b>Plan lekcji</b> |
                    <a href="http://planlekcji.googlecode.com" target="_blank">strona projektu Plan Lekcji</a></p>
            </div>
        </body>
    </html>
    <?php exit; ?>
    <?php
/**
 * Gdy nie ma tabeli
 */
else:
    ?>
    <?php
    /**
     * Sprawdza czy zostal wyslany formularz instalacji
     * -- nie
     */
    if (!isset($_POST['step2'])):
        ?>
        <html>
            <head>
                <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
                <title>Instalacja Internetowy Plan Lekcji</title>
                <link rel="stylesheet" type="text/css" href="lib/css/style.css"/>
                <style type="text/css">
                    pre{
                        font-size: 10pt;
                    }
                </style>
            </head>
            <body>
                <img src="lib/images/logo.png"/>
                <h1>Instalator pakietu Internetowy Plan Lekcji 1.5</h1>
                <?php
                /**
                 * Wymaga instalacji z hosta lokalnego
                 */
                if ($_SERVER['SERVER_NAME'] != 'localhost' && $_SERVER['SERVER_NAME'] != '127.0.0.1'):
                    ?>
                    <p class="error">
                        Aplikacja może zostać zainstalowana tylko wtedy, gdy ta
                        strona jest wywołana z komputera lokalnego.
                    </p>
                <?php else: ?>
                    <?php if (isset($_GET['reinstall'])): ?>
                        <p class="info">Reinstalacja usunie wszystkich użytkowników, wszystkie dane
                            systemu zostaną zachowane.</p>
                    <?php endif; ?>
                    <?php
                    $r = $_SERVER['REQUEST_URI'];
                    $r = str_replace('index.php', '', $r);
                    $r = str_replace('install.php', '', $r);
                    $r = str_replace('?err', '', $r);
                    $r = str_replace('?reinstall', '', $r);
                    ?>
                    <h3>Krok 1 - wprowadzanie danych</h3>
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
        <?php
    /**
     * Gdy formularz zostal wyslany
     */
    else:
        ?>
        <?php
        if (empty($_POST['inpSzkola']) || $_POST['inpSzkola'] == ''):
            header('Location: install.php?err');
            exit;
        endif;
        $szkola = $_POST['inpSzkola'];
        $a = fopen('config.php', 'w');
        /**
         * Czy udalo sie utworzyc plik config.php
         */
        if (!$a) {
            $ferr = true;
        } else {
            $file = '<?php' . PHP_EOL . '$path = \'' . $_POST['inpPath'] . '\';' . PHP_EOL;
            $file .= 'define(\'APP_PATH\', $path);' . PHP_EOL;
            $file .= '?>';
            fputs($a, $file);
            fclose($a);
        }
        /**
         * Gdy instalacja awaryjna (na istniejaca instalacje),
         * wowczas usuwa starych uzytkownikow
         */
        $isf->DbDelete('rejestr', 'opcja like \'%\'');
        $isf->DbDelete('uzytkownicy', 'login like \'%\'');
        $isf->DbDelete('tokeny', 'token like \'%\'');
        $isf->DbTblCreate('przedmioty', array(
            'przedmiot' => 'text not null'
        ));

        $isf->DbTblCreate('sale', array(
            'sala' => 'text not null'
        ));

        $isf->DbTblCreate('przedmiot_sale', array(
            'przedmiot' => 'text not null',
            'sala' => 'text not null'
        ));

        $isf->DbTblCreate('klasy', array(
            'klasa' => 'text not null'
        ));

        $isf->DbTblCreate('nauczyciele', array(
            'imie_naz' => 'text not null',
            'skrot' => 'text not null'
        ));

        $isf->DbTblCreate('nl_przedm', array(
            'nauczyciel' => 'text not null',
            'przedmiot' => 'text not null'
        ));

        $isf->DbTblCreate('nl_klasy', array(
            'nauczyciel' => 'text not null',
            'klasa' => 'text not null'
        ));

        $isf->DbTblCreate('rejestr', array(
            'opcja' => 'text not null',
            'wartosc' => 'text'
        ));

        $isf->DbTblCreate('planlek', array(
            'dzien' => 'text',
            'klasa' => 'text',
            'lekcja' => 'text',
            'przedmiot' => 'text',
            'sala' => 'text',
            'nauczyciel' => 'text',
            'skrot' => 'text'
        ));

        $isf->DbTblCreate('uzytkownicy', array(
            'uid' => 'integer primary key autoincrement not null',
            'login' => 'text not null',
            'haslo' => 'text not null',
            'webapi_token' => 'text',
            'webapi_timestamp' => '',
            'ilosc_prob' => ''
        ));

        $isf->DbTblCreate('log', array(
            'id' => 'integer primary key autoincrement not null',
            'data' => 'text not null',
            'modul' => 'text not null',
            'wiadomosc' => 'text',
        ));

        $isf->DbTblCreate('tokeny', array(
            'login' => 'text',
            'token' => 'text',
        ));

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

        $isf->DbTblCreate('zast_id', array(
            'zast_id' => 'integer primary key autoincrement not null',
            'dzien' => 'text',
            'za_nl' => 'text',
            'info' => 'text',
        ));

        $isf->DbTblCreate('zastepstwa', array(
            'zast_id' => 'text',
            'lekcja' => 'text',
            'przedmiot' => 'text',
            'nauczyciel' => 'text',
            'sala' => 'text',
        ));

        $isf->DbTblCreate('lek_godziny', array(
            'lekcja' => 'text',
            'godzina' => 'text',
            'dl_prz' => 'text'
        ));

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
            'wartosc' => '<h1>Witaj w Planie Lekcji 1.5</h1><p>Na początek proszę zmienić hasła do panelu administracyjnego
                oraz zmienić treść tej strony w górnym panelu użytkownika.</p><p>Dziękujemy za skorzystanie z systemu Plan Lekcji</p>'
                ), false);

        $isf->DbInsert('rejestr', array(
            'opcja' => 'ilosc_grup',
            'wartosc' => '0'
        ));

        $isf->DbInsert('rejestr', array(
            'opcja' => 'godz_rozp_zaj',
            'wartosc' => '08:00'
        ));

        $isf->DbInsert('rejestr', array(
            'opcja' => 'installed',
            'wartosc' => '1'
        ));

        $isf->DbInsert('rejestr', array(
            'opcja' => 'app_ver',
            'wartosc' => '1.5 dev classic'
        ));

        $isf->DbInsert('rejestr', array(
            'opcja' => 'randtoken_version',
            'wartosc' => '1.5 dev classic'
        ));

        $isf->DbInsert('log', array(
            'data' => date('d.m.Y H:i:s'),
            'modul' => 'plan.install',
            'wiadomosc' => 'Instalacja systemu'
        ));

        $isf->DbInsert('log', array(
            'data' => date('d.m.Y H:i:s'),
            'modul' => 'plan.install',
            'wiadomosc' => 'Tworzenie administratora'
        ));

        $pass = substr(md5(@date('Y:m:d')), 0, 8);
        $pass = rand(1, 100) . $pass;

        $isf->DbInsert('uzytkownicy', array(
            'login' => 'root',
            'haslo' => md5('plan' . sha1('lekcji' . $pass)),
        ));

        $token = substr(md5(time() . 'plan'), 0, 6);

        $isf->DbInsert('tokeny', array('login' => 'root', 'token' => md5('plan' . $token)));
        ?>
        <html>
            <head>
                <meta charset="UTF-8"/>
                <link rel="stylesheet" type="text/css" href="lib/css/style.css"/>
                <title>Instalator pakietu Internetowy Plan Lekcji 1.5</title>
                <style type="text/css">
                    span{
                        font-size: 10pt;
                    }
                </style>
            </head>
            <body>
                <img src="lib/images/logo.png" style="height: 80px;"/>
                <h1>Instalator pakietu Internetowy Plan Lekcji 1.5</h1><h3>Krok 2: instalacja</h3>
                <fieldset style="max-width: 50%;">
                    <legend>Dane administratora</legend>
                    <p><b>Login: </b>root</p>
                    <p><b>Hasło: </b><?php echo $pass; ?></p>
                    <p><b>Token: </b><?php echo $token; ?></p>
                    <p class="info">Zapamiętaj dane do logowania oraz usuń pliki <b>install.php</b> oraz <b>unixinstall.php</b>,
                        a następnie przejdź do <a href="index.php">strony głównej</a>.</p>
                </fieldset>
                <?php if (!file_exists('config.php')): ?>
                    <?php
                    $r = $_SERVER['REQUEST_URI'];
                    $r = str_replace('index.php', '', $r);
                    $r = str_replace('install.php', '', $r);
                    $r = str_replace('?reinstall', '', $r);
                    ?>
                    <fieldset style="max-width: 50%;">
                        <legend>
                            <p class="error">
                                Błąd zapisu pliku <b>config.php</b>
                            </p>
                        </legend>
                        <?php
                        $str = <<< START
   <?php
   \$path = '$r';
   define('APP_PATH', \$path);
   ?>
START;
                        highlight_string($str);
                        ?>
                        <p>Proszę utworzyć plik config.php w katalogu głównym
                            aplikacji o powyższej treści.</p>
                    </fieldset>
                <?php endif; ?>
                <div id="foot">
                    <p>
                        <img src="lib/images/gplv3.png" alt="GNU GPL v3 logo"/>
                        <b>Plan lekcji</b> |
                        <a href="http://planlekcji.googlecode.com" target="_blank">strona projektu Plan Lekcji</a></p>
                </div>
            </body>
        </html>
    <?php endif; ?>
<?php endif; ?>