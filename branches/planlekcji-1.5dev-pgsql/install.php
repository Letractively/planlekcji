<?php
/*
 * Plik instalacyjny Planu Lekcji
 */
require_once 'modules/isf/classes/kohana/isf.php';
if (!file_exists('config.php')) {
    $r = 0;
} else {
	if(!isset($my_cfg)){
		$r = 0;
	}else{
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
                <h1>Instalator Intersys Plan Lekcji - MySQL</h1>
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
                        <fieldset style="margin: 10px; max-width: 50%">
                            <legend><b>Dane serwera MySQL</b></legend>
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
        $isf->DbConnect($customvars);
        $a = fopen('config.php', 'w');
        if (!$a) {
            $ferr = true;
        } else {
            $file = '<?php' . PHP_EOL . '$path = \'' . $_POST['inpPath'] . '\';' . PHP_EOL;
            $file .= '$my_cfg = array(\'host\'=>\'' . $_POST['dbHost'] . '\',\'user\'=>\'' . $_POST['dbLogin'] . '\', \'password\'=>\'' . $_POST['dbHaslo'] . '\',\'database\'=>\'' . $_POST['dbBaza'] . '\',';
            $file .= ');' . PHP_EOL . '$GLOBALS[\'my_cfg\']=$my_cfg; ' . PHP_EOL;
			$file .= 'define(\'APP_PATH\', $path);'. PHP_EOL . '?>';
            fputs($a, $file);
            fclose($a);
        }
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
            'uid' => 'numeric not null',
            'login' => 'text not null',
            'haslo' => 'text not null',
            'webapi_token' => 'text',
            'webapi_timestamp' => 'text',
            'ilosc_prob' => 'numeric'
        ));

        $isf->DbTblCreate('log', array(
            'id' => 'serial',
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
            'zast_id' => 'serial',
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
            'wartosc' => '1.5.0 rtm 201107141355'
        ));

        $isf->DbInsert('rejestr', array(
            'opcja' => 'randtoken_version',
            'wartosc' => '1.5.0 rtm 201107141355'
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
			'uid' => 1,
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
            </head>
            <body>
                <img src="lib/images/logo.png"/>
                <h1>Instalator pakietu Internetowy Plan Lekcji 1.5</h1><h3>Krok 2: instalacja</h3>
                <h3>Dane administratora</h3>
                <p><b>Login: </b>root</p>
                <p><b>Hasło: </b><?php echo $pass; ?></p>
                <p><b>Token: </b><?php echo $token; ?></p>
                <p class="info">Zapamiętaj dane do logowania oraz usuń pliki <b>install.php</b> oraz <b>unixinstall.php</b>,
                			a następnie przejdź do <a href="index.php">strony głównej</a>.</p>
                    <?php
                    /**
                     * Gdy plik config.php nie zostal zapisany
                     */
                    if ($ferr == true) {
                        echo '<pre><b>BŁĄD ZAPISU: config.php</b><br/>Prosze utworzyc plik config.php<br/>';
                        echo htmlspecialchars('<?php') . PHP_EOL;
                        echo htmlspecialchars('$path = \'' . $r . '\';') . PHP_EOL;
                        echo htmlspecialchars('define(\'APP_PATH\', $path);') . PHP_EOL;
                        echo htmlspecialchars('?>');
                        echo '</pre>';
                    }
                    ?>
            </body>
        </html>
    <?php endif; ?>
<?php endif; ?>