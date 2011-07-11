<?php
/*
 * Zamknięcie trybu edycji Planu Lekcji
 */
$db = new Kohana_Isf();
$db->DbConnect();
$c = count($db->DbSelect('uzytkownicy', array('*'), 'where login != \'root\''));
?>
<?php if($c != 0): ?>
<h1>Zamknięcie edycji systemu</h1>
<p>&emsp;&bull;&nbsp;<b>Zamknięcie edycji systemu</b> pozwoli tworzyć plany zajęć dla poszczególnych
    klas, oraz otworzy system dla osób postronnych, w celu przeglądania planów
    zajęć. Nim zamkniesz możliwość edycji, upewnij się, że wszystkie dane w poszczególnych
    kategoriach menu, zostały wypełnione.</p>
<p>&emsp;&bull;&nbsp;<b>Powrót do możliwości edycji</b> jest możliwy jako opcja <b>resetowania systemu</b>,
    jednak usunięciu mogą ulec najmniej plany zajęć, a nawet wszystkie dane systemu!</p>
<p>&emsp;&bull;&nbsp;<b>Dostęp ograniczonej edycji </b>uniemożliwi edycję sal, przedmiotów, nauczycieli
    w systemie, klas oraz godzin lekcyjnych. Czy napewno chcesz zamknąć edycję systemu?</p>
<form action="<?php echo URL::site('admin/zamknijconfirm'); ?>" method="post">
    <button type="submit" name="btnSubmit">Zamknij edycję systemu</button>
</form>
<?php else: ?>
<h1>Zamknięcie edycji systemu</h1>
<p>&emsp;&bull;&nbsp;<b>Zamknięcie edycji</b> wymaga utworzenia zwykłego konta
użytkownika systemu Plan Lekcji. Konto <b>roota</b> nie jest upoważnione do
edycji planów.</p>
<?php endif; ?>