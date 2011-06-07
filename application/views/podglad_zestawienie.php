<?php
$GLOBALS['ilosc_klas'] = '';

function pobierz_naglowki() {

    $isf = new Kohana_Isf();
    $isf->DbConnect();
    $klasy = $isf->DbSelect('klasy', array('*'));
    $GLOBALS['ilosc_klas'] = count($klasy);
    
    echo '<tr><td colspan=2></td><td colspan='.$GLOBALS['ilosc_klas'].'>Klasy</td>';
    echo '<tr><td></td><td>Godziny</td>';
    
    foreach ($klasy as $rowid => $rowcol) {
        echo '<td width="150">' . $rowcol['klasa'] . '</td>';
    }
    
    echo '</tr>';
}

function pobierz_klasy($dzien, $lekcja) {
    $isf = new Kohana_Isf();
    $isf->DbConnect();

    $godziny = $isf->DbSelect('lek_godziny', array('*'), 'where lekcja=\'' . $lekcja . '\'');
    echo '<tr><td><b>' . $lekcja . '</b></td><td><i>' . $godziny[1]['godzina'] . '</i></td>';
    $klasy = $isf->DbSelect('klasy', array('*'));
    foreach ($klasy as $rowid => $rowcol) {
        echo '<td>';
        $lek = $isf->DbSelect('planlek', array('*'),
                'where dzien==\''.$dzien.'\' and klasa=\''.$rowcol['klasa'].'\'
                    and lekcja=\''.$lekcja.'\'');
        if(count($lek)!=0){
            if(isset($lek[1]['sala'])&&isset($lek[1]['skrot'])){
                echo '' . $lek[1]['przedmiot'] . ' (<a href="' . URL::site('podglad/sala/' . $lek[1]['sala']) . '">' . $lek[1]['sala'] . '</a>) (<a href="' . URL::site('podglad/nauczyciel/' . $lek[1]['skrot']) . '">' . $lek[1]['skrot'] . '</a>)';
            }else{
                echo ''.$lek[1]['przedmiot'].'';
            }
        }else{
            $lek = $isf->DbSelect('plan_grupy', array('*'),
                'where dzien=\''.$dzien.'\' and klasa=\''.$rowcol['klasa'].'\'
                    and lekcja=\''.$lekcja.'\' order by grupa asc');
            foreach($lek as $rowid=>$rowcol){
                if(isset($rowcol['sala'])&&isset($rowcol['skrot'])){
                    echo '<p class="grplek">gr '.$rowcol['grupa'].' -' . $lek[1]['przedmiot'] . ' (<a href="' . URL::site('podglad/sala/' . $lek[1]['sala']) . '">' . $lek[1]['sala'] . '</a>)
                        (<a href="' . URL::site('podglad/nauczyciel/' . $lek[1]['skrot']) . '">' . $lek[1]['skrot'] . '</a>)</p>';
                }else{
                    echo '<p class="grplek">gr '.$rowcol['grupa'].'- '.$rowcol['przedmiot'].'</p>';
                }
                
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

    $colspan = $GLOBALS['ilosc_klas'] + 2;
    echo '<tr class="zestdzien"><td colspan="' . $colspan . '">' . $dzien . '</td></tr>';

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
    </head>
    <body>
        <h1><a href="#" onClick="window.print();"><img border="0" src="<?php echo URL::base() ?>lib/images/printer.png" alt="[drukuj plan]"/></a>
            Plan lekcji ogólny</h1>
        <table class="przed">
            <thead style="background: #ccffcc; text-align: center">
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
