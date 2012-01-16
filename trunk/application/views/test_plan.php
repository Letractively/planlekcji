<?php
$klasa = '2D';

$isf = new Kohana_Isf();
$isf->Connect(APP_DBSYS);

$przedmioty = array(
    'język polski',
    'matematyka',
    'przeds',
    'etyka',
    'religia',
    'informatyka',
    'po',
    'wf',
    'język angielski',
    'język niemiecki',
    'język francuski',
    'historia',
    'biologia',
    'chemia',
    'geografia',
    'fizyka',
    'wos',
    'lw'
);

//$przedmioty = sort($przedmioty);

if (!isset($przedmioty)) {
    foreach ($isf->DbSelect('przedmioty', array('*'), 'order by przedmiot asc') as $rowid => $rowcol) {
	$przedmioty[$rowcol['przedmiot']];
    }
}

$dni = array(
    'Poniedziałek',
    'Wtorek',
    'Środa',
    'Czwartek',
    'Piątek',
);

$igl = App_Globals::getRegistryKey('ilosc_godzin_lek');
?>

<!DOCTYPE html>
<html lang="pl">
    <head>
	<meta charset="UTF-8"/>
	<title>ETES</title>
	<style type="text/css">
	    @import url('<?php echo URL::base(); ?>lib/css/style.css');
	    @import url('<?php echo URL::base(); ?>lib/css/themes/domyslny.css');
	    body{
		margin: 10px;
	    }
	    .opt{
		width: 180px;
		max-width: 180px;
	    }
	</style>
    </head>
    <body>
	<div id="container">
	    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
		<table class="przed">
		    <thead class="a_odd">
			<tr>
			    <td></td>
			    <?php foreach ($dni as $dzien): ?>
    			    <td><?php echo $dzien; ?></td>
			    <?php endforeach; ?>
			</tr>
		    </thead>
		    <tbody>
			<?php for ($l = 1; $l <= $igl; $l++): ?>
    			<tr <?php echo ($l % 2 == 1) ? 'class="a_even"' : ''; ?>>
    			    <td>
    				<b><?php echo $l; ?></b>
    			    </td>
				<?php foreach ($dni as $dzien): ?>
				    <td>
					<?php
					$select = '<select name="' . $dzien . '[' . $l . ']" class="opt">';

					$z_cond = 'where dzien=\'' . $dzien . '\' and lekcja=\'' . $l . '\'';
					$z_cond .= ' and klasa=\'' . $klasa . '\'';
					$z_lek = $isf->DbSelect('planlek', array('*'), $z_cond);

					$g_cond = 'where dzien=\'' . $dzien . '\' and lekcja=\'' . $l . '\'';
					$g_cond .= ' and klasa=\'' . $klasa . '\'';
					$g_lek = $isf->DbSelect('plan_grupy', array('*'), $z_cond);
					if (count($z_lek) == 1) {
					    ?>
	    				<span class="grptxt">
						<?php echo $z_lek[0]['przedmiot']; ?> -
						<?php echo $z_lek[0]['skrot']; ?> -
						<?php echo $z_lek[0]['sala']; ?>
	    				</span>
					    <?php
					    $select.='<option selected  class="a_even">' . $z_lek[0]['przedmiot'] . ':';
					    $select.=$z_lek[0]['sala'] . ':';
					    $select.=$z_lek[0]['nauczyciel'] . '</option>';
					} else if (count($g_lek) >= 1) {
					    ?>
	    				<span class="grptxt">
	    				    <b>Zajęcia w grupach</b>
	    				</span>
					    <?php
					}

					$select .= '<option label="brak zajęć" class="a_odd">---</option>';

					foreach ($przedmioty as $przedmiot) {
					    $select .= '<optgroup label="' . $przedmiot . '">';
					    $select .= '<option label="zwykła lekcja">' . $przedmiot . '</option>';

					    $cond = 'where klasa=\'' . $klasa . '\' and
						nauczyciel=(select nauczyciel from nl_przedm
						where przedmiot=\'' . $przedmiot . '\')';

					    $nl = $isf->DbSelect('nl_klasy', array('nauczyciel'), $cond);
					    foreach ($nl as $rid => $rcl) {
						
						$select .= '<option label="' . $rcl['nauczyciel'] . '"
						    disabled="disabled"></option>';
						
						$sale_cond = 'where przedmiot=\'' . $przedmiot . '\' and
						(sala != (select sala from planlek where
						dzien=\'' . $dzien . '\' and lekcja=\'' . $l . '\')
						or sala != (select sala from plan_grupy where
						dzien=\'' . $dzien . '\' and lekcja=\'' . $l . '\')) order by sala asc';

						$sale = $isf->DbSelect('przedmiot_sale', array('sala'), $sale_cond);
						foreach ($sale as $rid => $rcl) {
						    $select .= '<option>'.$rcl['sala'].'</option>';
						}
					    }

					    $select .= '</optgroup>';
					}

					$select .= '</select>';
					?>
					<p>
					    <?php echo $select; ?>
					</p>
				    </td>
				<?php endforeach; ?>
    			</tr>
			<?php endfor; ?>
		    </tbody>
		</table>
		<button type="submit" name="btnSubmit">Zapisz</button>
	    </form>
	</div>
    </body>
</html>