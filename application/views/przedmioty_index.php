<?php $isf = new Kohana_Isf(); ?>
<?php $isf->DbConnect(); ?>
<h1>Zarządzanie przedmiotami</h1>
<form action="<?php echo URL::site('przedmioty/dodajform'); ?>" method="post">
    Przedmiot: <input type="text" name="inpPrzedmiot"/>
    <button type="submit" name="btnSubmit">Dodaj przedmiot</button>
</form>
<?php if (count($res) == 0): ?>
    <p class="info">Brak zdefiniowanych przedmiotów. Dodaj nowy przedmiot.</p>
<?php else: ?>
    <table>
        <thead>
            <tr style="height: 30px; font-weight: bold;">
                <td style="width: 100px;">Przedmiot</td>
                <td style="width: 150px; max-width: 200px;">Przypisane sale</td>
                <td>Nauczyciele uczący</td>
                <td></td>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($res as $rowid => $rowcol): ?>
                <tr>
                    <td><a href="<?php echo URL::site('przedmioty/zarzadzanie/'.$rowcol['przedmiot']); ?>"><?php echo $rowcol['przedmiot']; ?></a></td>
                    <td>
                        <b>
                        <?php foreach ($isf->DbSelect('przedmiot_sale', array('sala'), 'where przedmiot="'.$rowcol['przedmiot'].'" order by sala asc')
                                as $rid=>$rcl): ?>
                        <?php echo $rcl['sala']; ?>, 
                        <?php endforeach; ?>
                        </b>
                    </td>
                    <td>
                        <?php foreach ($isf->DbSelect('nl_przedm', array('nauczyciel'), 'where przedmiot="'.$rowcol['przedmiot'].'" order by nauczyciel asc')
                                as $rid=>$rcl): ?>
                        <?php echo $rcl['nauczyciel']; ?>, 
                        <?php endforeach; ?>
                    </td>
                    <td><a href="<?php echo URL::site('przedmioty/sale/' . $rowcol['przedmiot']); ?>">
                            [ sale ]</a>
                        <a href="<?php echo URL::site('przedmioty/zarzadzanie/'.$rowcol['przedmiot']); ?>">[ zarządzanie ]</a>
                        <a href="<?php echo URL::site('przedmioty/usun/' . $rowcol['przedmiot']); ?>">[ usuń przedmiot ]
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
