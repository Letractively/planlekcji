<?php
/**
 * Instalator Planu Lekcji
 * 
 * @author Michał Bocian <mhl.bocian@gmail.com>
 * @version 1.5
 * @license GNU GPL v3
 * @package main\install
 */
/**
 * Wersja instalatora
 */
define('_I_SYSVER', '1.10 dev');

require_once 'modules/isf/classes/kohana/isf.php'; # pobiera framework ISF
require_once 'application/planlekcji/core.php';
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
            <title>Internetowy Plan Lekcji <?php echo _I_SYSVER; ?></title>
            <link rel="stylesheet" type="text/css" href="lib/css/style.css"/>
            <style>
                body{
                    background-image: url('lib/images/background.png');
                }
            </style>
        </head>
        <body>
            <div style="text-align: center;">
                <img src="lib/images/logo.png" style="height: 80px;"/>
                <h1 class="notice">Instalacja zakończona powodzeniem!</h1>
                <p>Proszę usunąć plik <b>install.php</b> oraz <b>unixinstall.php</b>
                    i zaloguj się, używając
                    danych podanych przez instalator</p>
                <?php if (!file_exists('config.php')): ?>
                    <?php
                    $r = $_SERVER['REQUEST_URI'];
                    $r = str_replace('index.php', '', $r);
                    $r = str_replace('install.php', '', $r);
                    $r = str_replace('?reinstall', '', $r);
                    ?>
                    <h3 class="error">
                        Błąd zapisu pliku <b>config.php</b>
                    </h3>
                    <p>Proszę utworzyć plik config.php w katalogu głównym
                        aplikacji o powyższej treści.</p>
                    <p style="font-size: 12pt;">
                        <?php
                        $str = <<< START
   <?php
   \$path = '$r';
   define('APP_PATH', \$path);
   ?>
START;
                        highlight_string($str);
                        ?>
                    </p>
                <?php endif; ?>
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
        <!DOCTYPE html>
        <html>
            <head>
                <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
                <title>Internetowy Plan Lekcji <?php echo _I_SYSVER; ?></title>
                <link rel="stylesheet" type="text/css" href="lib/css/style.css"/>
                <style>
                    body{
						background-image: url('lib/images/background.png');
					}
                    input, button{
                        font-size: 14pt;
                    }
                </style>
            </head>
            <body>
                <div style="text-align: center;">
                    <img src="lib/images/logo.png"/>
                    <h1>Internetowy Plan Lekcji <?php echo _I_SYSVER; ?></h1>
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
                        <?php
                        $r = $_SERVER['REQUEST_URI'];
                        $r = str_replace('index.php', '', $r);
                        $r = str_replace('install.php', '', $r);
                        $r = str_replace('?err', '', $r);
                        $r = str_replace('?reinstall', '', $r);
                        ?>
                        <h3>Instalator systemu</h3>
                        <form action="" method="post">

                            <b>Nazwa szkoły: </b>
                            <input type="text" name="inpSzkola" size="80"/><p/>
                            <b>Ścieżka aplikacji*: </b>
                            <input type="text" name="inpPath" size="50" value="<?php echo $r; ?>"/>
                            <input type="hidden" name="step2" value="true"/><p/>
                            <?php if (isset($_GET['err'])): ?>
                                <p class="error">Żadne pole nie może być puste!</p>
                            <?php endif; ?>
                            <p>
                                Ścieżka aplikacji to ciąg znaków po nazwie hosta w pasku adresu
                                przeglądarki. System automatycznie dopasuje odpowiednią wartość.
                                Proszę nie zmieniać wartości tego pola chyba, że jest ona nieprawidłowa.
                            </p>
                            <button type="submit" name="btnSubmit">Zainstaluj aplikację</button>
                        </form>
                    <?php endif; ?>
                </div>
            </body>
        </html>
        <?php
    /**
     * Gdy formularz zostal wyslany
     */
    else:
        ?>
        <?php
        /**
         * Podstawowa walidacja danych
         */
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
         * Przystapienie do instalacji systemu
         */
        $App_Install = new \com\planlekcji\Install('sqlite');
        $App_Install->Connect('sqlite');
        $res = $App_Install->DbInit($_POST['inpSzkola'], _I_SYSVER);
        ?>
        <html>
            <head>
                <meta charset="UTF-8"/>
                <link rel="stylesheet" type="text/css" href="lib/css/style.css"/>
                <title>Internetowy Plan Lekcji <?php echo _I_SYSVER; ?></title>
                <style type="text/css">
                    body{
						background-image: url('lib/images/background.png');
					}
                    span{
                        font-size: 10pt;
                    }
                </style>
            </head>
            <body>
                <div style="text-align: center;">
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
   ?>
START;
                            highlight_string($str);
                            ?>
                            <p>Proszę utworzyć plik config.php w katalogu głównym
                                aplikacji o powyższej treści.</p>
                        </fieldset>
                    <?php endif; ?>
                </div>
            </body>
        </html>
    <?php endif; ?>
<?php endif; ?>