<?php
/*
 * Zarządzanie nauczycielami
 * 
 * 
 */
$isf = new Kohana_Isf();
$isf->DbConnect();
?>
<h1>Zarządzanie nauczycielami</h1>
<form action="<?php echo URL::site('nauczyciele/dodaj'); ?>" method="post" name="form1">
    Imię i nazwisko: <input type="text" name="inpName"/>&nbsp;
    <button type="submit" name="btnSubmit">Dodaj nauczyciela</button>
</form>

<?php switch ($_err): ?>
<?php case 'e1': ?>
<p class="error">Nauczyciel już istnieje</p>
<?php break; ?>
<?php case 'e2': ?>
<p class="error">Ciąg zawiera niedozwolone znaki</p>
<?php break; ?>
<?php case 'e3': ?>
<p class="error">Ciąg nie może być pusty</p>
<?php break; ?>
<?php case 'pass': ?>
<p class="notice">Nauczyciel został wpisany</p>
<?php break; ?>
<?php case 'usun': ?>
<p class="notice">Nauczyciel został usunięty</p>
<?php break; ?>
<?php endswitch; ?>

<?php
/*
 * Pobiera wszystkich nauczycieli
 */
$res = $isf->DbSelect('nauczyciele', array('*'), 'order by imie_naz asc');
?>
<?php if (count($res) == 0): ?>
    <p class="info">Brak nauczycieli w systemie.</p>
<?php else: ?>
    <table class="przed">
        <thead>
            <tr style="height: 30px; font-weight: bold;">
                <td>Imię i nazwisko</td>
                <td>Przedmioty</td>
                <td>Klasy</td>
                <td></td>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($res as $rowid => $rowcol): ?>
                <tr>
                    <td>
                        (<?php echo $rowcol['skrot']; ?>)
                        <a href="<?php echo URL::site('nauczyciele/zarzadzanie/' . $rowcol['imie_naz']); ?>">
                            <?php echo $rowcol['imie_naz']; ?></a>
                    </td>
                    <td>
                        <?php foreach ($isf->DbSelect('nl_przedm', array('przedmiot'), 'where nauczyciel=\'' . $rowcol['imie_naz'] . '\'')
                        as $rid => $rcl): ?>
                            <?php echo $rcl['przedmiot']; ?>, 
                        <?php endforeach; ?>
                    </td>
                    <td
                        style="max-width: 250px; width: 100px;">
                            <?php foreach ($isf->DbSelect('nl_klasy', array('klasa'), 'where nauczyciel=\'' . $rowcol['imie_naz'] . '\' order by klasa asc')
                            as $rid => $rcl): ?>
                            <?php echo $rcl['klasa']; ?>, 
                        <?php endforeach; ?>
                    </td>
                    <td>
                        <a class="anac"
                           href="<?php echo URL::site('nauczyciele/zarzadzanie/' . $rowcol['imie_naz']); ?>">
                            zarządzanie</a>&emsp;
                        <a class="anac"
                           href="<?php echo URL::site('nauczyciele/usun/' . $rowcol['skrot']); ?>">usuń nauczyciela</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
