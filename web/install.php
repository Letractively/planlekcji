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
define('_I_SYSVER', 'trunk dev');
define('MOBILE_LIB_PATH', '/lib/jquery/mobile/');

require_once 'modules/isf/classes/kohana/isf.php'; // ISF1
require_once 'modules/isf/classes/isf2.php'; // ISF2
require_once 'application/planlekcji/core.php';

if (!file_exists('config.php')) {
    $r = 0;
} else {
    require_once 'config.php';
    if (defined('APP_DBSYS')) {
	if (APP_DBSYS == 'sqlite') {
	    $res = Isf2::Connect()->Select('rejestr')
			    ->Where(array('opcja' => 'installed'))->Execute()->fetchAll();
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
	<meta name="viewport" content="width=device-width, initial-scale=1"/>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<link rel="stylesheet" href="<?php echo MOBILE_LIB_PATH; ?>jquery.mobile-1.0.1.min.css" />
	<script src="<?php echo MOBILE_LIB_PATH; ?>jquery-1.6.4.min.js"></script>
	<script src="<?php echo MOBILE_LIB_PATH; ?>jquery.mobile-1.0.1.min.js"></script>        
	<script>
            $(document).ready(function() {
            });
        </script>
	<title>Instalator pakietu Internetowy Plan Lekcji <?php echo _I_SYSVER; ?></title>
    </head>
    <body>
	<div id="main" data-role="page" data-theme="a" data-content-theme="a">
	    <?php if ($r == 1): ?>
    	    <div id="title" data-role="header">
    		<h3>Instalacja systemu Internetowy Plan Lekcji</h3>
    	    </div>
    	    <div id="ct_step2_1" data-role="content" data-theme="c">
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
    	    </div>
	    <?php else: ?>
		<?php
		$valid_db = true;
		$valid_paths = true;
		if (!extension_loaded('pdo_sqlite') || !extension_loaded('pdo_pgsql')) {
		    $valid_db = false;
		}
		$paths_err = '';
		$paths = array('../resources', '../resources/timetables', 'application/logs', 'application/cache');
		foreach ($paths as $path) {
		    if (!is_writable($path)) {
			$paths_err .= '<li>Katalog <b>' . realpath($path) . '</b> musi posiadać prawa zapisu</li>';
			$valid_paths = false;
		    }
		}
		?>
		<?php if (!isset($_POST['step2'])): ?>

		    <div id="title" data-role="header">
			<h3>Instalacja systemu Internetowy Plan Lekcji - krok 1</h3>
		    </div>
		    <div id="ct_step2_1" data-role="content" data-theme="c">
			<?php
			$r = $_SERVER['REQUEST_URI'];
			$r = str_replace('index.php', '', $r);
			$r = str_replace('install.php', '', $r);
			$r = str_replace('?err', '', $r);
			$r = str_replace('?reinstall', '', $r);
			?>
			<h3>Instalator systetmu</h3>

			<?php if ($valid_db == false || $valid_paths == false): ?>
	    		<p>
	    		    <b>Wystąpiły błędy. Instalacja przerwana</b>
	    		</p>
			    <?php if ($valid_db == false): ?>
				<p>
				    Instalator wymaga obsługi <b>SQLite3</b> lub <b>PostgreSQL</b> w
				    wersjach <b>PDO</b>.</p>
			    <?php endif; ?>
			    <?php if ($valid_paths == false): ?>
				<ul>
				    <?php echo $paths_err; ?>
				</ul>
			    <?php endif; ?>
			<?php else: ?>

	    		<form action="" method="post" name="frmS1" id="frmS1">
	    		    <div data-role="fieldcontain">
	    			<label for="inpSzkola" id="lblSzkola">Nazwa szkoły</label>
	    			<input type="text" name="inpSzkola" id="inpSzkola" value=""/>
	    		    </div>
	    		    <div data-role="fieldcontain">
	    			<label for="inpPath" id="lglPath">Scieżka aplikacji</label>
	    			<input type="text" name="inpPath" id="inpPath" value="<?php echo $r; ?>"/>
	    		    </div>

	    		    <div data-role="collapsible" data-collapsed="false" data-content-theme="e">
	    			<h3>Ścieżka aplikacji - informacje</h3>
	    			To element ścieżki HTTP, dzięki której można dostać się do aplikacji.
	    			Wartość ta jest ustawiana automatycznie oraz nie zaleca się jej modyfikacji.
	    		    </div>

	    		    <div data-role="fieldcontain">
	    			<label for="dbtype" class="select">Typ bazy danych</label>
	    			<select name="dbtype" id="dbtype">
	    			    <option>SQLite</option>
	    			    <option>PgSQL</option>
	    			</select>
	    		    </div>

	    		    <input type="hidden" name="step2" value="true"/><p/>

	    		    <div data-role="collapsible" data-collapsed="true">
	    			<h3>Dane serwera PostgreSQL - nie dotyczy SQLite</h3>
	    			<div data-role="fieldcontain">
	    			    <label for="dbHost" id="dbHost">Host</label>
	    			    <input type="text" name="dbHost" id="dbHost" value=""/>
	    			</div>
	    			<div data-role="fieldcontain">
	    			    <label for="dbLogin" id="dbLogin">Użytkownik</label>
	    			    <input type="text" name="dbLogin" id="dbLogin" value=""/>
	    			</div>
	    			<div data-role="fieldcontain">
	    			    <label for="dbHaslo" id="dbHaslo">Hasło</label>
	    			    <input type="text" name="dbHaslo" id="dbHaslo" value=""/>
	    			</div>
	    			<div data-role="fieldcontain">
	    			    <label for="dbBaza" id="dbBaza">Nazwa bazy</label>
	    			    <input type="text" name="dbBaza" id="dbBaza" value=""/>
	    			</div>
	    		    </div>
	    		    <button type="submit" name="btnSubmit">Zainstaluj aplikację</button>
	    		</form>
			    <?php if (isset($_GET['err'])): ?>
				<span style="font-weight: bold; color:red;">Żadne pole nie może być puste!</p>
				<?php endif; ?>
			    <?php endif; ?>
		    </div>

		<?php else: ?>
		    <div id="title" data-role="header">
			<h3>Instalacja systemu Internetowy Plan Lekcji - krok 2</h3>
		    </div>
		    <div id="ct_step2_1" data-role="content" data-theme="c">
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
	    		<h3 class="notice">Dziękujemy za instalację IPL <?php echo _I_SYSVER; ?></h3>
	    		<h3>Twoje dane administratora</h3>
	    		<p><b>Login: </b>root</p>
	    		<p><b>Hasło: </b><?php echo $res['pass']; ?></p>
	    		<p><b>Token: </b><?php echo $res['token']; ?></p>
	    		<p class="info">Zapamiętaj dane do logowania oraz usuń pliki
			    <b>install.php</b> i <b>unixinstall.php</b>.</p>
			<?php else: ?>
	    		<h1>Instalacja zakończyła się niepowodzeniem!</h1>
	    		<p>Utwórz pusty plik config.php i nadaj mu prawa zapisu!</p>
			<?php endif; ?>
		    <?php endif; ?>
    	    </div>
	    <?php endif; ?>
	    <div id="footer" data-role="footer">
		<h3>&copy;<?php echo date('Y'); ?>, Internetowy Plan Lekcji</h3>
	    </div>
	</div>
    </body>
</html>