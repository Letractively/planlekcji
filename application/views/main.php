<?php
/*
 * Strona glowna Planu Lekcji
 * 
 * @author Michal Bocian <mhl.bocian@gmail.com>
 * 
 */
/**
 * Instrukcje dotyczace zmiennych w szablone, gdy
 * nie sa zdefiniowane, ustawia je jako puste (null)
 */
if (!isset($content))
    $content = null;
if (!isset($_SESSION['token']))
    $_SESSION['token'] = null;
if (!isset($script))
    $script = null;
if (!isset($bodystr))
    $bodystr = null;
$appver = App_Globals::getRegistryKey('app_ver');
?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>Plan lekcji - <?php echo App_Globals::getRegistryKey('nazwa_szkoly'); ?></title>

        <!-- [SEKCJA]: JS,CSS,META -->
	<?php echo $script; ?>
	<link rel="stylesheet" type="text/css" href="<?php echo URL::base() ?>lib/css/themes/<?php echo $_SESSION['app_theme']; ?>.css"/>
        <link rel="stylesheet" type="text/css" href="<?php echo URL::base() ?>lib/css/style.css"/>
	<?php
	$isf = new Kohana_Isf();
	$isf->IE9_faviconset();
	$isf->IE9_WebAPP('Internetowy Plan Lekcji', 'Uruchom IPL', APP_PATH);
	$isf->IE9_apptask('Logowanie', 'index.php/admin/login');
	if (App_Globals::getSysLv() == 3) {
	    $isf->IE9_apptask('Zestawienie planów', 'index.php/podglad/zestawienie');
	    $isf->IE9_apptask('Zastępstwa', 'index.php/zastepstwa/index');
	}
	echo $isf->IE9_make();
	if (isset($_SESSION['token'])) {
	    $zadmin = time() + 10 * 60;
	    $toktime = strtotime($_SESSION['token_time']);
	    if ($zadmin > $toktime) {
		$bodystr = 'onLoad="alert(\'RAND_TOKEN: token wygaśnie za chwilę\\nProszę go odnowić!\');"';
	    }
	}
	?>
        <style>
            body{
		background: #21638c;
		background-image: url('<?php echo URL::base(); ?>lib/images/image1.jpg'),
		    url('<?php echo URL::base(); ?>lib/images/image2.jpg');
		background-repeat: repeat-x, repeat;
            }
	    <?php if ($_SESSION['token'] != null): ?>
    	    div#pnlCenter{
    		width: 570px;
    	    }
	    <?php endif; ?>
        </style>
        <!-- [/SEKCJA] -->

    </head>
    <body onLoad="resizeContent()" onResize="resizeContent()">
	<div id="container">
	    <div id="container1">
		<div id="pnlLeft">
		    <div class="app_info">
			<a href="<?php echo URL::site('default/index'); ?>">
			    <img src="<?php echo URL::base(); ?>lib/icons/home.png" alt=""/></a>
			Plan Lekcji
			<?php echo View::factory()->render('_snippet_theme'); ?>
		    </div>
		    <?php if (preg_match('#dev#', $appver)): ?>
    		    <div class="a_error" style="width: 100%; font-size: x-small;">
    			&nbsp;Używasz wersji rozwojowej systemu
    		    </div>
		    <?php endif; ?>
		    <div id="sidebar_menu" style="padding-left: 10px;">
			<?php echo View::factory()->render('_sidebar_menu'); ?>
		    </div>
		</div>
		<div id="pnlCenter">
		    <?php echo $content; ?>
		</div>

		<?php if ($_SESSION['token'] != null): ?>
    		<div id="pnlRight">
			<?php echo View::factory()->render('_sidebar_right'); ?>
    		</div>
    	    </div>
	    <?php endif; ?>
	    <div class="divbrk"/>
	    <div id="footer">
		<?php echo View::factory()->render('_panel_bottom'); ?>
	    </div>
	</div>
	<!-- [SEKCJA]: JAVASCRIPT -->
	<script type="text/javascript">
	    function resizeContent() { 
		var contentDiv = document.getElementById('container');
		
		// This may need to be done differently on IE than FF, but you get the idea. 
		var viewPortHeight = window.innerHeight; 
		contentDiv.style.height =  
		    Math.max(viewPortHeight, contentDiv.clientHeight) + 'px';
	    }
	</script>
	<!-- [/SEKCJA] -->
    </body>
</html>
