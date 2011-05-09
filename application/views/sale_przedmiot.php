<h1>Przedmioty sali [ <?php echo $sala; ?> ]</h1>
<a href="<?php echo URL::site('sale/index'); ?>">[ powrót ]</a>
<?php if ($c == 0): ?>
    <p class="info">Brak przedmiotów skojarzonych z tą salą</p>
<?php else: ?>
    <p><b>Przedmioty skojarzone z tą salą:</b></p>
    <ul>
        <?php foreach ($res as $rowid => $rowcol): ?>
        <li><b><?php echo $rowcol['przedmiot']; ?></b>
            <a href="<?php echo URL::site('sale/przedusun/'.$rowcol['sala'].'/'.$rowcol['przedmiot']) ?>">[ wypisz ]</a></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>
<?php if (count($przed) == 0): ?>
    <p class="info">Brak przedmiotów do przypisania.</p>
<?php else: ?>
    <form action="<?php echo URL::site('sale/dodajprzedm') ?>" method="post">
        <b>Wybierz przedmiot: </b>
        <input type="hidden" name="formSala" value="<?php echo $sala; ?>"/>
        <select name="selPrzed">
            <?php foreach ($przed as $pid=>$pcol): ?>
            <option><?php echo $pcol['przedmiot']; ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit" name="btnSubmit">Skojarz</button>
    </form>
<?php endif; ?>

