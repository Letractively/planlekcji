++++++++++++++++++++++++++++	   ###        #####
+                          +	   ###       #######
+  P L A N    L E K C J I  +	    ##       ##   ##
+  I  N  T  E  R  S  Y  S  +	    ##       ##   ##
+                          +	 ######## ## #######
++++++++++++++++++++++++++++	 ######## ##  #####

1. LICENCJA

Na stan obecny (1.0 beta 3), dozwolone jest u�ycie tylko w celach
testowych

2. U�YTE OPROGRAMOWANIE

KOHANA3			BSD
ISF1.0 KOHANA3 MODULE	GNU GPL
TINY_MCE		GNU GPL

Wymaga min. PHP 5.3 oraz obs�ugi SQLite3.
Katalog /modules/isf/isf_resources musi mie� uprawnienia do zapisu (dot. system�w Unix oraz Windows Server)

3. INSTALACJA


 * Wypakuj do folderu aplikacji www z obs�ug� PHP.
 * Uruchom plik index.php w wierszu polece� ( informacje w dokumentacji)
   php index.php

4. ROZWI�ZYWANIE PROBLEM�W

- Pokazuje si� strona o b��dach URL i Kohany:
	Czy strona znajduje si� w podfolderze? Tzn., �e dost�p poprzez adres HTTP wymaga po nazwie hosta,
	dodatkowej nazwy folderu? Edytuj plik index.php

	$path = '/podfolder(y)/';

5. AUTORZY

 * Micha� Bocian <mhl.bocian@gmail.com>