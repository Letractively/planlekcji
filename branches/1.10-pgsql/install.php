<?php
/*
 * Plik instalacyjny Planu Lekcji
 */
 /**
 * Wersja instalatora
 */
define('_I_SYSVER', '1.10 dev');

require_once 'modules/isf/classes/kohana/isf.php'; # pobiera framework ISF
require_once 'application/planlekcji/core.php';

if (!file_exists('config.php')) {
    $r = 0;
} else {
    if (!isset($my_cfg)) {
        $r = 0;
    } else {
        $isfa = new Kohana_Isf();
        $isfa->DbConnect();
        $res = $isfa->DbSelect('rejestr', array('*'), 'where opcja=\'installed\'');
        if (count($res) >= 1) {
            $r = 1;
        } else {
            $r = 0;
        }
    }
}
if (isset($_GET['reinstall'])) {
    $r = 0;
}
?>
<?php if ($r == 1): ?>
    <html>
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
            <title>Instalator pakietu Internetowy Plan Lekcji <?php echo _I_SYSVER; ?></title>
            <link rel="stylesheet" type="text/css" href="lib/css/style.css"/>
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
   \$my_cfg['host'] = ''; // nazwa hosta bazy danych PostgreSQL
   \$my_cfg['database'] = ''; // nazwa bazy danych
   \$my_cfg['user'] = ''; // nazwa uzytkownika bazy
   \$my_cfg['password'] = ''; // haslo bazy danych
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
<?php else: ?>
    <?php if (!isset($_POST['step2'])): ?>
        <html>
            <head>
                <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
                <title>Instalator pakietu Internetowy Plan Lekcji <?php echo _I_SYSVER; ?></title>
                <link rel="stylesheet" type="text/css" href="lib/css/style.css"/>
                <style type="text/css">
                    pre{
                        font-size: 10pt;
                    }
                </style>
            </head>
            <body>
                <img src="lib/images/logo.png"/>
                <h1>Instalator pakietu Internetowy Plan Lekcji <?php echo _I_SYSVER; ?></h1>
                <?php if ($_SERVER['SERVER_NAME'] != 'localhost' && $_SERVER['SERVER_NAME'] != '127.0.0.1'): ?>
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
                        <fieldset style="margin: 10px; max-width: 50%">
                            <legend><b>Dane serwera PostgreSQL</b></legend>
                            <p><b>Host: <input type="text" name="dbHost" size="50"/></b></p>
                            <p><b>Login: <input type="text" name="dbLogin" size="50"/></b></p>
                            <p><b>Hasło: <input type="password" name="dbHaslo" size="50"/></b></p>
                            <p><b>Baza danych: <input type="text" name="dbBaza" size="50"/></b>
                                baza musi istnieć</p>
                        </fieldset>
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
        if (empty($_POST['inpSzkola']) || $_POST['inpSzkola'] == ''
                || empty($_POST['dbLogin']) || empty($_POST['dbHaslo'])
                || empty($_POST['dbBaza']) || empty($_POST['dbHost'])):
            header('Location: install.php?err');
            exit;
        endif;

        $szkola = $_POST['inpSzkola'];
        $isf = new Kohana_Isf();
        $customvars = array(
            'host' => $_POST['dbHost'],
            'user' => $_POST['dbLogin'],
            'password' => $_POST['dbHaslo'],
            'database' => $_POST['dbBaza'],
        );
		
        $a = fopen('config.php', 'w');
        if (!$a) {
            $ferr = true;
        } else {
            $file = '<?php' . PHP_EOL . '$path = \'' . $_POST['inpPath'] . '\';' . PHP_EOL;
            $file .= '$my_cfg = array(\'host\'=>\'' . $_POST['dbHost'] . '\',\'user\'=>\'' . $_POST['dbLogin'] . '\', \'password\'=>\'' . $_POST['dbHaslo'] . '\',\'database\'=>\'' . $_POST['dbBaza'] . '\',';
            $file .= ');' . PHP_EOL . '$GLOBALS[\'my_cfg\']=$my_cfg; ' . PHP_EOL;
            $file .= 'define(\'APP_PATH\', $path);' . PHP_EOL . '?>';
            fputs($a, $file);
            fclose($a);
        }
		
		/**
         * Przystapienie do instalacji systemu
         */
        $App_Install = new Core_Install('sqlite');
        $App_Install->Connect('sqlite');
        $res = $App_Install->DbInit($_POST['inpSzkola'], _I_SYSVER);
        ?>
        <html>
            <head>
                <meta charset="UTF-8"/>
                <link rel="stylesheet" type="text/css" href="lib/css/style.css"/>
                <title>Instalator pakietu Internetowy Plan Lekcji <?php echo _I_SYSVER; ?></title>
                <style type="text/css">
                    span{
                        font-size: 10pt;
                    }
                </style>
            </head>
            <body>
                <img src="lib/images/logo.png" style="height: 80px;"/>
                <h1>Instalator pakietu Internetowy Plan Lekcji <?php echo _I_SYSVER; ?></h1>
                    <h3 class="notice">Dziękujemy za instalację</h3>
                    <h3>Twoje dane administratora</h3>
                    <p><b>Login: </b>root</p>
                    <p><b>Hasło: </b><?php echo $res['pass']; ?></p>
                    <p><b>Token: </b><?php echo $res['token']; ?></p>
                    <p class="info">Zapamiętaj dane do logowania oraz usuń pliki <b>install.php</b> oraz <b>unixinstall.php</b>,
                        a następnie przejdź do <a href="index.php">strony głównej</a>.</p>
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
   \$my_cfg['host'] = ''; // nazwa hosta bazy danych PostgreSQL
   \$my_cfg['database'] = ''; // nazwa bazy danych
   \$my_cfg['user'] = ''; // nazwa uzytkownika bazy
   \$my_cfg['password'] = ''; // haslo bazy danych
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