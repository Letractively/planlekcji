<?php
$GLOBALS['ilosc_klas'] = '';

function pobierz_naglowki() {

    $isf = new Kohana_Isf();
    $isf->DbConnect();
    $klasy = $isf->DbSelect('klasy', array('*'));
    $nl = $isf->DbSelect('nauczyciele', array('*'), 'order by imie_naz asc');
    $GLOBALS['ilosc_nl'] = count($nl);
    $GLOBALS['ilosc_klas'] = count($klasy);

    echo '<tr><td colspan=2></td><td colspan=' . $GLOBALS['ilosc_klas'] . '>Klasy</td>';
    echo '<td colspan=' . $GLOBALS['ilosc_nl'] . '>Nauczyciele</td>';
    echo '<tr><td></td><td>Godziny</td>';

    foreach ($klasy as $rowid => $rowcol) {
        echo '<td width=\'150\' style=\'min-width:150px;\'>' . $rowcol['klasa'] . '</td>';
    }
    foreach ($nl as $rowid => $rowcol) {
        echo '<td width=\'150\' style=\'min-width:50px;max-width:50px;\'>' . $rowcol['skrot'] . '</td>';
    }

    echo '</tr>';
}

function pobierz_klasy($dzien, $lekcja) {
    $isf = new Kohana_Isf();
    $isf->DbConnect();

    $apg = new App_Globals();
    $ilgr = $apg->getRegistryKey('ilosc_grup');

    $godziny = $isf->DbSelect('lek_godziny', array('*'), 'where lekcja=\'' . $lekcja . '\'');
    echo '<tr><td><b>' . $lekcja . '</b></td><td><i>' . $godziny[1]['godzina'] . '</i></td>';
    $klasy = $isf->DbSelect('klasy', array('*'));
    foreach ($klasy as $rowid => $rowcol) {
        echo '<td>';
        $lek = $isf->DbSelect('planlek', array('*'), 'where dzien=\'' . $dzien . '\' and klasa=\'' . $rowcol['klasa'] . '\'
                    and lekcja=\'' . $lekcja . '\'');
        if (count($lek) != 0) {
            if (isset($lek[1]['sala']) && isset($lek[1]['skrot'])) {
                echo '<b>' . $lek[1]['przedmiot'] . '</b>
                    <span class="grptxt">
                    <a href=\'' . URL::site('podglad/sala/' . $lek[1]['sala']) . '\'>' . $lek[1]['sala'] . '</a> <a href=\'' . URL::site('podglad/nauczyciel/' . $lek[1]['skrot']) . '\'>' . $lek[1]['skrot'] . '</a>
                        </span>';
            } else {
                echo '<b>' . $lek[1]['przedmiot'] . '</b>';
            }
        } else {
            $lek = $isf->DbSelect('plan_grupy', array('*'), 'where dzien=\'' . $dzien . '\' and klasa=\'' . $rowcol['klasa'] . '\'
                    and lekcja=\'' . $lekcja . '\' order by grupa asc');
            foreach ($lek as $rowid => $rowcol) {
                if (isset($rowcol['sala']) && isset($rowcol['skrot'])) {
                    echo '<p class=\'grplek\'><b>' . $rowcol['przedmiot'] . '</b>
                        <span class="grptxt">
                        ' . $rowcol['grupa'] . '/' . $ilgr . '
                        <a href=\'' . URL::site('podglad/sala/' . $lek[1]['sala']) . '\'>' . $rowcol['sala'] . '</a>
                        <a href=\'' . URL::site('podglad/nauczyciel/' . $lek[1]['skrot']) . '\'>' . $rowcol['skrot'] . '</a>
                        </span></p>';
                } else {
                    echo '<p class=\'grplek\'><b>' . $rowcol['przedmiot'] . '</b> <span class="grptxt">' . $rowcol['grupa'] . '/'.$ilgr.'</span></p>';
                }
            }
        }
        echo '</td>';
    }
    $nl = $isf->DbSelect('nauczyciele', array('*'), 'order by imie_naz asc');
    foreach ($nl as $rowid => $rowcol) {
        echo '<td>';
        $lek = $isf->DbSelect('planlek', array('*'), 'where dzien=\'' . $dzien . '\' and nauczyciel=\'' . $rowcol['imie_naz'] . '\'
                    and lekcja=\'' . $lekcja . '\'');
        if (count($lek) == 1) {
            echo '<p class=\'grplek\'><b>' . $lek[1]['klasa'] . '</b>
                <span class="grptxt">
                <a href=\'' . URL::site('podglad/sala/' . $lek[1]['sala']) . '\'>' . $lek[1]['sala'] . '</a>
                </span></p>';
        } else {
            $lek = $isf->DbSelect('plan_grupy', array('*'), 'where dzien=\'' . $dzien . '\' and nauczyciel=\'' . $rowcol['imie_naz'] . '\'
                    and lekcja=\'' . $lekcja . '\'');
            if (count($lek) > 0) {
                echo '<p class=\'grplek\'><b>';
                $stg = '';
                foreach ($lek as $rowid => $rowcol) {
                    $stg .= '' . $rowcol['klasa'] . '</b>-<span class="grptxt">' . $rowcol['grupa'] . '/' . $ilgr . ', ';
                }
                echo substr($stg, 0, -2);
                echo '</span></p><span class=\'grplek\'>
                    <a href=\'' . URL::site('podglad/sala/' . $rowcol['sala']) . '\'>' . $rowcol['sala'] . '</a>
                    </span>';
            } else {
                echo '---';
            }
        }
        echo '</td>';
    }
    echo '</tr>';
}

function pobierz_dzien($dzien) {
    $isf = new Kohana_Isf();
    $isf->DbConnect();

    $lekcje = $isf->DbSelect('lek_godziny', array('*'));

    $colspan = $GLOBALS['ilosc_klas'] + $GLOBALS['ilosc_nl'];
    echo '<tr class="a_even" style="font-weight: bold; text-align: center;">
         <td colspan=2></td><td colspan=\'' . $colspan . '\'>' . $dzien . '</td>';
    //echo '<td colspan='.$GLOBALS['ilosc_nl'].'></td>';
    echo '</tr>';

    foreach ($lekcje as $rowid => $rowcol) {
        pobierz_klasy($dzien, $rowid);
    }
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>Plan lekcji</title>
        <link rel="stylesheet" type="text/css" href="<?php echo URL::base() ?>lib/css/style.css"/>
        <link rel="stylesheet" type="text/css" href="<?php echo URL::base() ?>lib/css/themes/{{theme}}.css"/>
        <style>
            body{
                max-width: none;
                background: none;
            }
        </style>
    </head>
    <body>
        <table class="przed">
            <thead class="a_odd" style="text-align: center;">
                <?php pobierz_naglowki(); ?>
            </thead>
            <?php pobierz_dzien('Poniedziałek'); ?>
            <?php pobierz_dzien('Wtorek'); ?>
            <?php pobierz_dzien('Środa'); ?>
            <?php pobierz_dzien('Czwartek'); ?>
            <?php pobierz_dzien('Piątek'); ?>
        </table>
    </body>
</html>
