<?php
/*
 * Zarządzanie przedmiotami
 * 
 * 
 */
$isf = new Kohana_Isf();
$isf->Connect(APP_DBSYS);
?>
<?php if (count($res) == 0): ?>
    <table style="width: 100%;">
        <thead>
    	<tr>
    	    <td class="a_odd" style="text-align: center;">
    		Brak zdefiniowanych przedmiotów
    	    </td>
    	</tr>
    	<tr>
    	    <td class="a_even" style="text-align: center;">
    		<form action="<?php echo URL::site('przedmioty/dodaj'); ?>" method="post" name="form1">
    		    Przedmiot: <input type="text" name="inpPrzedmiot"/>&nbsp;
    		    <button type="submit" name="btnSubmit">Dodaj przedmiot</button>
    		</form>
    	    </td>
    	</tr>
        </thead>
    </table>
<?php else: ?>
    <table style="width: 100%;">
        <thead>
    	<tr>
    	    <td colspan="4" class="a_odd" style="text-align: center;">
    		<form action="<?php echo URL::site('przedmioty/dodaj'); ?>" method="post" name="form1">
    		    Przedmiot: <input type="text" name="inpPrzedmiot"/>&nbsp;
    		    <button type="submit" name="btnSubmit">Dodaj przedmiot</button>
    		</form>
    	    </td>
    	</tr>
    	<tr class="a_even">
    	    <td style="width: 100px;">Przedmiot</td>
    	    <td style="width: 150px; max-width: 200px;">Przypisane sale</td>
    	    <td>Nauczyciele uczący</td>
    	    <td style="width: 250px;"></td>
    	</tr>
        </thead>
        <tbody>
	    <?php $i = 0; ?>
	    <?php foreach ($res as $rowid => $rowcol): ?>
		<?php $i++; ?>
		<?php if ($i % 2 == 0): ?>
		    <?php $class = " class='a_even'"; ?>
		<?php else: ?>
		    <?php $class = ""; ?>
		<?php endif; ?>
		<tr valign="top" <?php echo $class; ?>>
		    <td>
			&bull;
			<a href="<?php echo URL::site('przedmioty/zarzadzanie/' . $rowcol['przedmiot']); ?>">
			    <?php echo $rowcol['przedmiot']; ?>
			</a>
		    </td>
		    <td> 
			<?php foreach ($isf->DbSelect('przedmiot_sale', array('sala'), 'where przedmiot=\'' . $rowcol['przedmiot'] . '\' order by sala asc')
			as $rid => $rcl): ?>
			    <?php echo $rcl['sala']; ?>&nbsp;
			<?php endforeach; ?>
		    </td>
		    <td> 
			<?php foreach ($isf->DbSelect('nl_przedm', array('nauczyciel'), 'where przedmiot=\'' . $rowcol['przedmiot'] . '\' order by nauczyciel asc')
			as $rid => $rcl): ?>
	    		&bull; <?php echo $rcl['nauczyciel']; ?><br/>
			<?php endforeach; ?>
		    </td>
		    <td>
			&bull; <a href="<?php echo URL::site('przedmioty/sale/' . $rowcol['przedmiot']); ?>">sale</a><br/>
			&bull; <a href="<?php echo URL::site('przedmioty/zarzadzanie/' . $rowcol['przedmiot']); ?>">zarządzanie</a><br/>
			&bull; <a href="<?php echo URL::site('przedmioty/usun/' . $rowcol['przedmiot']); ?>">usuń przedmiot</a>
		    </td>
		</tr>
	    <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
<?php switch ($_err): ?>
<?php case 'e1': ?>
<p class="error">Wybrany przedmiot już istnieje</p>
<?php break; ?>
<?php case 'e2': ?>
<p class="error">Ciąg zawiera niedozwolone znaki</p>
<?php break; ?>
<?php case 'e3': ?>
<p class="error">Ciąg nie może być pusty</p>
<?php break; ?>
<?php case 'pass': ?>
<p class="notice">Przedmiot został utworzony</p>
<?php break; ?>
<?php case 'usun': ?>
<p class="notice">Przedmiot został usunięty</p>
<?php break; ?>
<?php endswitch; ?>