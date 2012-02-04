<?php
$db = Isf2::Connect();
$res = $db->Select('przedmiot_sale')
		->Where(array('sala' => $sala))
		->OrderBy(array('przedmiot' => 'asc'))
		->Execute()->fetchAll();
$przedm = $db->Select('przedmioty')->OrderBy(array('przedmiot' => 'asc'))
		->Execute()->fetchAll();
if (count($przedm) != 0) {
    $przedmioty = array();
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
<h3>Sala <?php echo $sala; ?> <a href="<?php echo URL::site('sale/index'); ?>">[ powrót ]</a></h3>
<?php if (count($res) == 0): ?>
    <p class="info">Brak przedmiotów skojarzonych z tą salą</p>
<?php else: ?>
    <h3>Przedmioty skojarzone z tą salą</h3>
    <ul>
	<?php foreach ($res as $rowid => $rowcol): ?>
	    <li><?php echo $rowcol['przedmiot']; ?>
		<a href="<?php echo URL::site('sale/przedusun/' . $rowcol['sala'] . '/' . $rowcol['przedmiot']) ?>">[ wypisz ]</a></li>
	<?php endforeach; ?>
    </ul>
<?php endif; ?>
<?php if (count($przedmioty) == 0): ?>
    <p class="info">Brak przedmiotów do przypisania.</p>
<?php else: ?>
    <form action="<?php echo URL::site('sale/dodajprzedm') ?>" method="post">
        <b>Wybierz przedmiot: </b>
        <input type="hidden" name="formSala" value="<?php echo $sala; ?>"/>
        <select name="selPrzed">
	    <?php foreach ($przedmioty as $pid => $pcol): ?>
		<option><?php echo $pcol; ?></option>
	    <?php endforeach; ?>
        </select>
        <button type="submit" name="btnSubmit">Dodaj przedmiot do sali</button>
    </form>
<?php endif; ?>

