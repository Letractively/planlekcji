<?php
/*
 * Zamknięcie trybu edycji Planu Lekcji
 */
$db = new Kohana_Isf();
$db->Connect(APP_DBSYS);
$c = count($db->DbSelect('uzytkownicy', array('*'), 'where login != \'root\''));
?>
<h3>Przejście do modułu Planu Zajęć</h3>
<p>&emsp;&bull;&nbsp;Pozwoli tworzyć plany zajęć dla poszczególnych
    klas, oraz otworzy system dla osób postronnych, w celu przeglądania planów
    zajęć. Nim zamkniesz możliwość edycji, upewnij się, że wszystkie dane w poszczególnych
    kategoriach menu, zostały wypełnione.</p>
<p>&emsp;&bull;&nbsp;<b>Powrót do możliwości edycji danych systtemu</b> jest możliwy jako opcja <b>Wyczyść System</b>,
    uruchomiona z poziomu konta <b>root</b>.</p>
<?php if ($c == 0): ?>
    <p class="info">   
        <span class="ui-icon ui-icon-alert" style="float: left; margin-right: 0.3em;"></span>
        Zalecane jest utworzenie co najmniej jednego konta zwykłego użytkownika.
    </p>
<?php endif; ?>
<form action="<?php echo URL::site('admin/doEditTimetablesPOST'); ?>" method="post">

    <button type="submit">
	Przejdź do modułu Plan Zajęć
    </button>

</form>
