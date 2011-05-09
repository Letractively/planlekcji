<?php $isf = new Kohana_Isf(); ?>
<?php $isf->DbConnect(); ?>
<?php $nres = $isf->DbSelect('nl_przedm', array('przedmiot'), 'where nauczyciel="' . $nauczyciel . '"'); ?>
<h1>Usunięcie nauczyciela <b><?php echo $nauczyciel; ?></b></h1>
<?php if (count($nres) == 0): ?>
    <p class="info">Brak przedmiotów nauczanych przez nauczyciela</p>
<?php else: ?>
    <p><b>Przedmioty nauczane przez nauczyciela</b></p>
    <ul>
        <?php foreach ($nres as $rcl => $rid): ?>
        <li><?php echo $rid['przedmiot']; ?></li>
        <?php endforeach; ?>
    </ul>

<?php endif; ?>
<?php
$klasy = $isf->DbSelect('nl_klasy', array('klasa'), 'where nauczyciel="'.$nauczyciel.'"');
if (count($klasy) != 0) {
    ?>
    <p><b>Klasy nauczane przez nauczyciela <?php echo $nauczyciel; ?></b></p>
    <ul>
        <?php foreach ($klasy as $rowid => $rowcol): ?>
            <li><?php echo $rowcol['klasa']; ?></li>
        <?php endforeach; ?>
    </ul>
    <?php
}else {
    ?>
    <p class="info">Brak nauczanych klas</p>
    <?php
}
?>
<p class="info"><b>Uwaga!</b> Przypisanie przedmiotów oraz klas <b>zostanie usunięte</b>.</p>
<p>Czy napewno chcesz usunąć? <b><a href="<?php echo URL::site('nauczyciele/index'); ?>" id="a_nie">[ nie ]</a> </b>
    <a href="<?php echo URL::site('nauczyciele/usun/' . $nauczyciel . '/true'); ?>" id="a_tak">[ tak ]</a></p>