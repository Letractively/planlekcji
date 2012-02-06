<?php
$db = Isf2::Connect();
$res = $db->Select('przedmiot_sale')
		->Where(array('sala' => $sala))
		->OrderBy(array('przedmiot' => 'asc'))
		->Execute()->fetchAll();
$przedm = $db->Select('przedmioty')->OrderBy(array('przedmiot' => 'asc'))
		->Execute()->fetchAll();
$przedmioty = array();
if (count($przedm) != 0) {
    foreach ($przedm as $rowid => $rowcol) {
	$nonused = true;
	for ($i = 0; $i < count($res); $i++) {
	    if (in_array($rowcol['przedmiot'], $res[$i], true)) {
		$nonused = false;
	    }
	}
	if ($nonused) {
	    $przedmioty[] = $rowcol['przedmiot'];
	}
    }
}
?>
<h1><?php echo $sala; ?></h3>
<?php if (count($res) == 0): ?>
    <p class="info">Brak przedmiotów skojarzonych z tą salą</p>
<?php else: ?>
    <form action="<?php echo URL::site('sale/wypisz') ?>" method="post">
        <input type="hidden" name="sala" value="<?php echo $sala; ?>"/>
        <fieldset>
    	<legend>Przedmioty skojarzone z tą salą</legend>
    	<ul style="list-style: none;">
		<?php foreach ($res as $rowid => $rowcol): ?>
		    <li>
			<input type="radio"
			       name="rdPrzedmiot"
			       value="<?php echo $rowcol['przedmiot']; ?>"
			       class="inpNoneBorder"/>
			       <?php echo $rowcol['przedmiot']; ?>
		    </li>
		<?php endforeach; ?>
    	</ul>
    	<p style="text-align: right;">
	    <button type="submit" name="btnWypisz">Wypisz przedmiot</button>
	</p>
        </fieldset>
    </form>
<?php endif; ?>
<?php if (count($przedmioty) == 0): ?>
    <p class="info">Brak przedmiotów do przypisania.</p>
<?php else: ?>
    <form action="<?php echo URL::site('sale/dodaj') ?>" method="post">
        <input type="hidden" name="sala" value="<?php echo $sala; ?>"/>
	<fieldset>
        <legend>Wybierz przedmiot skojarzony z tą salą</legend>
        <input type="hidden" name="formSala" value="<?php echo $sala; ?>"/>
        <select name="selPrzed">
	    <?php foreach ($przedmioty as $pid => $pcol): ?>
		<option><?php echo $pcol; ?></option>
	    <?php endforeach; ?>
        </select>
        <p style="text-align: right;">
	    <button type="submit" name="btnSubmit">Dodaj przedmiot</button>
	</p>
	</fieldset>
    </form>
<?php endif; ?>
    <p>
	<a href="<?php echo URL::site('sale/index'); ?>">Powrót</a>
    </p>

