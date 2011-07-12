<?php
/**
 * Instalator dla systemow Unixowych
 * 
 * Ustawia prawa dla katalogow
 * 
 * @author Michał Bocian <mhl.bocian@gmail.com>
 * @license GNU GPL v3
 * @version 1.5
 */
/**
 * Wymaga uruchomienia z konsoli (CLI)
 */
if (isset($_SERVER['HTTP_USER_AGENT'])) {
    echo 'Uruchom ten plik z uprawnieniami administratora w konsoli';
    exit;
}
if (function_exists('posix_getuid') && posix_getuid() != 0) {
    die('Na systemie UNIX musisz uruchomic z prawami roota');
}
echo 'Stosowanie uprawnien...'.PHP_EOL;
fopen('./config.php', 'w');
chmod('./modules/isf/isf_resources', 0777);
if(!file_exists('./modules/isf/isf_resources/default.sqlite')){
    fopen('./modules/isf/isf_resources/default.sqlite', 'w');
}
chmod('./modules/isf/isf_resources/default.sqlite', 0777);
chmod('./config.php', 0777);
chmod('./application/logs', 0777);
chmod('./application/cache', 0777);
echo 'zakonczono. Jezeli blad nadal wystepuje, prosze recznie ustawic prawa'.PHP_EOL;