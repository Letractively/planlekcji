<?php
/*
 * Usuwanie sali
 * 
 * 
 */
?>
<h1>Usunięcie sali <b><?php echo $sala; ?></b></h1>
<?php
if ($ilosc_przed != 0) {
    ?>
    <p><b>Przedmioty skojarzone z tą salą:</b></p>
    <ul>
        <?php foreach ($sala_przedm as $rowid => $rowcol): ?>
            <li><?php echo $rowcol['przedmiot']; ?></li>
        <?php endforeach; ?>
    </ul>
    <?php
}else {
    ?>
    <p class="info">Brak przedmiotów skojarzonych z tą klasą</p>
    <?php
}
?>
<p><b>Uwaga!</b> Przypisanie przedmiotów do danej
    sali zostanie usunięte.
</p>
<p>Czy napewno chcesz usunąć? <b><a href="<?php echo URL::site('sale/index'); ?>">[ nie ]</a> </b>
    <a href="<?php echo URL::site('sale/usun/' . $sala . '/true'); ?>">[ tak ]</a></p>