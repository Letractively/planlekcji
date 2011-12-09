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

if (!file_exists('config.php')) {
    $r = 0;
} else {
    require_once 'config.php';
    if (defined('APP_DBSYS')) {
	if (APP_DBSYS == 'sqlite') {
	    $isf = new Kohana_Isf();
	    $isf->Connect(APP_DBSYS);
	    $res = $isf->DbSelect('rejestr', array('*'), 'where opcja=\'installed\'');
	    if (count($res) == 1) {
		$r = 1;
	    } else {
		$r = 0;
	    }
	} else if (APP_DBSYS == 'pgsql') {
	    if (!isset($my_cfg)) {
		$r = 0;
	    }
	} else {
	    echo '<h1>System uszkodzony</h1><p>Prosze usunac plik <b>config.php</b>.</p>';
	    exit;
	}
    } else {
	$r = 0;
    }
}
if (isset($_GET['reinstall'])) {
    $r = 0;
}
?>
<!DOCTYPE html>
<html>
    <head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>Instalator pakietu Internetowy Plan Lekcji <?php echo _I_SYSVER; ?></title>
	<link rel="stylesheet" type="text/css" href="lib/css/style.css"/>
	<style>
	    body{
		background: #21638c;
		background-image: url('lib/images/image1.jpg'),
		    url('lib/images/image2.jpg');
		background-repeat: repeat-x, repeat;
	    }
	    pre{
		font-size: 10pt;
	    }
	    input, button, select{
		border: 1px solid;
		border-color: gray;
		font-size: 12pt;
		text-align: center;
	    }
	    span{
		font-size: 10pt;
	    }
	    div#main{
		text-align: center;
		width: 1000px;
		margin: 0 auto;
		background-color: white;
		padding-top: 10px;
		box-shadow: white 0px 0px 6px;
	    }
	</style>
    </head>
    <body onLoad="resizeContent();" onResize="resizeContent();">
	<div id="main">
	    <?php if ($r == 1): ?>

    	    <img src="lib/images/logo.png"/>
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
		    <h3 class="error">Błąd zapisu pliku config.php</h3>
		    <p>Proszę utworzyć plik config.php w katalogu głównym
			aplikacji o poniższej treści.</p>
		    <?php
		    $str = <<< START
   <?php
   \$path = '$r';
   define('APP_PATH', \$path);
   define('APP_DBSYS' 'nazwa_bazy'); // sqlite, pgsql
   // gdy baza danych PgSQL uzupelnij koniecznie!
   \$my_cfg['host'] = ''; // nazwa hosta bazy danych PostgreSQL
   \$my_cfg['database'] = ''; // nazwa bazy danych
   \$my_cfg['user'] = ''; // nazwa uzytkownika bazy
   \$my_cfg['password'] = ''; // haslo bazy danych
   ?>
