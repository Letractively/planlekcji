<?php
/*
 * Zamknięcie edycji planu
 */
?>
<h1>Zamknięcie edycji planu</h1>
<p>Czy napewno chcesz zamknąć edycję planu i mieć dostęp do wygenerowanych
    planów zajęć oraz do systemu zastępstw?</p>
<form action="<?php echo URL::site('admin/zamknijconfirm2'); ?>" method="post">
    <button type="submit" name="btnS">
        Zamknij edycję planów
    </button>
</form>