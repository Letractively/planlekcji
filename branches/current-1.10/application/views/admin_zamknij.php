<?php
/*
 * Zamknięcie trybu edycji Planu Lekcji
 */
$db = new Kohana_Isf();
$db->Connect(APP_DBSYS);
$c = count($db->DbSelect('uzytkownicy', array('*'), 'where login != \'root\''));
?>
<?php if ($c != 0): ?>
    <p class="info">
        <span class="ui-icon ui-icon-info" style="float: left; margin-right: 0.3em;"></span>
        <b>Zamknięcie edycji systemu</b>
    </p>
    <p>&emsp;&bull;&nbsp;<b>Zamknięcie edycji systemu</b> pozwoli tworzyć plany zajęć dla poszczególnych
        klas, oraz otworzy system dla osób postronnych, w celu przeglądania planów
        zajęć. Nim zamkniesz możliwość edycji, upewnij się, że wszystkie dane w poszczególnych
        kategoriach menu, zostały wypełnione.</p>
    <p>&emsp;&bull;&nbsp;<b>Powrót do możliwości edycji</b> jest możliwy jako opcja <b>resetowania systemu</b>,
        jednak usunięciu mogą ulec najmniej plany zajęć, a nawet wszystkie dane systemu!</p>
    <p>&emsp;&bull;&nbsp;<b>Dostęp ograniczonej edycji </b>uniemożliwi edycję sal, przedmiotów, nauczycieli
        w systemie, klas oraz godzin lekcyjnych. Czy napewno chcesz zamknąć edycję systemu?</p>
    <form action="<?php echo URL::site('admin/zamknijconfirm'); ?>" method="post">
        <button type="submit">
    	Zamknij edycję systemu
        </button>
    </form>
<?php else: ?>
    <p class="info">
        <span class="ui-icon ui-icon-alert" style="float: left; margin-right: 0.3em;"></span>
        Zamknięcie edycji wymaga utworzenia zwykłego konta
        użytkownika systemu Plan Lekcji. Konto <b>roota</b> nie jest upoważnione do
        edycji planów.
    </p>
<?php endif; ?>