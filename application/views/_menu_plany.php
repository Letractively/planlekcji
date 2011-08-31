<?php $isf = new Kohana_Isf(); ?>
<?php $isf->DbConnect(); ?>
<?php if (App_Globals::getSysLv() == 3): // gdy edycja planow zamknieta   ?>
    <h3>Plany lekcji według klas</h3>
    <ul>
        <?php foreach ($isf->DbSelect('klasy', array('*'), 'order by klasa asc') as $rw => $rc): ?>
            <li><a href="<?php echo URL::site('podglad/klasa/' . $rc['klasa']); ?>" target="_blank"><?php echo $rc['klasa']; ?></a></li>
        <?php endforeach; ?>
    </ul>
    <h3>Plany lekcji według sali</h3>
    <ul>
        <?php foreach ($isf->DbSelect('sale', array('*'), 'order by sala asc') as $rw => $rc): ?>
            <li><a href="<?php echo URL::site('podglad/sala/' . $rc['sala']); ?>" target="_blank"><?php echo $rc['sala']; ?></a></li>
        <?php endforeach; ?>    
    </ul>
    <h3>Plany lekcji według nauczycieli</h3>
    <ul>
        <?php foreach ($isf->DbSelect('nauczyciele', array('*'), 'order by imie_naz asc') as $rw => $rc): ?>
            <li><?php echo $rc['skrot']; ?> <a href="<?php echo URL::site('podglad/nauczyciel/' . $rc['skrot']); ?>" target="_blank"><?php echo $rc['imie_naz']; ?></a></li>
        <?php endforeach; ?>    
    </ul>
<?php else: ?>
    <p class="info">Podgląd planów będzie dostępny po zamknięciu edycji planów zajęć.</p>
<?php endif; ?>