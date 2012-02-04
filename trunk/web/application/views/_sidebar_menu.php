<!-- [SEKCJA]: SIDEBAR MENU -->
<?php $isf = new Kohana_Isf(); ?>
<?php $isf->Connect(APP_DBSYS); ?>
<?php if ($_SESSION['token'] == null): ?>
    <!-- menu dla niezalogowanych -->
    <?php echo View::factory()->render('_menu'); ?>
<?php else: ?>

    <?php if (App_Globals::getSysLv() == 0): // Edycja planow zajec ?>
	<?php if ($_SESSION['user'] == 'root'): ?>
	    <?php echo View::factory()->render('_menu_close_root'); ?>
	<?php endif; ?>
	<?php echo View::factory()->render('_menu_user_0'); ?>
    <?php endif; ?>

    <?php if (App_Globals::getSysLv() == 1): // Edycja danych systemu ?>
	<?php if ($_SESSION['user'] == 'root'): ?>
	    <?php echo View::factory()->render('_menu_root_1'); ?>
	<?php else: ?>
	    <p class="error">Witaj <b><?php echo $_SESSION['user']; ?></b>.
	        Niestety, nie masz dostępu do edycji sal, przedmiotów, godzin,
	        klas i nauczycieli.</p>
	<?php endif; ?>
    <?php endif; ?>

    <?php if (App_Globals::getSysLv() == 3): // Zamkniecie systemu dla edycji ?>
	<?php if ($_SESSION['user'] == 'root'): ?>
	    <?php echo View::factory()->render('_menu_close_root'); ?>
	<?php endif; ?>
	<?php echo View::factory()->render('_menu_user_3'); ?>
	<?php echo View::factory()->render('_menu'); ?>
    <?php endif; ?>
<?php endif; ?>
<!-- [/SEKCJA] -->