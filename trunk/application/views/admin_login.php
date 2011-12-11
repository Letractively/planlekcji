<?php
/*
 * Logowanie administratora 
 */

if (!isset($_POST['inpLogin'])) {
    $_POST['inpLogin'] = null;
}
?>
<div class="a_odd" style="width: 780px;">
    <b>Logowanie metodą RAND_TOKEN</b>
</div>
<style type="text/css">
    input{
        height: 14pt;
        font-size: 14pt;
        text-align: center;
    }
</style>

<form action="<?php echo url::site('admin/dologin'); ?>" method="post" name="lgn">
    <div class="a_light_menu">
        <table border="0" style="width: 775px;">
            <tbody>
                <tr>
                    <td style="width: 100px;">Login</td>
                    <td style="width: 675px;">
                        <input style="width: 100%;" type="text" name="inpLogin" value="<?php echo $_POST['inpLogin']; ?>"/>
                    </td>
                </tr>
                <tr>
                    <td>Hasło</td>
                    <td><input style="width: 100%;" type="password" name="inpHaslo"/></td>
                </tr>
                <tr>
                    <td>Token sesji</td>
                    <td><input style="width: 100%;" type="text" name="inpToken"/></td>
                </tr>
		<?php if ($pass == 'false'): ?>
    		<tr class="a_error">
    		    <td colspan="2">
    			Nie udało się zalogować do systemu
    		    </td>
    		</tr>
		<?php endif; ?>
		<?php if ($pass == 'locked'): ?>
    		<tr class="a_error">
    		    <td colspan="2">
    			Twoje konto jest zablokowane
    		    </td>
    		</tr>
		<?php endif; ?>
		<?php if ($pass == 'delay'): ?>
    		<tr class="a_error">
    		    <td colspan="2">
    			Twój token wygasł
    		    </td>
    		</tr>
		<?php endif; ?>
		<?php if ($pass == 'exist'): ?>
    		<tr class="a_error">
    		    <td colspan="2">
    			Token został nadpisany. Prawdopodobnie inna osoba zalogowała się
			na to konto.
    		    </td>
    		</tr>
		<?php endif; ?>
                <tr style="text-align: center">
                    <td></td>
                    <td>
                        <button type="submit" name="btnSubmit" id="btnSubmit">Zaloguj</button>&emsp;
                        <button type="reset" name="btnReset" id="btnReset">Wyczyść dane</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</form>


