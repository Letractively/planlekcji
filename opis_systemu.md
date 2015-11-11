# Rejestr systemowy #
System prowadzi rejestr sytemowy w wewnętrznej bazie danych SQLite.
## Poziomy edycji danych (edycja\_danych) ##
  * 0 - edycja planów zajęć
  * 1 - edycja danych systemu (sale, przedmioty, nauczyciele, klasy)
  * 3 - otwarcie systemu dla wszystkich (podgląd planów zajęć, edycja zastępstw)

## Prawa użytkowników ##
W trakcie instalacji tworzony jest główny użytkownik **root**, posiadający losowe hasło (możliwość zmiany) oraz stały token. Root może tylko tworzyć sale, przedmioty, klasy, nauczycieli i użytkowników, jednak nie ma praw do edycji planów zajęć. Dostęp do planów zajęć wymaga utworzenia użytkownika oraz wygenerowania tokenów. W przypadku zamknięcia systemu, root może tylko tworzyć tokeny dla użytkowników oraz zmieniać dane strony głównej.

  * root - zmiana danych systemu, tworzenie sal, przedmiotów itp.
  * pozostali - tworzenie planów, zastępstw

# Co po instalacji? #
Należy bezwzględnie ustawić ilość godzin lekcyjnych oraz czas przerwy między nimi (system automatycznie utworzy siatkę godzin).

# Tokeny #
W przeciwieństwie do **roota**, pozostali używkownicy muszą posiadać losowo wygenerowane tokeny przez administratora (zalecane jest wydrukowanie listy). Każdy token można użyć tylko raz.

# Logowanie #
Logowanie wymaga podania loginu, hasła oraz tokena. W przeciwieństwie do roota, ilość nieprawidłowych zalogowań jest ograniczona (3 nieudane logowania blokują konto, po nieudanym logowaniu (< 3), każde pomyślnie zeruje licznik nieprawidłowych zalogowań).

# Zablokowane konto #
Każde zablokowane konto, należy usunąć przez roota, i wygenerować nowe.