<?php
$k = $klasa;
$isf = new Kohana_Isf();
$isf->DbConnect();
$reg = $isf->DbSelect('rejestr', array('*'), 'where opcja=\'edycja_danych\'');
$ns = $isf->DbSelect('rejestr', array('*'), 'where opcja=\'nazwa_szkoly\'');

$isf->JQUi();
$isf->JQUi_AjaxdivDoAjax('progress', URL::site('plan/grupaajax/' . $klasa), true);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>Plan lekcji - <?php echo $ns[1]['wartosc']; ?></title>
        <link rel="stylesheet" type="text/css" href="<?php echo URL::base() ?>lib/css/style.css"/>
    </head>
    <body>
        <div style="position: fixed; top: 10px; width: 100%; height: 80px; background: white;">
            <h1>
                <a href="#" onClick="document.forms['formPlan'].submit();">
                    <img src="<?php echo URL::base() ?>lib/images/save.png" alt="zapisz"/></a>
                Edycja planu dla <?php echo $klasa; ?> (grupowy)
            </h1>
        </div>
        <div style="margin-top: 100px">
            <?php echo $isf->JQUi_AjaxdivCreate('progress', true, false, '<b>Przeglądarka nie obsługuje JavaScript? Spróbuj <a href="' . URL::site('plan/grupaajax/' . $klasa . '/true') . '">metodę alternatywną</a></b>'); ?>
        </div>
        <?php echo $isf->JQUi_MakeScript(); ?>
    </body>
</html>