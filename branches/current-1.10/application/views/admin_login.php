<?php
/*
 * Logowanie adminisdivatora 
 */

if (!isset($_POST['inpLogin'])) {
    $_POST['inpLogin'] = null;
}
?>
<div class="a_odd" style="width: 780px;">
    <b>Logowanie metodą RAND_TOKEN</b>
</div>
<?php if ($pass == 'false'): ?>
    <div class="loginFormError a_error">
        Nie udało się zalogować do systemu
    </div>
<?php endif; ?>
<?php if ($pass == 'locked'): ?>
    <div class="loginFormError a_error">
        Twoje konto jest zablokowane
    </div>
<?php endif; ?>
<?php if ($pass == 'delay'): ?>
    <div class="loginFormError a_error">
        Twój token wygasł
    </div>
<?php endif; ?>
<?php if ($pass == 'exist'): ?>
    <div class="loginFormError a_error">
        Token został nadpisany. Prawdopodobnie inna osoba zalogowała się
        na to konto.
    </div>
<?php endif; ?>
<form action="<?php echo url::site('admin/dologin'); ?>" method="post" name="lgn">
    <div class="tableDiv">
	<div class="tableRow">
	    <div class="tableCell">
		<label for="inpLogin">Login</label>
	    </div>
	    <div class="tableCell">
		<input class="a_light_menu inpLoginForm" type="text" name="inpLogin" value=""/><p></p>
	    </div>
	</div>
	<div class="tableRow">
	    <div class="tableCell">
		<label for="inpHaslo">Hasło</label>
	    </div>
	    <div class="tableCell">
		<input class="a_light_menu inpLoginForm" type="password" name="inpHaslo" value=""/><p></p>
	    </div>
	</div>
	<div class="tableRow">
	    <div class="tableCell">
		<label for="inpToken">Token</label>
	    </div>
	    <div class="tableCell">
		<input class="a_light_menu inpLoginForm" type="text" name="inpToken" value=""/>
	    </div>
	    <div class="tableCell">
		<button type="submit" name="btnSubmit" id="btnSubmit">Zaloguj</button>
	    </div>
	</div>
    </div>
</form>



