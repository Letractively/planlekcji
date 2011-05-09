++++++++++++++++++++++++++++	   ###        #####
+                          +	   ###       #######
+  P L A N    L E K C J I  +	    ##       ##   ##
+  I  N  T  E  R  S  Y  S  +	    ##       ##   ##
+                          +	 ######## ## #######
++++++++++++++++++++++++++++	 ######## ##  #####

1. LICENCJA

Na stan obecny (1.0 beta 3), dozwolone jest u¿ycie tylko w celach
testowych

2. U¯YTE OPROGRAMOWANIE

KOHANA3			BSD
ISF1.0 KOHANA3 MODULE	GNU GPL
TINY_MCE		GNU GPL

Wymaga min. PHP 5.3 oraz obs³ugi SQLite3.
Katalog /modules/isf/isf_resources musi mieæ uprawnienia do zapisu (dot. systemów Unix oraz Windows Server)

3. INSTALACJA


 * Wypakuj do folderu aplikacji www z obs³ug¹ PHP.
 * Uruchom plik index.php w wierszu poleceñ ( informacje w dokumentacji)
   php index.php

4. ROZWI¥ZYWANIE PROBLEMÓW

- Pokazuje siê strona o b³êdach URL i Kohany:
	Czy strona znajduje siê w podfolderze? Tzn., ¿e dostêp poprzez adres HTTP wymaga po nazwie hosta,
	dodatkowej nazwy folderu? Edytuj plik index.php

	$path = '/podfolder(y)/';

5. AUTORZY

 * Micha³ Bocian <mhl.bocian@gmail.com>