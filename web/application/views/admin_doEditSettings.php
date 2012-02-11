<?php
/*
 * Zmiana danych systemu Plan Lekcji
 * 
 * 
 */
$isf = new Kohana_Isf();
$isf->Connect(APP_DBSYS);
/** pobiera nazwe szkoly */
$nazwa = $isf->DbSelect('rejestr', array('*'), 'where opcja=\'nazwa_szkoly\'');
/** pobiera tresc strony glownej */
$msg = $isf->DbSelect('rejestr', array('*'), 'where opcja=\'index_text\'');
?>
<form action="<?php echo URL::site('admin/doEditSettingsPOST'); ?>" method="post" name="formPlan">
    <fiedset>
	<legend><h3>Ustawienia systemu</h3></legend>
	<div class="tableDiv" style="width: 100%;">
	    <div class="tableRow">
		<div class="tableCell">
		    <label for="inpNazwa">Nazwa szkoły</label>
		</div>
		<div class="tableCell">
		    <input
			type="text"
			style="width: 100%"
			name="inpNazwa"
			value="<?php echo $nazwa[0]['wartosc']; ?>"
			/>
		</div>
	    </div>
	</div>
    </fiedset>
    <p>
        <textarea name="txtMsg" style="width: 100%;" rows="30">
	    <?php echo $msg[0]['wartosc']; ?>
        </textarea>
    </p>
    <p style="text-align: right">
	<button type="submit" name="btnSubmit">Zapisz ustawienia</button>
    </p>
</form>