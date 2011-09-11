<?php
/*
 * Zamknięcie edycji planu
 */
?>
<h1>Zamknięcie edycji planów zajęć</h1>
<h3>Dzięki zamknięciu edycji planów zajęć</h3>
<ul>
    <li>Uzyskasz dostęp do systemu zastępstw (tylko z zajęciami przypisanymi do nauczycieli)</li>
    <li>Plany zajęć będą ogólnodostępne</li>
    <li>Możliwość wygenerowania planów do plików HTML</li>
</ul>
<h3>Przed zamknięciem upewnij się, że wszystkie plany zostały wprowadzone!</h3>
<form action="<?php echo URL::site('admin/zamknijconfirm2'); ?>" method="post">
    <button type="submit" name="btnS">
        Wykonaj operację
    </button>
</form>