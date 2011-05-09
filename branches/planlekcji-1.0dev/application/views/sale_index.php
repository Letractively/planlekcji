<?php
/*
 * Zarządzanie salami
 * 
 * 
 */
$isf = new Kohana_Isf();
$isf->DbConnect();
?>
<h1>Zarządzanie salami</h1>
<form action="<?php echo URL::site('sale/dodaj'); ?>" method="post">
    Sala: <input type="text" name="inpSala"/>&nbsp;
    <button type="submit" name="btnSubmit">Dodaj salę</button>
</form>

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
    <table>
        <thead>
            <tr style="height: 30px; font-weight: bold;">
                <td style="width: 100px;">Numer sali</td>
                <td>Przedmioty</td>
                <td></td>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($res as $rowid => $rowcol): ?>
                <tr>
                    <td><?php echo $rowcol['sala']; ?></td>
                    <td>
                        <?php foreach ($isf->DbSelect('przedmiot_sale', array('przedmiot'), 'where sala="' . $rowcol['sala'] . '"')
                        as $rid => $rcl): ?>
                            <?php echo $rcl['przedmiot']; ?>,&nbsp;
                        <?php endforeach; ?>
                    </td>
                    <td>
                        <a href="<?php echo URL::site('sale/przedmiot/' . $rowcol['sala']); ?>">[ przedmioty ]</a>&emsp;
                        <a href="<?php echo URL::site('sale/usun/' . $rowcol['sala']); ?>">[ usuń salę ]</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
