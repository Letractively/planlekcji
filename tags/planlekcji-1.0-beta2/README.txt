++++++++++++++++++++++++++++  /##\         /####\
+                          +  \###         ######
+  P L A N    L E K C J I  +    ##         ##  ##
+  I  N  T  E  R  S  Y  S  +    ##         ##  ##
+                          +  /####\  ##   ######   beta 2
++++++++++++++++++++++++++++  ######  ##   \####/

LICENCJA
========
Na stan obecny (1.0 beta), dozwolone jest u¿ycie tylko w celach
testowych

U¯YTE OPROGRAMOWANIE
====================
KOHANA3			BSD
ISF1.0 KOHANA3 MODULE	GNU GPL

Wymaga min. PHP 5.3 oraz obs³ugi SQLite3.
Katalog /modules/isf/isf_resources musi mieæ uprawnienia do zapisu (dot. systemów Unix oraz Windows Server)

INSTALACJA
==========
Wypakuj do folderu aplikacji www z obs³ug¹ PHP. Zaloguj siê jako administrator:
http://[adres_hosta]/index.php/admin

ROZWI¥ZYWANIE PROBLEMÓW
=======================
- Pokazuje siê strona o b³êdach URL i Kohany:
	Czy strona znajduje siê w podfolderze? Tzn., ¿e dostêp poprzez adres HTTP wymaga po nazwie hosta,
	dodatkowej nazwy folderu? Edytuj plik application/bootstrap.php

	Kohana::init(array(
		'base_url' => '_tu_to_co_po_http://[nazwa_hosta]',
 //domyslnie '/'
		));

AUTORZY
=======
(c)2011 Micha³ Bocian <mhl.bocian@gmail.com>