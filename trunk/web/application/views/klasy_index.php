<?php
/*
 * Główna strona klas
 * 
 * 
 */
?>
<?php
$isf = Isf2::Connect();
$res = $isf->Select('klasy', array('klasa'))
		->OrderBy(array('klasa' => 'asc'))
		->Execute()->fetchAll();
$grp = App_Globals::getRegistryKey('ilosc_grup');
?>
<div class="tableDiv" style="width: 100%;">
    <div class="tableRow">
	<div class="tableCell" style="width: 50%;">
	    <h3>Dodaj klasę</h3>
	    <form action="<?php echo URL::site('klasy/dodaj'); ?>" method="post" name="form1">
		<input type="text" name="inpKlasa"/>&nbsp;
		<button type="submit" name="btnSubmit">Dodaj klasę</button>
	    </form>
	</div>
	<div class="tableCell" style="width: 50%;">
	    <h3>Grupy klasowe (<?php echo $grp; ?>)</h3>
	    <form action="<?php echo url::site('klasy/grupyklasowe'); ?>" method="post" name="form">
		<select name="grp">
		    <?php for ($i = 0; $i <= 10; $i++): ?>
			<?php if ($i == 1): ?>
			<?php else: ?>
			    <?php if ($i == $grp): ?>
	    		    <option selected><?php echo $i; ?></option>
			    <?php else: ?>
	    		    <option><?php echo $i; ?></option>
			    <?php endif; ?>
			<?php endif; ?>
		    <?php endfor; ?>
		</select>
		<button type="submit" name="btnSubmit">Ustaw ilość grup</button>
	    </form>
	</div>
    </div>
</div>
<form action="<?php echo URL::site('klasy/usun'); ?>" method="post">
    <?php if (count($res) == 0): ?>
        <p class="error">Brak klas w systemie</p>
    <?php else: ?>
        <table width="100%">
    	<thead style="text-align: center;">
    	    <tr class="a_odd">
    		<td colspan="2">Zarządzanie klasami</td>
    	    </tr>
    	    <tr class="a_even">
    		<td>Klasa</td>
    	    </tr>
    	</thead>
	    <?php $i = 0; ?>
	    <?php foreach ($res as $rowid => $rowcol): ?>
		<tr <?php echo ($i % 2 == 1) ? 'class="a_even"' : ''; ?>>
		    <td style="text-align: center;">
			<?php echo $rowcol['klasa']; ?>&emsp;
			<button type="submit" name="btnClass[]"
				value="<?php echo $rowcol['klasa']; ?>">
			    Usuń
			</button>
		    </td>
		</tr>
		<?php $i++; ?>
	    <?php endforeach; ?>
        </table>
    <?php endif; ?>
</form>
<?php /** kody bledow */ ?>
<?php if ($_err == 'e1'): ?>
    <p class="error">Klasa już istnieje</p>
<?php endif; ?>
<?php if ($_err == 'e2'): ?>
    <p class="error">Ciąg zawiera niedozwolone znaki</p>
<?php endif; ?>
<?php if ($_err == 'e3'): ?>
    <p class="error">Ciąg nie może być pusty</p>
<?php endif; ?>
<?php if ($_err == 'pass'): ?>
    <p class="notice">Klasa została wpisana</p>
<?php endif; ?>
<?php if ($_err == 'usun'): ?>
    <p class="notice">Klasa została usunięta</p>
<?php endif; ?>
