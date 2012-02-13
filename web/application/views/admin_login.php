<?php
/*
 * Logowanie adminisdivatora 
 */

if (!isset($_POST['inpLogin'])) {
    $_POST['inpLogin'] = null;
}
?>
<div class="a_odd">
    <b>
	Logowanie do systemu
	(<?php echo (defined('ldap_enable') && ldap_enable == "true") ? 'LDAP' : 'RAND_TOKEN'; ?>)
    </b>
</div>
<h1>Witamy w systemie Internetowy Plan Lekcji</h1>
<p>
    Aby uzyskać dostęp do systemu, proszę o podanie właściwych danych niezbędnych
    do autoryzacji w systemie.
</p>
<br/>
<form action="<?php echo url::site('admin/dologin'); ?>" method="post" name="lgn">
    <div class="tableDiv">
	<div class="tableRow">
	    <div class="tableCell">
		<label for="inpLogin">Login</label>
	    </div>
	    <div class="tableCell">
		<input class="inpLoginForm" type="text" name="inpLogin" value="<?php echo $_POST['inpLogin']; ?>"/>
	    </div>
	</div>
	<div class="tableRow">
	    <div class="tableCell">
		<label for="inpHaslo">Hasło</label>
	    </div>
	    <div class="tableCell">
		<input class="inpLoginForm" type="password" name="inpHaslo" value=""/>
		<button type="submit" name="btnSubmit" id="btnSubmit">Zaloguj</button>
	    </div>
	</div>
	<?php if (!defined('ldap_enable') || ldap_enable != "true"): ?>
    	<div class="tableRow">
    	    <div class="tableCell">
    		<label for="inpToken">Token</label>
    	    </div>
    	    <div class="tableCell">
    		<input class="inpLoginForm" type="text" name="inpToken" value=""/>
    	    </div>
    	</div>
	<?php endif; ?>
    </div>
</form>
<br/>
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
