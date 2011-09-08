<!-- [SEKCJA]: SIDEBAR MENU -->
<?php $isf = new Kohana_Isf(); ?>
<?php $isf->DbConnect(); ?>
<?php if ($_SESSION['token'] == null): ?>
    <!-- menu dla niezalogowanych -->
    <?php echo View::factory()->render('_menu'); ?>
<?php else: ?>

    <?php if (App_Globals::getSysLv() == 1 && $_SESSION['user'] == 'root'): //gdy edycja sal etc  ?>
        <!-- menu dla roota -->
        <?php echo View::factory()->render('_menu_root_1'); ?>
    <?php else: ?>

        <?php if (App_Globals::getSysLv() == 3 && $_SESSION['user'] != 'root'): //gdy system calkiem otwarty ?>
            <!-- menu uzytkownika -->
            <?php echo View::factory()->render('_menu_user_3'); ?>
        <?php endif; ?>

        <?php if (App_Globals::getSysLv() == 1 && $_SESSION['user'] != 'root'): //gdy edycja sys ?>
            <!-- uzytkownik -->
            <p class="error">Witaj <b><?php echo $_SESSION['user']; ?></b>. Niestety, nie masz dostępu do
                edycji sal, przedmiotów, godzin, klas i nauczycieli.</p>
        <?php endif; ?>

        <?php if (App_Globals::getSysLv() == 0 && $_SESSION['user'] != 'root'): //gdy edycja planow ?>  
            <!-- uzytkownik -->
            <?php echo View::factory()->render('_menu_user_0'); ?>
        <?php endif; ?>

        <?php if (App_Globals::getSysLv() != 1 && $_SESSION['user'] == 'root'): //gdy edycja planow i zamkn sys ?>
            <!-- menu dla root -->
            <?php echo View::factory()->render('_menu_close_root'); ?>
        <?php endif; ?>

        <hr/>

        <!-- plany lekcji -->
        <?php echo View::factory()->render('_menu_plany'); ?>

    <?php endif; ?>
<?php endif; ?>
<!-- [/SEKCJA] -->