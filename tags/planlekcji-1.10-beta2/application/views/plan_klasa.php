<?php
/**
 * Strona ze skryptem AJAX, ktory pobiera wlasciwa
 * strone edycji planu (klasaajax)
 */
$k = $klasa;
$isf = new Kohana_Isf();
$isf->DbConnect();
$reg = $isf->DbSelect('rejestr', array('*'), 'where opcja=\'edycja_danych\'');
$ns = $isf->DbSelect('rejestr', array('*'), 'where opcja=\'nazwa_szkoly\'');

$isf->JQUi();
$isf->JQUi_AjaxdivDoAjax('progress', URL::site('plan/klasaajax/' . $klasa), true);
if ($isf->detect_ie()):
    Kohana_Request::factory()->redirect('plan/klasaajax/' . $klasa . '/true');
endif;
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <link rel="stylesheet" type="text/css" href="<?php echo URL::base() ?>lib/css/themes/<?php echo $_SESSION['app_theme']; ?>.css"/>
        <title>Plan lekcji - <?php echo $ns[1]['wartosc']; ?></title>
        <link rel="stylesheet" type="text/css" href="<?php echo URL::base() ?>lib/css/style.css"/>
    </head>
    <body>
        <h1>
            <a href="#" onClick="confirmation();">
                <img src="<?php echo URL::base() ?>lib/images/save.png" alt="zapisz"/></a>
            Edycja planu dla <?php echo $klasa; ?>
        </h1>
        <?php
        $alternative = '<b>Przeglądarka nie obsługuje JavaScript?
                Spróbuj <a href="' . URL::site('plan/klasaajax/' . $klasa . '/true') . '">metodę alternatywną</a></b>';
        $customload = ' Trwa przypisywanie sal, przedmiotów i nauczycieli...';
        echo $isf->JQUi_AjaxdivCreate('progress', true, false, $alternative, $customload);
        ?>
        <?php echo $isf->JQUi_MakeScript(); ?>
    </body>
</html>