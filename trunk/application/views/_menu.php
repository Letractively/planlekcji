<?php $isf = new Kohana_Isf(); ?>
<?php $isf->DbConnect(); ?>
<?php if (App_Globals::getSysLv() == 1): ?>
    <p>
        <a href="<?php echo URL::site('admin/login'); ?>" style="font-size: 10pt; font-weight: bold;">
            <img src="<?php echo URL::base(); ?>lib/images/t1.png" alt="" width="24" height="24"/> Administracja
        </a>
    </p>
    <p class="info">System będzie niedostępny, dopóki opcja edycji sal, przedmiotów, itp.
        będzie <b>włączona</b>.</p>
    <?php
else:
    ?>
    <p>
        <a href="<?php echo URL::site('admin/login'); ?>" style="font-size: 10pt; font-weight: bold;">
            <img src="<?php echo URL::base(); ?>lib/images/t1.png" alt="" width="24" height="24"/> Administracja
        </a>
    </p>
    <?php if (App_Globals::getSysLv() == 3): //gdy system jest calkowicie otwarty bez edycji sal, czy planow ?>
        <p>
            <a href="<?php echo URL::site('zastepstwa/index'); ?>" style="font-size: 10pt; font-weight: bold;">
                <img src="<?php echo URL::base(); ?>lib/images/notes.png" alt="" width="24" height="24"/> Zastępstwa
            </a>
        </p>
        <p>
            <img src="<?php echo URL::base(); ?>lib/images/t2.png" alt="" width="24" height="24"/>
            <a href="<?php echo URL::site('podglad/zestawienie'); ?>" style="font-size: 10pt; font-weight: bold;" target="_blank">
                Zestawienie planów
            </a>
        </p>
        <h3>Plany lekcji według klas</h3>
        <p class="a_klasy">
            <?php foreach ($isf->DbSelect('klasy', array('*'), 'order by klasa asc') as $rw => $rc): ?>
                <a target="_blank" href="<?php echo URL::site('podglad/klasa/' . $rc['klasa']); ?>"><?php echo $rc['klasa']; ?></a>&emsp;
            <?php endforeach; ?>
        </p>
        <h3>Plany lekcji według sali</h3>
        <p class="a_klasy">
            <?php foreach ($isf->DbSelect('sale', array('*'), 'order by sala asc') as $rw => $rc): ?>
                <a target="_blank" href="<?php echo URL::site('podglad/sala/' . $rc['sala']); ?>"><?php echo $rc['sala']; ?></a>&emsp;
            <?php endforeach; ?>    
        </p>
        <h3>Plany lekcji według nauczycieli</h3>
        <p class="a_klasy">
            <?php foreach ($isf->DbSelect('nauczyciele', array('*'), 'order by imie_naz asc') as $rw => $rc): ?>
                <?php echo $rc['skrot']; ?>-<a href="<?php echo URL::site('podglad/nauczyciel/' . $rc['skrot']); ?>" target="_blank"><?php echo $rc['imie_naz']; ?></a>&emsp;
            <?php endforeach; ?>    
        </p>
    <?php else: ?>
        <p class="info">Dopóki system edycji planów będzie otwarty, nie ma możliwości
            podglądu planu zajęć oraz zastępstw.</p>
    <?php endif; ?>
<?php endif; ?>