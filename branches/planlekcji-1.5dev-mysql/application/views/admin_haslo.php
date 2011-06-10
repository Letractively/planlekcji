<?php
/*
 * Zmiana hasła administratora
 */
?>
<h1>Zmiana hasła administratora</h1>
<form action="<?php echo URL::site('admin/chpass'); ?>" method="post" name="form">
    <p class="info">
        Wszystkie pola muszą być wypełnione oraz hasło musi mieć min. 6 znaków
    </p>
    <p>Stare hasło: <input type="password" name="inpSH"/></p>
    <p>Nowe hasło: <input type="password" name="inpNH"/></p>
    <p>Powtórz hasło: <input type="password" name="inpPH"/></p>
    <a href="#" onClick="document.forms['form'].submit();">[ zmiana hasła ]</a>
    <?php
    switch ($_tplerr) {
        case 'false':
            ?>
            <p class="error">
                Podane hasła się nie zgadzają, bądź nie mają odpowiedniej długości
                6 znaków.
            </p>
            <?php
            break;
        case 'pass':
            ?>
            <p class="notice">
                Hasło użytkownika zostało zmienione
            </p>
            <?php
            break;
        default:
            break;
    }
    ?>
</form>