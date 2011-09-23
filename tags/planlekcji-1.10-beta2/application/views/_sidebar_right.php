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
                <img src="<?php echo URL::base() ?>lib/images/keylabel.gif" alt=""/>
                <a href="<?php echo URL::site('admin/renew'); ?>">
                    Odnów mój token
                </a>
            </li>
            <li>
                <img src="<?php echo URL::base() ?>lib/images/keys.gif" alt=""/>
                <a href="<?php echo URL::site('admin/haslo'); ?>">
                    Zmień moje hasło
                </a>
            </li>
            <li>
                <img src="<?php echo URL::base() ?>lib/images/keygenoff.gif" alt=""/>
                <a href="<?php echo URL::site('admin/logout'); ?>">
                    Wyloguj mnie
                </a>
            </li>
            <li>
                <b>Token wygasa o: </b> <?php echo $tokenizer; ?>
            </li>
        </ul>
    </fieldset>
    <?php if ($_SESSION['user'] == 'root'): ?>
        <p>
            <img src="<?php echo URL::base() ?>lib/images/settings.gif" alt=""/>
            <a href="<?php echo URL::site('admin/zmiendane'); ?>">
                Ustawienia szkoły i strony głównej
            </a>
        </p>
        <p>
            <img src="<?php echo URL::base() ?>lib/images/warn.gif" alt=""/>
            <a  href="<?php echo url::site('admin/reset'); ?>">
                Wyczyść system
            </a>
        </p>
        <p>
            <img src="<?php echo URL::base(); ?>lib/images/warn2.gif" alt="" width="16" height="16"/>
            <a href="<?php echo URL::site('admin/backup'); ?>">Kopia zapasowa systemu</a>
        </p>
        <?php if (App_Globals::getSysLv() == 3): ?>
            <p>
                <img src="<?php echo URL::base(); ?>lib/images/save.png" alt="" width="16" height="16"/>
                <a href="#" onClick="window.open('<?php echo URL::base(); ?>export.php', 'moje', 'width=500,height=500,scrollbars=1')" >Eksport planu zajęć</a>
            </p>
        <?php endif; ?>
        <?php if (App_Globals::getSysLv() == 0): ?>
            <p>
                <img src="<?php echo URL::base(); ?>lib/images/registry.png" alt="" width="16" height="16"/>
                <a href="#" onClick="window.open('<?php echo URL::base(); ?>generator.php', 'moje', 'width=500,height=500,scrollbars=1')" >Generator planu zajęć</a>
            </p>
        <?php endif; ?>
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