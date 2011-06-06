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
$ilosc_grp = $isf->DbSelect('rejestr', array('*'), 'where opcja="ilosc_grup"');

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
    $options = '';
    foreach ($a as $rowid => $rowcol) {
        $b_table = 'planlek';
        $b_cols = array('*');
        $b_cond = 'where dzien="' . $dzien . '" and lekcja="' . $lekcja . '" and ( nauczyciel="' . $rowcol['nauczyciel'] . '" or sala="' . $rowcol['sala'] . '")';
        if (count($isf->DbSelect($b_table, $b_cols, $b_cond)) == 0) {
            $b_table = 'plan_grupy';
            $b_cols = array('*');
            $b_cond = 'where dzien="' . $dzien . '" and lekcja="' . $lekcja . '" and nauczyciel="' . $rowcol['nauczyciel'] . '" and sala!="' . $rowcol['sala'] . '"';
            if (count($isf->DbSelect($b_table, $b_cols, $b_cond)) == 0) {
                $v = $rowcol['przedmiot'] . ':' . $rowcol['sala'] . ':' . $rowcol['nauczyciel'];
                $options.='<option>' . $v . '</option>';
            }
        } else {
            
        }
    }
    if (count($lek) == 0) {
        $ilosc_grp = $isf->DbSelect('rejestr', array('*'), 'where opcja="ilosc_grup"');
        $g = $ilosc_grp[1]['wartosc'];
        $i = 0;
        while ($i < $g) {
            $i++;
            $ret .= '<p class="grplek">gr' . $i;
            $ret .= '<select style="width:200px;" name="' . $dzien . '[' . $lekcja . '][' . $i . ']">';
            $lg = $isf->DbSelect('plan_grupy', array('*'), 'where dzien="' . $dzien . '" and lekcja="' . $lekcja . '" and grupa="' . $i . '" and klasa="'.$k.'"');
            if (count($lg) != 0) {
                if(isset($lg[1]['sala'])&&isset($lg[1]['nauczyciel'])){
                    $vg = $lg[1]['przedmiot'] . ':' . $lg[1]['sala'] . ':' . $lg[1]['nauczyciel'];
                }else{
                    $vg = $lg[1]['przedmiot'];
                }
                $ret .= '<option selected>' . $vg . '</option>';
            }
            $ret .= '<option>---</option>';
            $ret .= $options;
            $ret.='<option>---</option>';
            foreach ($isf->DbSelect('przedmioty', array('*'), 'order by przedmiot asc') as $rc => $ri) {
                $ret.='<option>' . $ri['przedmiot'] . '</option>';
            }
            $ret.='</select></p>';
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

    return $ret;
}
?>
<?php if ($alternative != false): ?>
    <h3>Edycja planu grupowego dla klasy <?php echo $klasa; ?></h3>
<?php endif; ?>
<?php if ($ilosc_grp[1]['wartosc'] == 0): ?>
    <h3>Nie można dokonać edycji z powodu braku ustawionych grup</h3>
<?php else: ?>
    <form action="<?php echo URL::site('plan/grupazatw'); ?>" method="post" name="formPlan" style="margin-top: 100px;">
        <input type="hidden" name="klasa" value="<?php echo $klasa; ?>"/>
        <?php if ($alternative != false): ?>
            <button type="submit" name="btnSubmit">Zapisz zmiany</button>
        <?php endif; ?>
        <table class="przed">
            <thead style="background: #7cc1f0;">
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
<?php endif; ?>