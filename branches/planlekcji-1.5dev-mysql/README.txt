PLAN LEKCJI - WERSJA 1.5 MySQL

1. LICENCJA

Plan Lekcji Intersys został wydany na licencji GNU GPL v3

2. UŻYTE OPROGRAMOWANIE 

KOHANA3			BSD
ISF1.0 KOHANA3 MODULE	GNU GPL	(AUTORSKI FRAMEWORK http://isframework.googlecode.com)
TINY_MCE		GNU GPL
NUSOAP                  GNU GPL

Wymaga min. PHP 5.2.5 oraz obsługi PDO SQLite3.
Katalog /modules/isf/isf_resources musi mieć uprawnienia do zapisu (dot. systemów Unix oraz Windows Server)

3. INSTALACJA

 * Wypakuj do folderu aplikacji www z obsługą PHP.
 * Na systemie UNIX: uruchom w konsoli przez PHP plik z
   poziomu katalogu aplikacji [unixinstall.php] z prawami root-a
 * Uruchom stronę w przeglądarce
 * W razie konieczności, wykonuj polecenia podane na stronie

4. ROZWIĄZYWANIE PROBLEMÓW 

- Pokazuje się strona o błędach URL i Kohany:
  Czy strona znajduje się w podfolderze? Tzn., że dostęp poprzez adres HTTP wymaga po nazwie hosta,
  dodatkowej nazwy folderu? Edytuj plik config.php

  $path = '/podfolder(y)/';

5. AUTORZY

 * Michał Bocian <mhl.bocian@gmail.com>