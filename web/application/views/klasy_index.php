<?php
/*
 * Główna strona klas
 * 
 * 
 */
?>
<?php /** kody bledow */ ?>
<?php switch ($_err): ?>
<?php case 'e1': ?>
<p class="error">Klasa już istnieje</p>
<?php break; ?>
<?php case 'e2': ?>
<p class="error">Ciąg zawiera niedozwolone znaki</p>
<?php break; ?>
<?php case 'e3': ?>
<p class="error">Ciąg nie może być pusty</p>
<?php break; ?>
<?php case 'pass': ?>
<p class="notice">Klasa została wpisana</p>
<?php break; ?>
<?php case 'usun': ?>
<p class="notice">Klasa została usunięta</p>
<?php break; ?>
<?php endswitch; ?>

<?php
$isf = new Kohana_Isf();
$isf->Connect(APP_DBSYS);
/** pobiera klasy w systemie */
$res = $isf->DbSelect('klasy', array('klasa'), 'order by klasa asc');
/** pobiera ilosc grup */
$grp = $isf->DbSelect('rejestr', array('*'), 'where opcja=\'ilosc_grup\'');
?>
<table style="width: 100%;">
    <tr valign="top">
        <td style="width: 50%;">
            <?php if (count($res) == 0): ?>
                <h3>Brak klas w systemie</h3>
            <?php else: ?>
                <table width="100%">
                    <thead style="text-align: center;">
                        <tr class="a_odd">
                            <td colspan="2">Zarządzanie klasami</td>
                        </tr>
                        <tr class="a_even">
                            <td width="20%">Klasa</td>
                            <td></td>
                        </tr>
                    </thead>
                    <?php $i=0; ?>
                    <?php foreach ($res as $rowid => $rowcol): ?>
                        <?php $i++; ?>
                        <?php if ($i % 2 == 0): ?>
                            <?php $class = " class='a_even'"; ?>
                        <?php else: ?>
                            <?php $class = ""; ?>
                        <?php endif; ?>
                        <tr <?php echo $class; ?>>
                            <td><?php echo $rowcol['klasa']; ?></td>
                            <td>
                                &bull; <a href="<?php echo URL::site('klasy/usun/' . $rowcol['klasa']); ?>">usuń</a>
                            </td>
                        </tr>

                    <?php endforeach; ?>
                </table>
                <p class="info">
                    Usunięcie klasy usunie wszystkie powiązania z nauczycielami uczących daną klasę.
                </p>
            <?php endif; ?>
        </td>
        <td style="width: 50%;">
            <h3>Dodaj klasę</h3>
            <form action="<?php echo URL::site('klasy/dodaj'); ?>" method="post" name="form1">
                <input type="text" name="inpKlasa"/>&nbsp;
                <button type="submit" name="btnSubmit">Dodaj klasę</button>
            </form>
            <h3>Grupy klasowe</h3>
            <p><b>Bieżąca ilość grup: </b><?php echo $grp[0]['wartosc']; ?></p>
            <form action="<?php echo url::site('klasy/grupyklasowe'); ?>" method="post" name="form">
                <select name="grp">
                    <?php for ($i = 0; $i <= 10; $i++): ?>
                        <?php if ($i == 1): ?>
                        <?php else: ?>
                            <?php if ($i == $grp[0]['wartosc']): ?>
                                <option selected><?php echo $i; ?></option>
                            <?php else: ?>
                                <option><?php echo $i; ?></option>
                            <?php endif; ?>
                        <?php endif; ?>
                    <?php endfor; ?>
                </select>
                <button type="submit" name="btnSubmit">Ustaw ilość grup</button>
            </form>
        </td>
    </tr>
</table>
