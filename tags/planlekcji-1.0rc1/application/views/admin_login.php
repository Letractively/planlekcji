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
    <label>Hasło</label> <input style="width: 150px;" type="password" name="inpHaslo"/><br/><br/>
    <a href="#" onClick="document.forms['lgn'].submit();">[ zaloguj ]</a>
    <a href="<?php echo URL::site(''); ?>">[ powrót ]</a>
</form>