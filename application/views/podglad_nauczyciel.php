<?php
/*
 * Plan Lekcji dla nauczyciela
 * 
 * 
 */
$isf = new Kohana_Isf();
$isf->DbConnect();
$ilosc_lek = $isf->DbSelect('rejestr', array('wartosc'), 'where opcja=\'ilosc_godzin_lek\'');
$ilosc_lek = $ilosc_lek[1]['wartosc'];
$lek_godziny = $isf->DbSelect('lek_godziny', array('*'));
$k = $klasa;
$GLOBALS['k'] = $klasa;
function pobierzdzien($dzien, $lekcja) {
    global $k;
    $isf = new Kohana_Isf();
    $isf->DbConnect();
    $ret='';
    $r = $isf->DbSelect('planlek', array('*'), 'where nauczyciel=\''.$k.'\' and dzien=\''.$dzien.'\' and lekcja=\''.$lekcja.'\'');
    if(count($r)!=0){
        echo $r[1]['przedmiot'].' <a href=\''.URL::site('podglad/sala/'.$r[1]['sala']).'\'>'.$r[1]['sala'].'</a> <a href=\''.URL::site('podglad/klasa/'.$r[1]['klasa']).'\'>'.$r[1]['klasa'].'</a>';
    }else{
        $rn = $isf->DbSelect('plan_grupy', array('*'), 'where nauczyciel=\'' . $k . '\' and dzien=\'' . $dzien . '\' and lekcja=\'' . $lekcja . '\'');
        if (count($rn) == 0) {
            echo '';
        } else {
            foreach ($rn as $rowid => $rowcol) {
                echo '<p class=\'grplek\'>'.$rowcol['przedmiot'].' <a href=\''.URL::site('podglad/sala/'.$rowcol['sala']).'\'>'.$rowcol['sala'].'</a> <a href=\''.URL::site('podglad/klasa/'.$rowcol['klasa']).'\'>'.$rowcol['klasa'].'</a>/'.$rowcol['grupa'].'</p>';
            }
        }
    }
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>Plan lekcji</title>
        <link rel="stylesheet" type="text/css" href="<?php echo URL::base() ?>lib/css/style.css"/>
    </head>
    <body>
        <h1><a href="#" onClick="window.print();"><img border="0" src="<?php echo URL::base() ?>lib/images/printer.png" alt="[drukuj plan]"/></a>
            Plan lekcji - nauczyciel <?php echo $klasa; ?> (<?php echo $skrot; ?>)</h1>
        <table class="przed">
            <thead style="background: coral;">
                <tr>
                    <td></td>
                    <td>Godziny</td>
                    <td style="min-width: 150px; max-width: 200px;">Poniedziałek</td>
                    <td style="min-width: 150px; max-width: 200px;">Wtorek</td>
                    <td style="min-width: 150px; max-width: 200px;">Środa</td>
                    <td style="min-width: 150px; max-width: 200px;">Czwartek</td>
                    <td style="min-width: 150px; max-width: 200px;">Piątek</td>
                </tr>
            </thead>
            <tbody>
                <?php for ($i = 1; $i <= $ilosc_lek; $i++): ?>
                    <tr>
                        <td><b><?php echo $i; ?></b></td>
                        <td class="info"><?php echo $lek_godziny[$i]['godzina']; ?></td>
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
    </body>
</html>