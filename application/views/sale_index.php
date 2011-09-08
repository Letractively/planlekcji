<?php
/*
 * Zarządzanie salami
 * 
 * 
 */
$isf = new Kohana_Isf();
$isf->DbConnect();
?>
<?php switch ($_err): ?>
<?php case 'e1': ?>
<p class="error">Wybrana sala już istnieje</p>
<?php break; ?>
<?php case 'e2': ?>
<p class="error">Ciąg zawiera niedozwolone znaki</p>
<?php break; ?>
<?php case 'e3': ?>
<p class="error">Ciąg nie może być pusty</p>
<?php break; ?>
<?php case 'pass': ?>
<p class="notice">Sala została utworzona</p>
<?php break; ?>
<?php case 'usun': ?>
<p class="notice">Sala została usunięta</p>
<?php break; ?>
<?php endswitch; ?>

<?php if (count($res) == 0): ?>
    <p class="info">Brak zdefiniowanych sal lekcyjnych. Dodaj nową salę.</p>
<?php else: ?>
    <table style="width: 100%;">
        <thead>
            <tr style="background: tan;">
                <td colspan="3" style="text-align: center;">
                    <form action="<?php echo URL::site('sale/dodaj'); ?>" method="post" name="form1">
                        Sala: <input type="text" name="inpSala"/>&nbsp;
                        <button type="submit" name="btnSubmit">Dodaj salę</button>
                    </form>
                </td>
            </tr>
            <tr style="height: 30px; font-weight: bold; background: darkgrey;">
                <td style="width: 100px;">Numer sali</td>
                <td>Przedmioty</td>
                <td style="width: 200px;"></td>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($res as $rowid => $rowcol): ?>
                <tr>
                    <td><?php echo $rowcol['sala']; ?></td>
                    <td>
                        <?php foreach ($isf->DbSelect('przedmiot_sale', array('przedmiot'), 'where sala=\'' . $rowcol['sala'] . '\'')
                        as $rid => $rcl): ?>
                            <?php echo $rcl['przedmiot']; ?>,&nbsp;
                        <?php endforeach; ?>
                    </td>
                    <td>
                        <a class="anac" href="<?php echo URL::site('sale/przedmiot/' . $rowcol['sala']); ?>">przedmioty</a>&emsp;
                        <a class="anac" href="<?php echo URL::site('sale/usun/' . $rowcol['sala']); ?>">usuń salę</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
