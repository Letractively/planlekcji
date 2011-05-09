<?php
/*
 * Logowanie administratora
 * 
 * 
 */
?>
<h1>Logowanie do systemu</h1>
<form action="<?php echo url::site('admin/dologin'); ?>" method="post" name="lgn">
    <label>Login</label> <input style="width: 150px;" type="text" name="inpLogin"/><br/><br/>
    <label>Hasło</label> <input style="width: 150px;" type="password" name="inpHaslo"/>&emsp;
    <button type="submit" name="btnSubmit">Zaloguj</button>
</form>
<?php if($pass=='false'): ?>
<p class="error">Nie udało się zalogować do systemu</p>
<?php endif; ?>
