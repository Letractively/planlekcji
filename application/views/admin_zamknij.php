<?php
/*
 * Zamknięcie trybu edycji Planu Lekcji
 */
?>
<h1>Zamknięcie edycji systemu</h1>
<p>&bull;&nbsp;<b>Zamknięcie edycji systemu</b> pozwoli tworzyć plany zajęć dla poszczególnych
    klas, oraz otworzy system dla osób postronnych, w celu przeglądania planów
    zajęć. Nim zamkniesz możliwość edycji, upewnij się, że wszystkie dane w poszczególnych
    kategoriach menu, zostały wypełnione.</p>
<p>&bull;&nbsp;<b>Powrót do możliwości edycji</b> jest możliwy jako opcja <b>resetowania systemu</b>,
    jednak usunięciu mogą ulec najmniej plany zajęć, a nawet wszystkie dane systemu!</p>
<p>&bull;&nbsp;<b>Dostęp ograniczonej edycji </b>uniemożliwi edycję sal, przedmiotów, nauczycieli
    w systemie, klas oraz godzin lekcyjnych. Czy napewno chcesz zamknąć edycję systemu?</p>
<p><b>Wybór:</b> &bull; <a class="error" href="<?php echo URL::site('admin/zamknijconfirm'); ?>">zamknij edycję</a>&emsp;
    &bull; <a href="<?php echo URL::site('default/index'); ?>">powrót do edycji danych</a>
</p>
