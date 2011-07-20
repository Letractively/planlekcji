<?php
/*
 * Główna strona klas
 * 
 * 
 */
?>
<h1>Zarządzanie klasami</h1>
<form action="<?php echo URL::site('klasy/dodaj'); ?>" method="post" name="form1">
    Klasa: <input type="text" name="inpKlasa"/>&nbsp;
    <button type="submit" name="btnSubmit">Dodaj klasę</button>
</form>
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
$isf->DbConnect();
/** pobiera klasy w systemie */
$res = $isf->DbSelect('klasy', array('klasa'), 'order by klasa asc');
/** pobiera ilosc grup */
$grp = $isf->DbSelect('rejestr', array('*'), 'where opcja=\'ilosc_grup\'');
?>
<?php if (count($res) == 0): ?>
    <p class="info">Brak klas w systemie</p>
<?php else: ?>
    <p><b>Klasy w systemie</b></p>
    <table class="przed">
        <thead>
            <tr>
                <td>Klasa</td>
                <td></td>
            </tr>
        </thead>
        <?php foreach ($res as $rowid => $rowcol): ?>
            <tr>
                <td><?php echo $rowcol['klasa']; ?></td>
                <td><a class="anac"
                        href="<?php echo URL::site('klasy/usun/' . $rowcol['klasa']); ?>">usuń</a></td>
            </tr>

        <?php endforeach; ?>
    </table>
    <p><b>Usunięcie klasy usunie wszystkie powiązania z nauczycielami uczących daną klasę.</b>
    </p>
<?php endif; ?>
<h3>Grupy klasowe</h3>
<p><b>Bieżąca ilość grup: </b><?php echo $grp[1]['wartosc']; ?></p>
<form action="<?php echo url::site('klasy/grupyklasowe'); ?>" method="post" name="form">
    <select name="grp">
        <?php for ($i = 0; $i <= 10; $i++): ?>
            <?php if ($i == 1): ?>
            <?php else: ?>
                <?php if ($i == $grp[1]['wartosc']): ?>
                    <option selected><?php echo $i; ?></option>
                <?php else: ?>
                    <option><?php echo $i; ?></option>
                <?php endif; ?>
            <?php endif; ?>
        <?php endfor; ?>
    </select>
    <button type="submit" name="btnSubmit">Ustaw ilość grup</button>
</form>
