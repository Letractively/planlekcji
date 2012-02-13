<?php
/*
 * Plan zajęć dla sali
 * 
 * 
 */
$isf = new Kohana_Isf();
$isf->Connect(APP_DBSYS);
$ilosc_lek = $isf->DbSelect('rejestr', array('wartosc'), 'where opcja=\'ilosc_godzin_lek\'');
$ilosc_lek = $ilosc_lek[0]['wartosc'];
$lek_godziny = $isf->DbSelect('lek_godziny', array('*'));
$k = $klasa;
$GLOBALS['k'] = $klasa;

function pobierzdzien($dzien, $lekcja) {
    global $k;
    $isf = new Kohana_Isf();
    $isf->Connect(APP_DBSYS);
    $apg = new App_Globals();
    $ilosc_grp = $apg->getRegistryKey('ilosc_grup');
    $ret = '';
    $r = $isf->DbSelect('planlek', array('*'), 'where sala=\'' . $k . '\' and dzien=\'' . $dzien . '\' and lekcja=\'' . $lekcja . '\'');
    if (count($r) != 0) {
        echo '<b>'.$r[0]['przedmiot'] . '</b><span class="grptxt">
            <a href=\'' . URL::site('podglad/klasa/' . $r[0]['klasa']) . '\'>' . $r[0]['klasa'] . '</a>
            <a href=\'' . URL::site('podglad/nauczyciel/' . $r[0]['skrot']) . '\'>' . $r[0]['skrot'] . '</a>
            </span>';
    } else {
        $rn = $isf->DbSelect('plan_grupy', array('*'), 'where sala=\'' . $k . '\' and dzien=\'' . $dzien . '\' and lekcja=\'' . $lekcja . '\'');
        if (count($rn) == 0) {
            echo '';
        } else {
            foreach ($rn as $rowid => $rowcol) {
                echo '
                    <p class=\'grplek\'><b>' . $rowcol['przedmiot'] . '</b>
                    <span class="grptxt">
                    <a href=\'' . URL::site('podglad/klasa/' . $rowcol['klasa']) . '\'>' . $rowcol['klasa'] . '</a>-' . $rowcol['grupa'] . '/' . $ilosc_grp . '
                    <a href=\'' . URL::site('podglad/nauczyciel/' . $rowcol['skrot']) . '\'>' . $rowcol['skrot'] . '</a>
                    </span></p>';
            }
        }
    }
}
?>
<style>
    .grptxt{
        font-size: 8pt;
    }
</style>
<table class="przed" align="center" style="font-size: 9pt; width: auto;">
    <thead style="background: #ccccff;">
        <tr class="a_odd">
            <td colspan="7" style="text-align: center">
                <p>
                    <span class="pltxt"><?php echo $klasa; ?></span>
                </p>
            </td>
        </tr>
        <tr class="a_even">
            <td></td>
            <td>Godziny</td>
            <td style="width: 110px;">Poniedziałek</td>
            <td style="width: 110px;">Wtorek</td>
            <td style="width: 110px;">Środa</td>
            <td style="width: 110px;">Czwartek</td>
            <td style="width: 110px;">Piątek</td>
        </tr>
    </thead>
    <tbody>
        <?php for ($i = 1; $i <= $ilosc_lek; $i++): ?>
            <?php if ($i % 2 == 0): ?>
                <?php $cl = 'class="a_even"'; ?>
            <?php else: ?>
                <?php $cl = ''; ?>
            <?php endif; ?>
            <tr <?php echo $cl; ?>>
                <td><b><?php echo $i; ?></b></td>
                <td class="info"><?php echo $lek_godziny[$i-1]['godzina']; ?></td>
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