<?php
/*
 * Obsługa godzin lekcyjnych
 * 
 * 
 */
$isf = new Kohana_Isf();
$isf->Connect(APP_DBSYS);
/** pobiera ilosc godzin lekcyjnych */
$res = $isf->DbSelect('rejestr', array('*'), 'where opcja=\'ilosc_godzin_lek\'');
/** pobiera dlogosc lekcji w min */
$dlugosc = $isf->DbSelect('rejestr', array('*'), 'where opcja=\'dlugosc_lekcji\'');

$grz = $isf->DbSelect('rejestr', array('*'), 'where opcja=\'godz_rozp_zaj\'');

/** gdy nie ma takiego klucza w rejestrze tworzy go i przeladowuje strone */
if (count($res) == 0) {
    $isf->DbInsert('rejestr', array('opcja' => 'ilosc_godzin_lek', 'wartosc' => '0'));
    Kohana_Request::factory()->redirect('godziny/index');
}
?>
<table border="0" style="width: 100%;">
    <tr valign="top">
        <td style="width: 50%;">
            <?php if ($res[0]['wartosc'] == 0): ?>
                <p class="info">Obecna ilość godzin lekcyjnych jest ustawiona na <b>0</b>!</p>
            <?php else: ?>
                <form action="<?php echo URL::site('godziny/lekcje'); ?>" method="post">
                    <table style="width:100%;">
                        <thead>
                            <tr class="a_odd">
                                <td colspan="3" style="text-align: center;">
                                    <p>
                                        Godzina rozpoczęcia zajęć:
                                        <input type="text" name="czasRZ" id="czasRZ" value="<?php echo $grz[0]['wartosc']; ?>" size="6"/>
                                    </p>
                                </td>
                            </tr>
                            <tr class="a_even">
                                <td>

                                </td>
                                <td>
                                    Przerwa
                                </td>
                                <td>
                                    Ustawiony czas lekcji
                                </td>
                            </tr>
                        </thead>
                        <tbody>
                            <?php for ($i = 1; $i <= $res[0]['wartosc']; $i++): ?>
                                <?php if ($i % 2 == 0): ?>
                                    <?php $class = " class='a_even'"; ?>
                                <?php else: ?>
                                    <?php $class = ""; ?>
                                <?php endif; ?>
                                <tr <?php echo $class; ?>>
                                    <td>
                                        <?php echo $i; ?>
                                    </td>
                                    <td>
                                        <?php $godz = $isf->DbSelect('lek_godziny', array('godzina', 'dl_prz'), 'where lekcja=\'' . $i . '\''); ?>
                                        <input type="text" name="lekcja[<?php echo $i; ?>]" id="lekcja<?php echo $i; ?>" size="5"
                                               value="<?php
                                if (count($godz) == 0):
                                    echo '00:00';
                                else:
                                    echo $godz[0]['dl_prz'];
                                endif;
                                        ?>"/>
                                    </td>
                                    <td>
                                        <?php
                                        if (count($godz) == 0):
                                            echo '';
                                        else:
                                            echo $godz[0]['godzina'];
                                        endif;
                                        ?>
                                    </td>
                                </tr>
                            <?php endfor; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" style="text-align: center">
                                    <p>
                                        <button type="submit" name="btnSubmitLek" class="button-jq ui-state-default ui-button">
                                            Ustaw godziny
                                        </button>
                                    </p>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </form>
            <?php endif; ?>
        </td>
        <td style="width: 50%;">
            <p><b>Ustaw ilość oraz długość godzin lekcyjnych</b></p>
            <form action="<?php echo URL::site('godziny/ustaw'); ?>" method="post">
                <p>
                    Ilość godzin: <select name="iloscgodzin">
                        <?php for ($i = 1; $i <= 15; $i++): ?>
                            <?php if ($res[0]['wartosc'] == $i): ?>
                                <option selected><?php echo $i; ?></option>
                            <?php else: ?>
                                <option><?php echo $i; ?></option>
                            <?php endif; ?>
                        <?php endfor; ?>
                    </select><br/>
                    Długość godziny lekcyjnej:
                    <select name="dlugosclekcji">
                        <?php for ($i = 5; $i <= 60; $i = $i + 5): ?>
                            <?php if ($dlugosc[0]['wartosc'] == $i): ?>
                                <option selected><?php echo $i; ?></option>
                            <?php else: ?>
                                <option><?php echo $i; ?></option>
                            <?php endif; ?>
                        <?php endfor; ?>
                    </select>
                    min
                </p>
                <button type="submit" name="btnSubmit" class="button-jq ui-state-default ui-button">
                    Zastosuj ustawienia
                </button>
            </form>
        </td>
    </tr>
</table>