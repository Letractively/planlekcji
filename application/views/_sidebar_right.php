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
<td valign="top" style="width: 20%;">
    <fieldset>
        <legend>
            <img src="<?php echo URL::base() ?>lib/images/user.gif" alt=""/>
            Zalogowany jako: <b><?php echo $_SESSION['user']; ?></b>
        </legend>
        <ul style="font-size: 8pt; list-style: none; padding: 0px;">
            <li>
                <a href="<?php echo URL::site('admin/renew'); ?>">
                    <img src="<?php echo URL::base() ?>lib/images/keylabel.gif" alt=""/>
                    Odnów mój token
                </a>
            </li>
            <li>
                <a href="<?php echo URL::site('admin/haslo'); ?>">
                    <img src="<?php echo URL::base() ?>lib/images/keys.gif" alt=""/>
                    Zmień moje hasło
                </a>
            </li>
            <li>
                <a href="<?php echo URL::site('admin/logout'); ?>">
                    <img src="<?php echo URL::base() ?>lib/images/keygenoff.gif" alt=""/>
                    Wyloguj mnie
                </a>
            </li>
            <li>
                <b>Token wygasa o: </b> <?php echo $tokenizer; ?>
            </li>
        </ul>
    </fieldset>
    <?php if (App_Globals::getSysLv() == 3 && isset($_SESSION['token'])): ?>
        <p>
            <img src="<?php echo URL::base(); ?>lib/images/save.png" alt="" width="16" height="16"/>
            <a href="#" onClick="window.open('<?php echo URL::base(); ?>export.php', 'moje', 'width=500,height=500,scrollbars=1')" >Eksport planu zajęć</a>
        </p>
    <?php endif; ?>
    <?php if ($_SESSION['user'] == 'root'): ?>
        <p>
            <a href="<?php echo URL::site('admin/zmiendane'); ?>">
                <img src="<?php echo URL::base() ?>lib/images/settings.gif" alt=""/>
                Ustawienia szkoły i strony głównej
            </a>
        </p>
        <p>
            <a  href="<?php echo url::site('admin/reset'); ?>">
                <img src="<?php echo URL::base() ?>lib/images/warn.gif" alt=""/>
                Wyczyść system
            </a>
        </p>
        <p>
            <img src="<?php echo URL::base(); ?>lib/images/betasign.png" alt="" height="12"/>
        </p>
        <?php if (App_Globals::getSysLv() == 0 && $_SESSION['user'] == 'root'): ?>
            <p>
                <img src="<?php echo URL::base(); ?>lib/images/registry.png" alt="" width="16" height="16"/>
                <a href="#" onClick="window.open('<?php echo URL::base(); ?>generator.php', 'moje', 'width=500,height=500,scrollbars=1')" >Generator planu zajęć</a>
            </p>
        <?php endif; ?>
        <p>
            <img src="<?php echo URL::base(); ?>lib/images/save.png" alt="" width="16" height="16"/>
            <a href="#" onClick="window.open('<?php echo URL::base(); ?>tools/backup.php', 'moje', 'width=500,height=500,scrollbars=1')" >Kopia zapasowa systemu</a>
        </p>
    <?php endif; ?>
    <?php if ($_SESSION['user'] != 'root' && App_Globals::getSysLv() == 0): ?>
        <p>
            <img src="<?php echo URL::base(); ?>lib/images/betasign.png" alt="" height="12"/>
        </p>
        <p>
            <img src="<?php echo URL::base() ?>lib/images/warn.gif" alt=""/>
            <a href="#" onClick="window.open('<?php echo URL::base(); ?>generator.php', 'moje', 'width=500,height=500,scrollbars=1')" >
                Generator planów zajęć
            </a>
        </p>
    <?php endif; ?>
</td>
<!-- [/SEKCJA] -->