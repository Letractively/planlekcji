<?php $isf = new Kohana_Isf(); ?>
<?php $isf->DbConnect(); ?>
<?php
$k = $klasa;
$GLOBALS['k'] = $klasa;

function pobierzdzien($dzien, $lekcja, $grupa) {
    global $k;
    $isf = new Kohana_Isf();
    $isf->DbConnect();
    $a_table = 'nl_klasy, nl_przedm, przedmiot_sale';
    $a_cols = array('nl_klasy.nauczyciel', 'nl_klasy.klasa', 'nl_przedm.przedmiot', 'przedmiot_sale.sala');
    $a_cond = "on nl_klasy.nauczyciel=nl_przedm.nauczyciel and przedmiot_sale.przedmiot=nl_przedm.przedmiot where nl_klasy.klasa='" . $k . "' order by nl_przedm.przedmiot asc";
    $a = $isf->DbSelect($a_table, $a_cols, $a_cond);
    $ret = '<select name="grupa[' . $grupa . ']" id="grupa' . $grupa . '"><option>---</option>';
    foreach ($a as $rowid => $rowcol) {
        $b_table = 'planlek';
        $b_cols = array('*');
        $b_cond = 'where dzien="' . $dzien . '" and lekcja="' . $lekcja . '" and ( nauczyciel="' . $rowcol['nauczyciel'] . '" or sala="' . $rowcol['sala'] . '")';
        if (count($isf->DbSelect($b_table, $b_cols, $b_cond)) == 0) {
            $c_table = 'plan_grupy';
            $c_cols = array('*');
            $c_p='(nauczyciel="'.$rowcol['nauczyciel'].'" and sala != "'.$rowcol['sala'].'")';
            $c_q='(nauczyciel="'.$rowcol['nauczyciel'].'" and przedmiot != "'.$rowcol['przedmiot'].'")';;
            $c_r='(nauczyciel!="'.$rowcol['nauczyciel'].'" and sala="'.$rowcol['sala'].'" and przedmiot!="'.$rowcol['przedmiot'].'")';;
            $c_cond = 'where dzien="' . $dzien . '" and lekcja="' . $lekcja . '" and ( ' . $c_p . ' or ' . $c_q . ' and ' . $c_r . ')';
            if (count($isf->DbSelect($c_table, $c_cols, $c_cond)) == 0) {
                $v = $rowcol['przedmiot'] . ':' . $rowcol['sala'] . ':' . $rowcol['nauczyciel'];
                $ret.='<option>' . $v . '</option>';
            }
        } else {
            
        }
    }
    $ret.='<option>---</option>';
    foreach ($isf->DbSelect('przedmioty', array('*'), 'order by przedmiot asc') as $rc => $ri) {
        $ret.='<option>' . $ri['przedmiot'] . '</option>';
    }
    $ret.='</select>';
    return $ret;
}

$r = $isf->DbSelect('rejestr', array('*'), 'where opcja="ilosc_grup"');
?>
<?php if ($r[1]['wartosc'] < 1): ?>
    <p class="info">Nie ustawiono grup w trybie edycji</p>
<?php else: ?>
    <h1>Plan lekcji dla grup</h1>
    <ul>
        <li>
            <b>Grupy:</b> <?php
    for ($i = 1; $i <= $r[1]['wartosc']; $i++) {
        echo $i . ', ';
    }
    ?>
        </li>
        <li>
            <b>Klasa:</b> <?php echo $klasa; ?>
        </li>
        <li>
            <b>Dzień: </b> <?php echo $dzien; ?>
        </li>
        <li>
            <b>Lekcja:</b> <?php echo $lekcja; ?>
        </li>
    </ul>
    <form action="<?php echo URL::site('plan/grpplan'); ?>" name="form" method="post">
        <input type="hidden" name="klasa" value="<?php echo $klasa; ?>"/>
        <input type="hidden" name="dzien" value="<?php echo $dzien; ?>"/>
        <input type="hidden" name="lekcja" value="<?php echo $lekcja; ?>"/>
        <?php for ($i = 1; $i <= $r[1]['wartosc']; $i++): ?>
            <h3>Grupa <?php echo $i; ?></h3>
            <?php echo pobierzdzien($dzien, $lekcja, $i); ?>
        <?php endfor; ?>
        <a href="#" onClick="wyslij('<?php echo $i - 1; ?>');">[ wypełnij plan ]</a>
    </form>
    <script type="text/javascript">
        function wyslij(n){
            document.forms['form'].submit();
        }
    </script>
<?php endif; ?>