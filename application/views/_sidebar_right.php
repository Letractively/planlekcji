<!-- [SEKCJA]: MENU BOCZNE -->
<?php
$zadmin = time() + 10 * 60;
$toktime = strtotime($_SESSION['token_time']);
if ($zadmin > $toktime) {
    $tokenizer = '<i class="error" style="text-decoration:blink;"><b>' . $_SESSION['token_time'] . '</b></i>';
} else {
    $tokenizer = '<i class="notice">' . $_SESSION['token_time'] . '</i>';
}
?>
<td valign="top">
    <fieldset>
        <legend>
            <img src="<?php echo URL::base() ?>lib/images/user.gif" alt=""/>
            Zalogowany jako: <b><?php echo $_SESSION['user']; ?></b>
        </legend>
        <ul style="font-size: 8pt; list-style: none; padding: 0px;">
            <li>
                <img src="<?php echo URL::base() ?>lib/icons/token.png" alt=""/>
                <a href="<?php echo URL::site('admin/renew'); ?>">
                    Odnów mój token
                </a>
            </li>
            <li>
                <img src="<?php echo URL::base() ?>lib/icons/password.png" alt=""/>
                <a href="<?php echo URL::site('admin/haslo'); ?>">
                    Zmień moje hasło
                </a>
            </li>
            <li>
                <img src="<?php echo URL::base() ?>lib/icons/logout.png" alt=""/>
                <a href="<?php echo URL::site('admin/logout'); ?>">
                    Wyloguj mnie
                </a>
            </li>
            <li>
                <br/>
                <b>Token wygasa o: </b> <?php echo $tokenizer; ?>
            </li>
        </ul>
    </fieldset>
    <?php if ($_SESSION['user'] == 'root'): ?>
        <p>
            <img src="<?php echo URL::base() ?>lib/icons/edit.png" alt=""/>
            <a href="<?php echo URL::site('admin/zmiendane'); ?>">
                Szkoła i strona główna
            </a>
        </p>
        <p>
            <img src="<?php echo URL::base() ?>lib/icons/alert.png" alt=""/>
            <a  href="<?php echo url::site('admin/reset'); ?>">
                Wyczyść system
            </a>
        </p>
        <p>
            <img src="<?php echo URL::base(); ?>lib/icons/backup.png" alt=""/>
            <a href="<?php echo URL::site('admin/backup'); ?>">Kopia zapasowa</a>
        </p>
        <?php if (App_Globals::getSysLv() == 3): ?>
            <p>
                <img src="<?php echo URL::base(); ?>lib/icons/save.png" alt=""/>
                <a href="#" onClick="window.open('<?php echo URL::base(); ?>export.php', 'moje', 'width=500,height=500,scrollbars=1')" >Eksport planów</a>
            </p>
        <?php endif; ?>
        <?php if (App_Globals::getSysLv() == 0): ?>
            <p>
                <img src="<?php echo URL::base(); ?>lib/images/registry.png" alt="" width="16" height="16"/>
                <a href="#" onClick="window.open('<?php echo URL::base(); ?>generator.php', 'moje', 'width=500,height=500,scrollbars=1')" >Generator planów zajęć</a>
            </p>
        <?php endif; ?>
    <?php endif; ?>
    <?php if ($_SESSION['user'] != 'root' && App_Globals::getSysLv() == 0): ?>
        <p>
            <img src="<?php echo URL::base(); ?>lib/images/betasign.png" alt="" height="12"/>
        </p>
        <p>
            <img src="<?php echo URL::base() ?>lib/images/registry.png" alt=""/>
            <a href="#" onClick="window.open('<?php echo URL::base(); ?>generator.php', 'moje', 'width=500,height=500,scrollbars=1')" >
                Generator planów zajęć
            </a>
        </p>
    <?php endif; ?>
</td>
<!-- [/SEKCJA] -->