<?php
/*
 * Logowanie administratora 
 */

if (!isset($_POST['inpLogin'])) {
    $_POST['inpLogin'] = null;
}
?>

<form action="<?php echo url::site('admin/dologin'); ?>" method="post" name="lgn">
    <table border="0">
        <thead style="background-color: tan; text-align: center">
            <tr>
                <td colspan="2">
                    Logowanie metodą RAND_TOKEN
                </td>
            </tr>
        </thead>
        <tbody style="background-color: lightgrey;">
            <tr>
                <td>Login</td>
                <td><input style="width: 150px;" type="text" name="inpLogin" value="<?php echo $_POST['inpLogin']; ?>"/></td>
            </tr>
            <tr>
                <td>Hasło</td>
                <td><input style="width: 150px;" type="password" name="inpHaslo"/></td>
            </tr>
            <tr>
                <td>Token sesji</td>
                <td><input style="width: 150px;" type="text" name="inpToken"/></td>
            </tr>
            <?php if ($pass == 'false'): ?>
                <tr style="background-color: red; color: white; text-align: center;">
                    <td colspan="2">
                        Nie udało się zalogować do systemu
                    </td>
                </tr>
            <?php endif; ?>
            <?php if ($pass == 'locked'): ?>
                <tr style="background-color: red; color: white; text-align: center;">
                    <td colspan="2">
                        Twoje konto jest zablokowane
                    </td>
                </tr>
            <?php endif; ?>
            <?php if ($pass == 'delay'): ?>
                <tr style="background-color: red; color: white; text-align: center;">
                    <td colspan="2">
                        Twój token wygasł
                    </td>
                </tr>
            <?php endif; ?>
            <tr style="text-align: center">
                <td colspan="2">
                    <button type="submit" name="btnSubmit" id="btnSubmit">Zaloguj</button>&emsp;
                    <button type="reset" name="btnReset" id="btnReset">Wyczyść dane</button>
                </td>
            </tr>
        </tbody>
    </table>
</form>