START;
		    highlight_string($str);
		    ?>

		<?php endif; ?>
	    <?php else: ?>
		<?php if (!isset($_POST['step2'])): ?>

		    <img src="lib/images/logo.png"/>
		    <h1>Internetowy Plan Lekcji <?php echo _I_SYSVER; ?></h1>
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
		    <h3>Instalator systetmu</h3>
		    <form action="" method="post">
			<b>Nazwa szkoły: </b>
			<input type="text" name="inpSzkola" size="80"/><p/>
			<b>Ścieżka aplikacji*: </b>
			<input type="text" name="inpPath" size="50" value="<?php echo $r; ?>"/><p/>
			<b>Typ bazy danych: </b>
			<select name="dbtype">
			    <option>SQLite</option>
			    <option>PgSQL</option>
			</select>
			<input type="hidden" name="step2" value="true"/><p/>
			<p class="info">
			    Ścieżka aplikacji to ciąg znaków po nazwie hosta w pasku adresu
			    przeglądarki. System automatycznie dopasuje odpowiednią wartość.
			    Proszę nie zmieniać wartości tego pola chyba, że jest ona nieprawidłowa.
			</p>
			<h3>Dane serwera PostgreSQL</h3>
			<p>W przypadku wybrania opcji <b>PgSQL</b></p>
			<p><b>Host: <input type="text" name="dbHost" size="50"/></b></p>
			<p><b>Login: <input type="text" name="dbLogin" size="50"/></b></p>
			<p><b>Hasło: <input type="text" name="dbHaslo" size="50"/></b></p>
			<p><b>Baza danych: <input type="text" name="dbBaza" size="50"/></b></p>
			<button type="submit" name="btnSubmit">Zainstaluj aplikację</button>
		    </form>
		    <?php if (isset($_GET['err'])): ?>
	    	    <p class="error">Żadne pole nie może być puste!</p>
		    <?php endif; ?>

		<?php else: ?>
		    <?php
		    if ($_POST['dbtype'] == 'PgSQL') {
			if (empty($_POST['inpSzkola']) || $_POST['inpSzkola'] == ''
				|| empty($_POST['dbLogin']) || empty($_POST['dbHaslo'])
				|| empty($_POST['dbBaza']) || empty($_POST['dbHost'])) {
			    header('Location: install.php?err');
			    exit;
			}

			$szkola = $_POST['inpSzkola'];
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
			    $file = '<?php' . PHP_EOL . '$path = \'' . $_POST['inpPath'] . '\';' . PHP_EOL . 'define(\'APP_DBSYS\', \'pgsql\');' . PHP_EOL;
			    $file .= '$my_cfg = array(\'host\'=>\'' . $_POST['dbHost'] . '\',\'user\'=>\'' . $_POST['dbLogin'] . '\', \'password\'=>\'' . $_POST['dbHaslo'] . '\',\'database\'=>\'' . $_POST['dbBaza'] . '\',';
			    $file .= ');' . PHP_EOL . '$GLOBALS[\'my_cfg\']=$my_cfg; ' . PHP_EOL;
			    $file .= 'define(\'APP_PATH\', $path);' . PHP_EOL . '?>';
			    fputs($a, $file);
			    fclose($a);
			    require_once 'config.php';
			    $App_Install = new Core_Install();
			    $App_Install->Connect('pgsql');
			    $res = $App_Install->DbInit($_POST['inpSzkola'], _I_SYSVER);
			}
		    } else {
			if (empty($_POST['inpSzkola']) || $_POST['inpSzkola'] == '') {
			    header('Location: install.php?err');
			    exit;
			}
			$a = fopen('config.php', 'w');
			if (!$a) {
			    $ferr = true;
			} else {
			    $file = '<?php' . PHP_EOL . '$path = \'' . $_POST['inpPath'] . '\';' . PHP_EOL . 'define(\'APP_DBSYS\', \'sqlite\');' . PHP_EOL . 'define(\'APP_PATH\', $path);' . PHP_EOL . '?>';
			    fputs($a, $file);
			    fclose($a);
			}
			$App_Install = new Core_Install();
			$App_Install->Connect('sqlite');
			$res = $App_Install->DbInit($_POST['inpSzkola'], _I_SYSVER);
		    }
		    ?>
		    <?php if (!isset($ferr)): ?>
	    	    <img src="lib/images/logo.png" style="height: 80px;"/>
	    	    <h1>Instalator pakietu Internetowy Plan Lekcji <?php echo _I_SYSVER; ?></h1>
	    	    <h3 class="notice">Dziękujemy za instalację</h3>
	    	    <h3>Twoje dane administratora</h3>
	    	    <p><b>Login: </b>root</p>
	    	    <p><b>Hasło: </b><?php echo $res['pass']; ?></p>
	    	    <p><b>Token: </b><?php echo $res['token']; ?></p>
	    	    <p class="info">Zapamiętaj dane do logowania oraz usuń pliki <b>install.php</b> oraz <b>unixinstall.php</b>,
	    		a następnie przejdź do <a href="index.php">strony głównej</a>.</p>
		    <?php else: ?>
	    	    <h1>Instalacja zakończyła się niepowodzeniem!</h1>
	    	    <p>Utwórz pusty plik config.php i nadaj mu prawa zapisu!</p>
		    <?php endif; ?>
		<?php endif; ?>
	    <?php endif; ?>
	</div>
	<script type="text/javascript">
	    function resizeContent() { 
		var contentDiv = document.getElementById('main');
		
		// This may need to be done differently on IE than FF, but you get the idea. 
		var viewPortHeight = window.innerHeight-10; 
		contentDiv.style.height =  
		    Math.max(viewPortHeight, contentDiv.clientHeight) + 'px';
	    }
	</script>
    </body>
</html>