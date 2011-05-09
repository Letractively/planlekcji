<?php $isf = new Kohana_Isf(); ?>
<?php $isf->DbConnect(); ?>
<h1><?php echo $przedmiot . ' - zarządzanie'; ?></h1>

<a href="<?php echo URL::site('przedmioty/index'); ?>">[ powrót ]</a>

<p><b>Nauczyciele uczący:</b></p>
<?php $prs = $isf->DbSelect('nl_przedm', array('*'), 'where przedmiot="' . $przedmiot . '" order by przedmiot asc'); ?>
<?php if (count($prs) == 0): ?>
    <p class="info">Brak nauczycieli uczących tego przedmiotu</p>
<?php else: ?>
    <ul>
        <?php foreach ($prs as $r => $c): ?>
            <li><?php echo $c['nauczyciel']; ?>
                <a href="<?php echo URL::site('przedmioty/wypisz/' . $przedmiot . '/' . $c['nauczyciel']); ?>">[ wypisz ]</a></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>
<p><b>Przypisanie nauczyciela</b></p>
<?php $prz = $isf->DbSelect('nauczyciele', array('imie_naz'), 'except select nauczyciel from nl_przedm where przedmiot="' . $przedmiot . '"'); ?>
<form action="<?php echo url::site('przedmioty/nlprzyp'); ?>" method="post">
    <input type="hidden" name="przedmiot" value="<?php echo $przedmiot; ?>"/>
    <select name="selNaucz">
        <?php foreach ($prz as $rid => $rcl): ?>
            <option><?php echo $rcl['imie_naz']; ?></option>
        <?php endforeach; ?>
    </select>
    &nbsp;<button type="submit" name="btnSubmit">Przypisz nauczyciela</button>
</form>