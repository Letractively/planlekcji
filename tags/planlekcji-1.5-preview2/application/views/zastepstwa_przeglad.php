<?php
$isf = new Kohana_Isf();
$GLOBALS['zast_id'] = $zast_id;

function pobierznl($lekcja) {
    $isf = new Kohana_Isf();
    $isf->DbConnect();
    $id = $GLOBALS['zast_id'];
    
    
    $res = $isf->DbSelect('zastepstwa', array('*'), 'where zast_id=\'' . $id . '\' and lekcja=\'' . $lekcja . '\'');

    if (count($res) != 0) {

        if (empty($res[1]['sala']) || empty($res[1]['nauczyciel'])) {
            echo $res[1]['przedmiot'];
        } else {
            echo $res[1]['przedmiot'] . ' (' . $res[1]['sala'] . ') - ' . $res[1]['nauczyciel'];
        }
    }
}

function pobierzdzien($dzien, $nauczyciel) {
    $isf = new Kohana_Isf();
    $isf->DbConnect();
    echo '<table class="przed"><thead style="background: #6699ff">
        <tr><td></td><td>Godzina</td><td>Lekcja</td></tr></thead>';
    foreach ($isf->DbSelect('lek_godziny', array('*')) as $rowid => $rowcol) {
        $lek_nr = $rowid;
        echo '<tr><td>' . $rowid . '</td><td>' . $rowcol['godzina'] . '</td><td>';
        $res = $isf->DbSelect('planlek', array('*'), 'where nauczyciel=\'' . $nauczyciel . '\'
            and dzien=\'' . $dzien . '\' and lekcja=\'' . $rowid . '\'');
        if (count($res) == 1) {
            echo '<p class="grplek">
                <b>' . $res[1]['klasa'] . '</b> - ';
            pobierznl($lek_nr);
            echo '</p></td></tr>';
        }
        if (count($res) == 0) {
            $res = $isf->DbSelect('plan_grupy', array('*'), 'where nauczyciel=\'' . $nauczyciel . '\'
            and dzien=\'' . $dzien . '\' and lekcja=\'' . $lek_nr . '\'');
            if (count($res != 0)) {
                foreach ($res as $rowid => $rowcol) {
                    echo '<p class="grplek">
                <b>' . $rowcol['klasa'] . ' gr ' . $rowcol['grupa'] . '</b> - ';
                    pobierznl($lek_nr);
                    echo '</p></td></tr>';
                }
            } else {
                echo '</td></tr>';
            }
        }
    }
    echo '</table>';
}
?>
<h1>
    Zastępstwo za <?php echo $nauczyciel; ?>
</h1>
<h3>W dniu <?php echo $data; ?> (<?php echo $dzien; ?>)</h3>
<p><b>Komentarz: </b><i><?php echo $komentarz; ?></i></p>
<?php pobierzdzien($dzien, $nauczyciel); ?>