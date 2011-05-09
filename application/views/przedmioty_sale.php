<h1>Sale przedmiotu <?php echo $przedmiot; ?></h1>
<a href="<?php echo URL::site('przedmioty/index'); ?>">[ powrót ]</a>
<?php if ($c == 0): ?>
    <p><b>Brak sal skojarzonych z tym przedmiotem</b></p>
<?php else: ?>
    <p><b>Sale skojarzone z tym przedmiotem:</b></p>
    <ul>
        <?php foreach ($res as $rowid => $rowcol): ?>
        <li><b><?php echo $rowcol['sala']; ?></b>
            <a href="<?php echo URL::site('przedmioty/przypisusun/'.$przedmiot.'/'.$rowcol['sala']) ?>">[ wypisz ]</a></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>
<?php if (count($sale) == 0): ?>
    <p><b>Brak do przypisania.</b></p>
<?php else: ?>
    <form action="<?php echo URL::site('przedmioty/dodajsale') ?>" method="post">
        <b>Wybierz salę: </b>
        <input type="hidden" name="formPrzedmiot" value="<?php echo $przedmiot; ?>"/>
        <select name="selSale">
            <?php foreach ($sale as $sid=>$scol): ?>
            <option><?php echo $scol['sala']; ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit" name="btnSubmit">Przypisz salę</button>
    </form>
<?php endif; ?>

