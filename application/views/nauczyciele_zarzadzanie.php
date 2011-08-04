<?php
/*
 * Zarządzanie nauczycielami
 * 
 * 
 */
$isf = new Kohana_Isf();
$isf->DbConnect();
?>
<h1><?php echo $nauczyciel . ' (' . $nskr . ') - zarządzanie'; ?></h1>

<a href="<?php echo URL::site('nauczyciele/index'); ?>">[ powrót ]</a>

<p><b>Nauczane klasy:</b></p>
<?php $kls = $isf->DbSelect('nl_klasy', array('klasa'), 'where nauczyciel="' . $nauczyciel . '" order by klasa asc'); ?>
<?php if (count($kls) == 0): ?>
    <p class="info">Brak nauczanych klas</p>
<?php else: ?>
    <ul>
        <?php foreach ($kls as $r => $c): ?>
            <li>
                <?php echo $c['klasa']; ?>
                <a href="<?php echo URL::site('nauczyciele/klwyp/' . $nauczyciel . '/' . $c['klasa']); ?>">[ wypisz ]</a>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<?php
$klasy = $isf->DbSelect('klasy', array('klasa'), 'except select klasa from nl_klasy where nauczyciel="' . $nauczyciel . '"');
?>
<?php if (count($klasy) == 0): ?>
    <p class="info">Brak dostępnych klas</p>
<?php else: ?>
    <form action="<?php echo URL::site('nauczyciele/dodklasa') ?>" method="post">
        <b>Wybierz klasę: </b>
        <input type="hidden" name="Nauczyciel" value="<?php echo $nauczyciel; ?>"/>
        <select name="selKlasy">
            <?php foreach ($klasy as $sid => $scol): ?>
                <option><?php echo $scol['klasa']; ?></option>
            <?php endforeach; ?>
        </select>&nbsp;
        <button type="submit" name="btnSubmit">Dodaj klasę</button>
    </form>
<?php endif; ?>

<p><b>Nauczane przedmioty:</b></p>
<?php $nlp = $isf->DbSelect('nl_przedm', array('przedmiot'), 'where nauczyciel="' . $nauczyciel . '"'); ?>
<?php if (count($nlp) == 0): ?>
    <p class="info">Brak nauczanych przedmiotów</p>
<?php else: ?>
    <ul>
        <?php foreach ($nlp as $r => $c): ?>
            <li><?php echo $c['przedmiot']; ?> <a
                    href="<?php echo URL::site('nauczyciele/przwyp/' . $nauczyciel . '/' . $c['przedmiot']); ?>">[ wypisz ]</a></li>
            <?php endforeach; ?>
    </ul>
<?php endif; ?>
<?php
$przedm = $isf->DbSelect('przedmioty', array('przedmiot'), 'except select przedmiot from nl_przedm where nauczyciel="' . $nauczyciel . '"');
?>
<?php if (count($przedm) == 0): ?>
    <p class="info">Brak dostępnych przedmiotów</p>
<?php else: ?>
    <form action="<?php echo URL::site('nauczyciele/dodprzed') ?>" method="post">
        <b>Wybierz przedmiot: </b>
        <input type="hidden" name="Nauczyciel" value="<?php echo $nauczyciel; ?>"/>
        <select name="selPrzedm">
            <?php foreach ($przedm as $sid => $scol): ?>
                <option><?php echo $scol['przedmiot']; ?></option>
            <?php endforeach; ?>
        </select>&nbsp;
        <button type="submit" name="btnSubmit">Dodaj przedmiot</button>
    </form>
<?php endif; ?>