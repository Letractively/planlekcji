PLAN LEKCJI - WERSJA 1.5

1. LICENCJA

Plan Lekcji Intersys został wydany na licencji GNU GPL v3

2. UŻYTE OPROGRAMOWANIE 

KOHANA3			BSD
ISF1.0 KOHANA3 MODULE	GNU GPL	(AUTORSKI FRAMEWORK http://isframework.googlecode.com)
TINY_MCE		GNU GPL

Wymaga min. PHP 5.3 oraz obsługi SQLite3.
Katalog /modules/isf/isf_resources musi mieć uprawnienia do zapisu (dot. systemów Unix oraz Windows Server)

3. INSTALACJA

 * Wypakuj do folderu aplikacji www z obsługą PHP.
 * Uruchom plik index.php w wierszu poleceń ( informacje w dokumentacji)
   php index.php

4. ROZWIĄZYWANIE PROBLEMÓW 

- Pokazuje się strona o błędach URL i Kohany:
  Czy strona znajduje się w podfolderze? Tzn., że dostęp poprzez adres HTTP wymaga po nazwie hosta,
  dodatkowej nazwy folderu? Edytuj plik index.php

  $path = '/podfolder(y)/';

5. AUTORZY

 * Michał Bocian <mhl.bocian@gmail.com>