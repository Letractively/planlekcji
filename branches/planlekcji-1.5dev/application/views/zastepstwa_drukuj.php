<?php

function pobierznl($lekcja, $id) {
    $isf = new Kohana_Isf();
    $isf->DbConnect();


    $res = $isf->DbSelect('zastepstwa', array('*'), 'where zast_id=\'' . $id . '\' and lekcja=\'' . $lekcja . '\'');

    if (count($res) != 0) {

        if (empty($res[1]['sala']) || empty($res[1]['nauczyciel'])) {
            echo $res[1]['przedmiot'];
        } else {
            echo $res[1]['przedmiot'] . ' (' . $res[1]['sala'] . ') - ' . $res[1]['nauczyciel'];
        }
    }
}

function pobierzdzien($id) {
    $isf = new Kohana_Isf();
    $isf->DbConnect();
    $nl = $isf->DbSelect('zast_id', array('*'), 'where zast_id=\'' . $id . '\'');
    $nauczyciel = $nl[1]['za_nl'];

    $enpl_days = array(
        'Monday' => 'Poniedziałek',
        'Tuesday' => 'Wtorek',
        'Wednesday' => 'Środa',
        'Thursday' => 'Czwartek',
        'Friday' => 'Piątek',
        'Saturday' => 'Sobota',
        'Sunday' => 'Niedziela',
    );
    $day = date('l', strtotime($nl[1]['dzien']));
    $dzien = $enpl_days[$day];

    echo '<table class="przed" style="width: 400px"><thead style="background: #DDD;">
            <tr><td colspan=3><h1>Zastępstwo za ' . $nauczyciel . '</h1>
                <h3>' . $dzien . ' - ' . $nl[1]['dzien'] . '</h3></td></tr>
        <tr><td width=25></td><td>Lekcja</td></tr></thead>';
    foreach ($isf->DbSelect('lek_godziny', array('*')) as $rowid => $rowcol) {
        $lek_nr = $rowid;
        echo '<tr><td>' . $rowid . '</td><td>';
        $res = $isf->DbSelect('planlek', array('*'), 'where nauczyciel=\'' . $nauczyciel . '\'
            and dzien=\'' . $dzien . '\' and lekcja=\'' . $rowid . '\'');
        if (count($res) == 1) {
            echo '<p class="grplek">
                <b>' . $res[1]['klasa'] . '</b> - ';
            pobierznl($lek_nr, $id);
            echo '</p></td></tr>';
        }
        if (count($res) == 0) {
            $res = $isf->DbSelect('plan_grupy', array('*'), 'where nauczyciel=\'' . $nauczyciel . '\'
            and dzien=\'' . $dzien . '\' and lekcja=\'' . $lek_nr . '\'');
            if (count($res) > 0) {
                foreach ($res as $rowid => $rowcol) {
                    echo '<p class="grplek">
                <b>' . $rowcol['klasa'] . ' gr ' . $rowcol['grupa'] . '</b> - ';
                    pobierznl($lek_nr, $id);
                }
                echo '</p></td></tr>';
            } else {
                echo '---</td></tr>';
            }
        }
    }
    echo '</table>';
}

function pobierzzast($id) {
    pobierzdzien($id);
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>Drukowanie zastępstw</title>
        <link rel="stylesheet" type="text/css" href="<?php echo URL::base() ?>lib/css/style_print.css"/>
    </head>
    <body>
        <?php foreach ($_POST['print'] as $rid => $rcl): ?>
            <?php pobierzzast($rid); ?>
            <p/>
        <?php endforeach; ?>
    </body>
</html>
