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

if ($isf->detect_ie()):
    Kohana_Request::factory()->redirect('plan/grupaajax/' . $klasa . '/true');
endif;
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>Plan lekcji - <?php echo $ns[1]['wartosc']; ?></title>
        <link rel="stylesheet" type="text/css" href="<?php echo URL::base() ?>lib/css/themes/<?php echo $_SESSION['app_theme']; ?>.css"/>
        <link rel="stylesheet" type="text/css" href="<?php echo URL::base() ?>lib/css/style.css"/>
        <?php if ($isf->detect_ie()): ?>
            <meta http-equiv="Refresh" content="0;URL=<?php echo URL::site('plan/klasaajax/' . $klasa . '/true'); ?>" />
        <?php endif; ?>
    </head>
    <body>
        <h1>
            <a href="#" onClick="document.forms['formPlan'].submit();">
                <img src="<?php echo URL::base() ?>lib/images/save.png" alt="zapisz"/></a>
            Edycja planu dla <?php echo $klasa; ?> (grupowy)
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