<?php
if (isset($_SERVER['HTTP_USER_AGENT'])) {
    echo 'Uruchom ten plik z uprawnieniami administratora w konsoli';
    exit;
}
echo 'Stosowanie uprawnien...'.PHP_EOL;
fopen('./config.php', 'w');
chmod('./config.php', 0777);
chmod('./application/logs', 0777);
chmod('./application/cache', 0777);
echo 'zakonczono. Jezeli blad nadal wystepuje, prosze recznie ustawic prawa'.PHP_EOL;