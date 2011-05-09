<?php $isf = new Kohana_Isf(); ?>
<?php $isf->DbConnect(); ?>
<?php $nres = $isf->DbSelect('nl_przedm', array('nauczyciel'), 'where przedmiot="' . $przedmiot . '"'); ?>
<h1>Usunięcie przedmiotu <b><?php echo $przedmiot; ?></b></h1>
<?php if (count($nres) == 0): ?>
    <p class="info">Brak nauczycieli uczących tego przedmiotu</p>
<?php else: ?>
    <p><b>Nauczyciele uczący tego przedmiotu</b></p>
    <ul>
        <?php foreach ($nres as $rcl => $rid): ?>
        <li><?php echo $rid['nauczyciel']; ?></li>
        <?php endforeach; ?>
    </ul>

<?php endif; ?>
<?php
if ($ilosc_sal != 0) {
    ?>
    <p><b>Sale skojarzone z tym przedmiotem:</b></p>
    <ul>
        <?php foreach ($sala_przedm as $rowid => $rowcol): ?>
            <li><?php echo $rowcol['sala']; ?></li>
        <?php endforeach; ?>
    </ul>
    <?php
}else {
    ?>
    <p class="info">Brak sal skojarzonych z tym przedmiotem</p>
    <?php
}
?>
<p class="info"><b>Uwaga!</b> Przypisanie sal i nauczycieli do danego
    przedmiotu zostanie usunięte.
</p>
<p>Czy napewno chcesz usunąć? <b><a href="<?php echo URL::site('przedmioty/index'); ?>">[ nie ]</a> </b>
    <a href="<?php echo URL::site('przedmioty/usun/' . $przedmiot . '/true'); ?>">[ tak ]</a></p>