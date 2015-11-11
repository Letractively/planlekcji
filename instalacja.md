# Gdy warunki nie są spełnione #
System automatycznie powiadomi, jakie warunki muszą być spełnione, aby można było zainstalować IPL.

# Na systemie UNIX #
W odróżnieniu od systemu Windows, systemy Unix-owe (Linux, BSD, MacOS X) posiadają restrykcyjne prawa katalogów. Specjalnie na potrzeby projektu, został napisany skrypt PHP ustawiający odpowiednie prawa dla katalogów. Po wypakowaniu archiwum, należy uruchomić w konsoli następujące polecenia:

```
 $ cd [sciezka do aplikacji]
 $ sudo php unixinstall.php
```

Następnie można już uruchomić instalator z poziomu przeglądarki.

# Po instalacji #

Należy usunąć pliki _install.php_ oraz _unixinstall.php_. System automatycznie podpowie, jakie kroki należy podjąć.