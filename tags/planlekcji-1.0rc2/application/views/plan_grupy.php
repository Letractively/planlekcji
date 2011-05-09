<?php
/*
 * Zajęcia w grupie
 * 
 * 
 */
$isf = new Kohana_Isf();
$isf->DbConnect();
$r = $isf->DbSelect('rejestr', array('*'), 'where opcja="ilosc_grup"');
?>
<?php if ($r[1]['wartosc'] < 1): ?>
    <p class="info">Nie ustawiono grup w trybie edycji</p>
<?php else: ?>
    <h1>Plan lekcji dla grup</h1>
    <p>Należy pamiętać, że można wybrać tylko tych nauczycieli, którzy nie prowadzą
        normalnych zajęć. Można wielokrotnie wybrać tych nauczycieli, prowadzących zajęcia w grupie.</p>
    <h3>Zdefiniowane lekcje dla grup</h3>
    <?php $res = $isf->DbSelect('plan_grupy', array('*'), ' order by klasa, dzien, lekcja asc'); ?>
    <table>
        <thead>
            <tr>
                <td>Klasa</td>
                <td>Dzień</td>
                <td>Lekcja</td>
                <td>Grupa - przedmiot - nauczyciel - sala</td>
                <td></td>
            </tr>
        </thead>
        <tbody>
            <?php if (count($res) == 0): ?>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td><p class="info">Brak zajęć w grupach</p></td>
                </tr>
            <?php endif; ?>
            <?php foreach ($res as $rowid => $rowcol): ?>
                <tr>
                    <td><?php echo $rowcol['klasa']; ?></td>
                    <td><?php echo $rowcol['dzien']; ?></td>
                    <td><?php echo $rowcol['lekcja']; ?></td>
                    <td><b>gr <?php echo $rowcol['grupa']; ?></b> - <?php echo $rowcol['przedmiot']; ?> - <?php echo $rowcol['nauczyciel']; ?> - <?php echo $rowcol['sala']; ?></td>
                    <td><a href="<?php
        echo URL::site('plan/grpdel/' .
                $rowcol['dzien'] . '/' . $rowcol['lekcja'] . '/' .
                $rowcol['klasa'] . '/' . $rowcol['grupa']);
                ?>">[ usuń lekcję ]</a></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <h3>Wypełnij plan dla grupy</h3>
    <form action="<?php echo URL::site('plan/grpedit'); ?>" method="post" name="form">
        <p><b>Klasa:</b>
            <select name="sKlasa">
                <?php foreach ($isf->DbSelect('klasy', array('*'), 'order by klasa asc') as $r => $rc): ?>
                    <option><?php echo $rc['klasa']; ?></option>
                <?php endforeach; ?>
            </select> 
        </p>
        <p><b>Dzień:</b>
            <select name="sDzien">
                <option selected>Poniedziałek</option>
                <option>Wtorek</option>
                <option>Środa</option>
                <option>Czwartek</option>
                <option>Piątek</option>
            </select>
        </p>
        <p><b>Lekcja:</b>
            <select name="sLekcja">
                <?php foreach ($isf->DbSelect('lek_godziny', array('*')) as $r => $rc): ?>
                    <option><?php echo $rc['lekcja']; ?></option>
                <?php endforeach; ?>
            </select> <a href="#" onClick="document.forms['form'].submit();">[ wypełnij plan ]</a>
        </p>
    </form>
<?php endif; ?>