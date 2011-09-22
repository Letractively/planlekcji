<?php
/*
 * Zmiana danych systemu Plan Lekcji
 * 
 * 
 */
$isf = new Kohana_Isf();
$isf->DbConnect();
/** pobiera nazwe szkoly */
$nazwa = $isf->DbSelect('rejestr', array('*'), 'where opcja=\'nazwa_szkoly\'');
/** pobiera tresc strony glownej */
$msg = $isf->DbSelect('rejestr', array('*'), 'where opcja=\'index_text\'');
?>
<form action="<?php echo URL::site('admin/dochange'); ?>" method="post" name="formPlan">
    <p style="text-align: center;">
        <button type="submit" name="btnSubmit">Zapisz ustawienia</button>
    </p>
    <input
        type="text"
        style="width: 100%; font-size: 14pt;"
        name="inpNazwa"
        value="<?php echo $nazwa[1]['wartosc']; ?>"
        />
    <p>
        <textarea name="txtMsg" style="width: 100%" rows="30">
            <?php echo $msg[1]['wartosc']; ?>
        </textarea>
    </p>
</form>