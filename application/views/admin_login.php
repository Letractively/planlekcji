<?php
/*
 * Logowanie administratora 
 */

if (!isset($_POST['inpLogin'])) {
    $_POST['inpLogin'] = null;
}
?>
<h1>Logowanie do systemu</h1>
<h3>Za pomocą metody RAND_TOKEN</h3>
<?php if ($pass == 'false'): ?>
    <p class="error">Nie udało się zalogować do systemu</p>
<?php endif; ?>
<?php if ($pass == 'locked'): ?>
    <p class="error"><b>Konto jest zablokowane</b>. Skontaktuj się z administratorem</p>
<?php endif; ?>
<?php if ($pass == 'delay'): ?>
    <p class="error"><b>RAND_TOKEN:</b> token wygasł</p>
<?php endif; ?>
<form action="<?php echo url::site('admin/dologin'); ?>" method="post" name="lgn">
    <label>Login</label> <input style="width: 150px;" type="text" name="inpLogin" value="<?php echo $_POST['inpLogin']; ?>"/><br/><br/>
    <label>Hasło</label> <input style="width: 150px;" type="password" name="inpHaslo"/><br/><br/>
    <label>Token</label> <input style="width: 150px;" type="text" name="inpToken"/>&emsp;
    <button type="submit" name="btnSubmit">Zaloguj</button>
    <p class="info">Token jest wymagany wobec wszystkich użytkowników razem z hasłem podstawowym.
        Każdy token można użyć <b>tylko raz</b>, jednak można go przedłużać na następne 3 godziny.
        Standardowa ważność po zalogowaniu to 3 godziny.</p>
</form>
