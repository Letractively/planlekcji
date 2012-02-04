<?php
/**
 * Strona ze skryptem AJAX, ktory pobiera wlasciwa
 * strone edycji planu (klasaajax)
 */
$k = $klasa;
$isf = new Kohana_Isf();
$isf->Connect(APP_DBSYS);
$reg = $isf->DbSelect('rejestr', array('*'), 'where opcja=\'edycja_danych\'');
$ns = $isf->DbSelect('rejestr', array('*'), 'where opcja=\'nazwa_szkoly\'');

$isf->JQUi();
$isf->JQUi_AjaxdivDoAjax('progress', URL::site('plan/grupaajax/' . $klasa), true);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>Plan lekcji - <?php echo $ns[0]['wartosc']; ?></title>
        <link rel="stylesheet" type="text/css" href="<?php echo URL::base() ?>lib/css/themes/<?php echo $_SESSION['app_theme']; ?>.css"/>
        <link rel="stylesheet" type="text/css" href="<?php echo URL::base() ?>lib/css/style.css"/>
	<style>
	    body{
		margin: 10px;
	    }
	</style>
    </head>
    <body>
	<h1>
	    <a href="<?php echo URL::site('default/index'); ?>">
                <img src="<?php echo URL::base() ?>lib/icons/back.png" alt="powrót"/></a>&emsp;
            Edycja planu dla <?php echo $klasa; ?> (grupowy)&emsp;
	    <a href="#" onClick="document.forms['formPlan'].submit();">
		<img src="<?php echo URL::base() ?>lib/icons/save.png" alt="zapisz"/></a>
        </h1>
	<?php
	$alternative = '<b>Przeglądarka nie obsługuje JavaScript?
                Spróbuj <a href="' . URL::site('plan/grupaajax/' . $klasa . '/true') . '">metodę alternatywną</a></b>';
	$customload = 'Trwa przypisywanie sal, przedmiotów i nauczycieli...';
	echo $isf->JQUi_AjaxdivCreate('progress', true, false, $alternative, $customload);
	?>
	<?php echo $isf->JQUi_MakeScript(); ?>
    </body>
</html>