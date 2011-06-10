<?php
/*
 * Edycja planu lekcji
 * 
 * 
 */
$isf = new Kohana_Isf();
$isf->DbConnect();
$ilosc_lek = $isf->DbSelect('rejestr', array('wartosc'), 'where opcja="ilosc_godzin_lek"');
$ilosc_lek = $ilosc_lek[1]['wartosc'];
$lek_godziny = $isf->DbSelect('lek_godziny', array('*'));
$k = $klasa;
$GLOBALS['k'] = $klasa;

function pobierzdzien($dzien, $lekcja) {
    global $k;
    $isf = new Kohana_Isf();
    $isf->DbConnect();
    $a_table = 'nl_klasy, nl_przedm, przedmiot_sale';
    $a_cols = array('nl_klasy.nauczyciel', 'nl_klasy.klasa', 'nl_przedm.przedmiot', 'przedmiot_sale.sala');
    $a_cond = "on nl_klasy.nauczyciel=nl_przedm.nauczyciel and przedmiot_sale.przedmiot=nl_przedm.przedmiot where nl_klasy.klasa='" . $k . "' order by nl_przedm.przedmiot asc";
    $a = $isf->DbSelect($a_table, $a_cols, $a_cond);
    $lek = $isf->DbSelect('planlek', array('*'), 'where lekcja="' . $lekcja . '" and dzien="' . $dzien . '" and klasa="' . $k . '"');
    $ret = '';
    $vl = '';
    if (count($lek) == 0) {
        $lekx = $isf->DbSelect('plan_grupy', array('*'), 'where lekcja="' . $lekcja . '" and dzien="' . $dzien . '" and klasa="' . $k . '"');
        if (count($lekx) >= 1) {
            $ret .= '<b>[ zajęcia w grupach ]</b><br/>';
        }
    } else {
        if ($vl != '---') {
            if ($lek[1]['sala'] == '' && $lek[1]['nauczyciel'] == '') {
                $ret .= '<b>' . $lek[1]['przedmiot'] . '</b><br/>';
                $vl = $lek[1]['przedmiot'];
            } else {
                $ret .= '<b>' . $lek[1]['przedmiot'] . '</b>(' . $lek[1]['sala'] . ')(' . $lek[1]['nauczyciel'] . ')<br/>';
                $vl = $lek[1]['przedmiot'] . ':' . $lek[1]['sala'] . ':' . $lek[1]['nauczyciel'];
            }
        }
    }
    $ret .= '<select style="width:200px;" name="' . $dzien . '[' . $lekcja . ']">';
    if ($vl != '') {
        $ret .= '<option selected>' . $vl . '</option>';
    }
    $ret .= '<option>---</option><optgroup label="Przedmiot - Sala - Nauczyciel">';
    foreach ($a as $rowid => $rowcol) {
        $b_table = 'planlek';
        $b_cols = array('*');
        $b_cond = 'where dzien="' . $dzien . '" and lekcja="' . $lekcja . '" and ( nauczyciel="' . $rowcol['nauczyciel'] . '" or sala="' . $rowcol['sala'] . '")';
        if (count($isf->DbSelect($b_table, $b_cols, $b_cond)) == 0) {
            $b_table = 'plan_grupy';
            $b_cols = array('*');
            $b_cond = 'where dzien="' . $dzien . '" and lekcja="' . $lekcja . '" and nauczyciel="' . $rowcol['nauczyciel'] . '"';
            if (count($isf->DbSelect($b_table, $b_cols, $b_cond)) == 0) {
                $v = $rowcol['przedmiot'] . ':' . $rowcol['sala'] . ':' . $rowcol['nauczyciel'];
                $ret.='<option>' . $v . '</option>';
            }
        } else {
            
        }
    }
    $ret.='</optgroup><optgroup label="Zwykły przedmiot">';
    foreach ($isf->DbSelect('przedmioty', array('*'), 'order by przedmiot asc') as $rc => $ri) {
        $ret.='<option>' . $ri['przedmiot'] . '</option>';
    }
    $ret.='</optgroup></select>';
    return $ret;
}
?>
<form action="<?php echo URL::site('plan/zatwierdz'); ?>" method="post" name="formPlan"
<?php if (!isset($alternative)): ?>
          style="margin-top: 100px;">
          <?php else: ?>
        >
    <?php endif; ?>
    <?php if ($alternative != false): ?>
        <link rel="stylesheet" type="text/css" href="<?php echo URL::base() ?>lib/css/style.css"/>
        <h1>Edycja planu dla klasy <?php echo $klasa; ?>
        &emsp;<button type="submit" name="btnSubmit">Zapisz zmiany</button></h1>
    <?php endif; ?>
    <input type="hidden" name="klasa" value="<?php echo $klasa; ?>"/>
    <table class="przed">
        <thead style="background: greenyellow;">
            <tr>
                <td></td>
                <td>Godziny</td>
                <td>Poniedziałek</td>
                <td>Wtorek</td>
                <td>Środa</td>
                <td>Czwartek</td>
                <td>Piątek</td>
            </tr>
        </thead>
        <tbody>
            <?php for ($i = 1; $i <= $ilosc_lek; $i++): ?>
                <tr>
                    <td><?php echo $i; ?></td>
                    <td><?php echo $lek_godziny[$i]['godzina']; ?></td>
                    <td>
                        <?php echo pobierzdzien('Poniedziałek', $i); ?>
                    </td>
                    <td>
                        <?php echo pobierzdzien('Wtorek', $i); ?>
                    </td>
                    <td>
                        <?php echo pobierzdzien('Środa', $i); ?>
                    </td>
                    <td>
                        <?php echo pobierzdzien('Czwartek', $i); ?>
                    </td>
                    <td>
                        <?php echo pobierzdzien('Piątek', $i); ?>
                    </td>
                </tr>
            <?php endfor; ?>
        </tbody>
    </table>
</form>