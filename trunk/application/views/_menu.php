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
        <?php echo View::factory('_menu_plany')->render(); ?>
    <?php else: ?>
        <p class="info">Dopóki system edycji planów będzie otwarty, nie ma możliwości
            podglądu planu zajęć oraz zastępstw.</p>
    <?php endif; ?>
<?php endif; ?>