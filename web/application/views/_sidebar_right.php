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
                <a href="<?php echo URL::site('admin/doRenewToken'); ?>">
                    Odnów mój token
                </a>
            </li>
            <li>
		<?php if (defined('ldap_enable') && ldap_enable == "true"): ?>
    		Zmiana hasła w trybie LDAP jest niemożliwa
		<?php else: ?>
    		<img src="<?php echo URL::base() ?>lib/icons/password.png" alt=""/>
                    <a href="<?php echo URL::site('admin/doChangePassword'); ?>">
                        Zmień moje hasło
                    </a>
		<?php endif; ?>
            </li>
            <li>
                <img src="<?php echo URL::base() ?>lib/icons/logout.png" alt=""/>
                <a href="<?php echo URL::site('admin/doLogout'); ?>">
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
    	<a href="<?php echo URL::site('admin/doEditSettings'); ?>">
    	    Ustawienia strony głównej
    	</a>
        </p>
        <p>
    	<img src="<?php echo URL::base() ?>lib/icons/alert.png" alt=""/>
    	<a  href="<?php echo url::site('admin/doCleanupSystem'); ?>">
    	    Wyczyść system
    	</a>
        </p>
        <!--<p>
        <img src="<?php echo URL::base(); ?>lib/icons/backup.png" alt=""/>
        <a href="<?php echo URL::site('admin/backup'); ?>">Kopia zapasowa</a>
        </p>-->
    <?php endif; ?>
    <?php if (App_Globals::getSysLv() == 3): ?>
        <p>
    	<img src="<?php echo URL::base(); ?>lib/icons/save.png" alt=""/>
    	<a href="<?php echo URL::site('plan/export'); ?>">Eksport planów</a>
        </p>
    <?php endif; ?>
</td>
<!-- [/SEKCJA] -->