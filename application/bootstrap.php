<?php

defined('SYSPATH') or die('No direct script access.');

// -- Environment setup --------------------------------------------------------
// Load the core Kohana class
require SYSPATH . 'classes/kohana/core' . EXT;

if (is_file(APPPATH . 'classes/kohana' . EXT)) {
    // Application extends the core
    require APPPATH . 'classes/kohana' . EXT;
} else {
    // Load empty core extension
    require SYSPATH . 'classes/kohana' . EXT;
}

/**
 * Set the default time zone.
 *
 * @see  http://kohanaframework.org/guide/using.configuration
 * @see  http://php.net/timezones
 */
date_default_timezone_set('Europe/Warsaw');

/**
 * Set the default locale.
 *
 * @see  http://kohanaframework.org/guide/using.configuration
 * @see  http://php.net/setlocale
 */
setlocale(LC_ALL, 'pl_PL.utf-8');

/**
 * Enable the Kohana auto-loader.
 *
 * @see  http://kohanaframework.org/guide/using.autoloading
 * @see  http://php.net/spl_autoload_register
 */
spl_autoload_register(array('Kohana', 'auto_load'));

/**
 * Enable the Kohana auto-loader for unserialization.
 *
 * @see  http://php.net/spl_autoload_call
 * @see  http://php.net/manual/var.configuration.php#unserialize-callback-func
 */
ini_set('unserialize_callback_func', 'spl_autoload_call');

// -- Configuration and initialization -----------------------------------------

/**
 * Set the default language
 */
I18n::lang('pl-pl');

/**
 * Set Kohana::$environment if a 'KOHANA_ENV' environment variable has been supplied.
 *
 * Note: If you supply an invalid environment name, a PHP warning will be thrown
 * saying "Couldn't find constant Kohana::<INVALID_ENV_NAME>"
 */
if (isset($_SERVER['KOHANA_ENV'])) {
    Kohana::$environment = constant('Kohana::' . strtoupper($_SERVER['KOHANA_ENV']));
}

/**
 * Initialize Kohana, setting the default options.
 *
 * The following options are available:
 *
 * - string   base_url    path, and optionally domain, of your application   NULL
 * - string   index_file  name of your index file, usually "index.php"       index.php
 * - string   charset     internal character set used for input and output   utf-8
 * - string   cache_dir   set the internal cache directory                   APPPATH/cache
 * - boolean  errors      enable or disable error handling                   TRUE
 * - boolean  profile     enable or disable internal profiling               TRUE
 * - boolean  caching     enable or disable internal caching                 FALSE
 */
Kohana::init(array(
    'base_url' => '/',
));

/**
 * Attach the file write to logging. Multiple writers are supported.
 */
Kohana::$log->attach(new Log_File(APPPATH . 'logs'));

/**
 * Attach a file reader to config. Multiple readers are supported.
 */
Kohana::$config->attach(new Config_File);

/**
 * Enable modules. Modules are referenced by a relative or absolute path.
 */
Kohana::modules(array(
    'isf' => MODPATH . 'isf', // ISFramework for Kohana
));

/**
 * Set the routes. Each route must have a minimum of a name, a URI and a set of
 * defaults for the URI.
 */
Route::set('default', '(<controller>(/<action>(/<id>)))')
        ->defaults(array(
            'controller' => 'default',
            'action' => 'index',
        ));
Route::set('sale', '(sale/usun/<sala>(/<usun>))')->defaults(array(
    'controller'=>'sale',
    'action'=>'usun',
        )
);
Route::set('przedmioty_usun', '(przedmioty/usun/<przedmiot>(/<usun>))')->defaults(array(
    'controller'=>'przedmioty',
    'action'=>'usun',
        )
);
Route::set('przedmioty_przypisusun', '(przedmioty/przypisusun/<przedmiot>/<sala>)')->defaults(array(
    'controller'=>'przedmioty',
    'action'=>'przypisusun',
        )
);
Route::set('sale_przedusun', '(sale/przedusun/<sala>/<przedmiot>)')->defaults(array(
    'controller'=>'sale',
    'action'=>'przedusun',
        )
);
Route::set('nl_usun', '(nauczyciele/usun/<nauczyciel>/<confirm>)')->defaults(array(
    'controller'=>'nauczyciele',
    'action'=>'usun',
        )
);
Route::set('nl_klwyp', '(nauczyciele/klwyp/<nauczyciel>/<klasa>)')->defaults(array(
    'controller'=>'nauczyciele',
    'action'=>'klwyp',
        )
);
Route::set('nl_przwyp', '(nauczyciele/przwyp/<nauczyciel>/<przedmiot>)')->defaults(array(
    'controller'=>'nauczyciele',
    'action'=>'przwyp',
        )
);
Route::set('pr_nlwyp', '(przedmioty/wypisz/<przedmiot>/<nauczyciel>)')->defaults(array(
    'controller'=>'przedmioty',
    'action'=>'wypisz',
        )
);
Route::set('plan_grpdel', '(plan/grpdel/<dzien>/<lekcja>/<klasa>/<grupa>)')->defaults(array(
    'controller'=>'plan',
    'action'=>'grpdel',
        )
);