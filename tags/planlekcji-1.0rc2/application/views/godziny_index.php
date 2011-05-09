<?php
/*
 * Obsługa godzin lekcyjnych
 * 
 * 
 */
$isf = new Kohana_Isf();
$isf->DbConnect();
$res = $isf->DbSelect('rejestr', array('*'), 'where opcja="ilosc_godzin_lek"');
$dlugosc = $isf->DbSelect('rejestr', array('*'), 'where opcja="dlugosc_lekcji"');
if (count($res) == 0) {
    $isf->DbInsert('rejestr', array('opcja' => 'ilosc_godzin_lek', 'wartosc' => '0'));
    Kohana_Request::factory()->redirect('godziny/index');
}
?>
<h1>Zarządzanie godzinami lekcyjnymi</h1>
<p><b>Ustaw ilość oraz długość godzin lekcyjnych</b></p>
<form action="<?php echo URL::site('godziny/ustaw'); ?>" method="post">
    <p>
        Ilość godzin: <select name="iloscgodzin">
            <?php for ($i = 1; $i <= 15; $i++): ?>
                <?php if ($res[1]['wartosc'] == $i): ?>
                    <option selected><?php echo $i; ?></option>
                <?php else: ?>
                    <option><?php echo $i; ?></option>
                <?php endif; ?>
            <?php endfor; ?>
        </select>&emsp;
        Długość godziny lekcyjnej:
        <select name="dlugosclekcji">
            <?php for ($i = 5; $i <= 60; $i = $i + 5): ?>
                <?php if ($dlugosc[1]['wartosc'] == $i): ?>
                    <option selected><?php echo $i; ?></option>
                <?php else: ?>
                    <option><?php echo $i; ?></option>
                <?php endif; ?>
            <?php endfor; ?>
        </select>
        min&emsp;
        <button type="submit" name="btnSubmit">Zastosuj</button>
    </p>
</form>
<p class="info">Lekcja 1 rozpoczyna się o godzinie 8:00</p>
<?php if ($res[1]['wartosc'] == 0): ?>
    <p class="info">Obecna ilość godzin lekcyjnych jest ustawiona na <b>0</b>!</p>
<?php else: ?>
    <form action="<?php echo URL::site('godziny/lekcje'); ?>" method="post">
        <table>
            <thead>
                <tr>
                    <td>

                    </td>
                    <td>
                        Długość przerwy po lekcji
                    </td>
                    <td>
                        Ustawiony czas lekcji
                    </td>
                </tr>
            </thead>
            <tbody>
                <?php for ($i = 1; $i <= $res[1]['wartosc']; $i++): ?>
                    <tr>
                        <td>
                            <?php echo $i; ?>
                        </td>
                        <td>
                            <?php $godz = $isf->DbSelect('lek_godziny', array('godzina', 'dl_prz'), 'where lekcja="' . $i . '"'); ?>
                            <input type="text" name="lekcja[<?php echo $i; ?>]" id="lekcja<?php echo $i; ?>" size="5"
                                   value="<?php
                    if (count($godz) == 0):
                        echo '00:00';
                    else:
                        echo $godz[1]['dl_prz'];
                    endif;
                            ?>"/>
                        </td>
                        <td>
                            <?php
                            if (count($godz) == 0):
                                echo '';
                            else:
                                echo $godz[1]['godzina'];
                            endif;
                            ?>
                        </td>
                    </tr>
                <?php endfor; ?>
            </tbody>
        </table>
        <p>Upewnij się że wszystkie dane zostały wpisane.&nbsp;
            <button type="submit" name="btnSubmitLek">Ustaw godziny</button></p>
    </form>
<?php endif; ?>
