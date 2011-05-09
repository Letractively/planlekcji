<?php
/**
 * Plik stalych systemowych
 *
 * @author Michal Bocian <mhl.bocian@gmail.com>
 * @package isf\root\defines
 * @version 1.0
 */
/**
 * kompletna sciezka http (NIE ZMIENIAC)
 */
define('HTTP_ADDR', 'http://' . $_SERVER['SERVER_NAME'] . HTTP_PATH);

/**
 * Wersja frameworka
 */
define('APP_VER', '1.0.0');

/**
 * Sciezka do katalogu z szablonami
 */
define('TPL_PATH', APP_PATH . DS . 'templates');

/**
 * Sciezka HTTP do folderu z szablonami
 */
define('TPL_ADDR', HTTP_ADDR . 'templates');

/**
 * Domyslny motyw JQuery UI
 */
define('JQUI_DEF_THEME', 'flick');

/**
 * Domyslny czas trwania ciasteczka
 * Domyslnie 1 godzina
 */
define('DEFAULT_COOKIE_TIME', time() + 3600 * 1);

/**
 * Domyslna sciezka ciasteczka
 * Domyslnie dla calej domeny /
 */
define('DEFAULT_COOKIE_PATH', '/');