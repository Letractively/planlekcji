<?php

/**
 * Jadra IPL
 * 
 * @package ipl\core
 * @author Michal Bocian <mhl.bocian@gmail.com>
 * @license GNU GPL v3
 */

/**
 * Klasa odpowiedzialna za instalacje systemu
 */
class Core_Install {

    public $Isf;
    public $type;

    /**
     *
     * @param string $type Rodzaj polaczenia
     * @return Isf2
     */
    public function Connect($type) {
        $this->type = $type;
        switch ($type) {
            case 'sqlite':
                $this->Isf = Isf2::Connect('sqlite', null, true);
                return 'db:cpass';
                break;
            case 'pgsql':
                $this->Isf = Isf2::Connect('pgsql');
                return 'db:cpass';
                break;
            default:
                return 'db:cfailed';
                break;
        }
    }

    /**
     * Inicjalizacja bazy danych
     *
     * @param string $szkola Nazwa szkoly
     * @param string $ver Wersja systemu
     * @return mixed 
     */
    public function DbInit($szkola, $ver) {

        try {

            $this->Isf->CreateTable('przedmioty', array(
                'przedmiot' => 'text not null'
            ))->Execute();

            $this->Isf->CreateTable('sale', array(
                'sala' => 'text not null'
            ))->Execute();

            $this->Isf->CreateTable('przedmiot_sale', array(
                'przedmiot' => 'text not null',
                'sala' => 'text not null'
            ))->Execute();

            $this->Isf->CreateTable('klasy', array(
                'klasa' => 'text not null'
            ))->Execute();

            $this->Isf->CreateTable('nauczyciele', array(
                'imie_naz' => 'text not null',
                'skrot' => 'text not null'
            ))->Execute();

            $this->Isf->CreateTable('nl_przedm', array(
                'nauczyciel' => 'text not null',
                'przedmiot' => 'text not null'
            ))->Execute();

            $this->Isf->CreateTable('nl_klasy', array(
                'nauczyciel' => 'text not null',
                'klasa' => 'text not null'
            ))->Execute();

            $this->Isf->CreateTable('rejestr', array(
                'opcja' => 'text not null',
                'wartosc' => 'text'
            ))->Execute();

            $this->Isf->CreateTable('planlek', array(
                'dzien' => 'text',
                'klasa' => 'text',
                'lekcja' => 'text',
                'przedmiot' => 'text',
                'sala' => 'text',
                'nauczyciel' => 'text',
                'skrot' => 'text'
            ))->Execute();

            $this->Isf->CreateTable('uzytkownicy', array(
                'uid' => 'numeric not null',
                'login' => 'text not null',
                'haslo' => 'text not null',
                'webapi_token' => 'text',
                'webapi_timestamp' => 'text',
                'ilosc_prob' => 'numeric'
            ))->Execute();

            $this->Isf->CreateTable('log', array(
                'id' => 'numeric not null',
                'data' => 'text not null',
                'modul' => 'text not null',
                'wiadomosc' => 'text',
            ))->Execute();

            $this->Isf->CreateTable('tokeny', array(
                'login' => 'text',
                'token' => 'text',
            ))->Execute();

            $this->Isf->CreateTable('plan_grupy', array(
                'dzien' => 'text',
                'lekcja' => 'text',
                'klasa' => 'text',
                'grupa' => 'text',
                'przedmiot' => 'text',
                'nauczyciel' => 'text',
                'skrot' => 'text',
                'sala' => 'text'
            ))->Execute();

            if ($this->type == 'sqlite') {
                $this->Isf->CreateTable('zast_id', array(
                    'zast_id' => 'integer primary key autoincrement',
                    'dzien' => 'text',
                    'za_nl' => 'text',
                    'info' => 'text',
                ))->Execute();
            } else {
                $this->Isf->CreateTable('zast_id', array(
                    'zast_id' => 'serial',
                    'dzien' => 'text',
                    'za_nl' => 'text',
                    'info' => 'text',
                ))->Execute();
            }

            $this->Isf->CreateTable('zastepstwa', array(
                'zast_id' => 'text',
                'lekcja' => 'text',
                'przedmiot' => 'text',
                'nauczyciel' => 'text',
                'sala' => 'text',
            ))->Execute();

            $this->Isf->CreateTable('lek_godziny', array(
                'lekcja' => 'text',
                'godzina' => 'text',
                'dl_prz' => 'text'
            ))->Execute();

            $this->Isf->Insert('rejestr', array(
                'opcja' => 'edycja_danych',
                'wartosc' => '1'
            ))->Execute();

            $this->Isf->Insert('rejestr', array(
                'opcja' => 'ilosc_godzin_lek',
                'wartosc' => '1'
            ))->Execute();

            $this->Isf->Insert('rejestr', array(
                'opcja' => 'dlugosc_lekcji',
                'wartosc' => '45'
            ))->Execute();

            $this->Isf->Insert('rejestr', array(
                'opcja' => 'nazwa_szkoly',
                'wartosc' => $szkola
            ))->Execute();

            $this->Isf->Insert('rejestr', array(
                'opcja' => 'index_text',
                'wartosc' => '<h1>Witaj w Planie Lekcji ' . $ver . '</h1><p>Dziękujemy za skorzystanie z systemu Plan Lekcji</p>
                <p>Proszę zmienić hasła do panelu administracyjnego oraz treść tej strony w bocznym panelu użytkownika.</p>'
            ))->Execute();

            $this->Isf->Insert('rejestr', array(
                'opcja' => 'ilosc_grup',
                'wartosc' => '0'
            ))->Execute();

            $this->Isf->Insert('rejestr', array(
                'opcja' => 'godz_rozp_zaj',
                'wartosc' => '08:00'
            ))->Execute();

            $this->Isf->Insert('rejestr', array(
                'opcja' => 'installed',
                'wartosc' => '1'
            ))->Execute();

            $this->Isf->Insert('rejestr', array(
                'opcja' => 'app_ver',
                'wartosc' => $ver
            ))->Execute();

            $this->Isf->Insert('rejestr', array(
                'opcja' => 'randtoken_version',
                'wartosc' => $ver
            ))->Execute();

            $pass = substr(md5(@date('Y:m:d')), 0, 8);
            $pass = rand(1, 100) . $pass;

            $this->Isf->Insert('uzytkownicy', array(
                'uid' => 1,
                'login' => 'root',
                'haslo' => md5('plan' . sha1('lekcji' . $pass)),
            ))->Execute();

            $token = substr(md5(time() . 'plan'), 0, 6);

            $this->Isf->Insert('tokeny', array('login' => 'root', 'token' => md5('plan' . $token)))->Execute();

            return array('pass' => $pass, 'token' => $token);
        } catch (Exception $e) {
            echo $e->getMessage();
            exit;
        }
    }

}

/**
 * Podstawowe API IPL
 * 
 * @package ipl\core
 */
class Core_Tools {

    protected $dbhandle;

    /**
     * Konstruktor klasy
     */
    public function __construct() {
        $this->dbhandle = Kohana_Isf::factory();
        $this->dbhandle->Connect(APP_DBSYS);
    }

    /**
     * Wyswietla strone z bledami
     *
     * @param string $message Tresc bledu
     * @param string $code Kod bledu
     * @param bool $self_doc Wyswietlic osobna strone
     */
    public static function ShowError($message, $code = '---', $self_doc = false, $return = false) {

        $errorPage = file_get_contents(APPPATH . 'error_page.html');
        $errorPage = str_replace('{{message}}', $message, $errorPage);
        $errorPage = str_replace('{{code}}', $code, $errorPage);
        if (!defined('global_app_path')) {
            $r = $_SERVER['REQUEST_URI'];
            $r = str_replace('index.php', '', $r);
            $r = str_replace('install.php', '', $r);
            $r = str_replace('?err', '', $r);
            $r = str_replace('?reinstall', '', $r);
            $errorPage = str_replace('{{HTTP_PATH}}', $r, $errorPage);
        } else {
            $errorPage = str_replace('{{HTTP_PATH}}', global_app_path, $errorPage);
        }
        if ($self_doc) {
            header('Content-Type: text/html');
            $header = '<!DOCTYPE html><html><head><meta charset="UTF-8"/></head><body>';
            $footer = '</body></html>';
            $return = $header . $errorPage . $footer;
            echo $return;
        } else if (!$return) {
            echo $errorPage;
        } else {
            return $errorPage;
        }
        exit;
    }

    /**
     * Sprawdza czy system jest zainicjowany
     */
    public static function CheckInstalled() {
        $paths_err = '<p><ul>';
        $paths = array('../resources',
            '../resources/timetables',
            'application/logs',
            'application/cache');
        $valid_paths = true;
        foreach ($paths as $path) {
            if (!is_writable($path)) {
                $paths_err .= '<li>Katalog <b>' . realpath($path) . '</b> musi posiadać prawa zapisu</li>';
                $valid_paths = false;
            }
        }
        $paths_err .= '</ul></p>';
        if (!$valid_paths) {
            self::ShowError($paths_err, 'S001', true);
        }
        if (!extension_loaded('pdo_sqlite') && !extension_loaded('pdo_pgsql')) {
            $dbErrMessage = 'IPL: PDO_SQLITE or PDO_PGSQL extension enabled is required';
            self::ShowError($dbErrMessage, 'S002', true);
        }
        if (!file_exists(APP_ROOT . DS . 'resources' . DS . 'config.ini')) {
            throw new Exception('IPL: Ready to install', 501);
        } else {
            $cfg = parse_ini_file(APP_ROOT . DS . 'resources' . DS . 'config.ini', true);
            if (!isset($cfg['global'])
                    || !isset($cfg['global']['app_path'])
                    || !isset($cfg['global']['app_dbsys'])) {
                self::ShowError('IPL: Config file is corrupt. Please remove config.ini file and refresh', 502, true);
            }
            if ($cfg['global']['app_dbsys'] != 'sqlite') {
                if (!isset($cfg['dbconfig'])
                        || !isset($cfg['dbconfig']['host'])
                        || !isset($cfg['dbconfig']['user'])
                        || !isset($cfg['dbconfig']['password'])
                        || !isset($cfg['dbconfig']['dbname'])) {
                    self::ShowError('IPL: Database config in config.ini is corrupt.', 503, true);
                }
            }
            foreach ($cfg as $group => $values) {
                foreach ($values as $name => $value) {
                    define($group . '_' . $name, $value);
                }
            }
            try {
                Isf2::Connect()->Select('rejestr')
                        ->Where(array('opcja' => 'installed'))->Execute()->fetchAll();
            } catch (Exception $e) {
                self::ShowError('IPL: Database is corrupt.' . $e->getMessage(), 504, true);
            }
            throw new Exception('IPL: READY', 505);
        }
    }

    /**
     * Parsuje plik konfiguracyjny
     */
    public static function parseCfgFile() {
        $cfg = parse_ini_file(APP_ROOT . DS . 'resources' . DS . 'config.ini', true);
        foreach ($cfg as $group => $values) {
            foreach ($values as $name => $value) {
                define($group . '_' . $name, $value);
            }
        }
    }

    /**
     * Wykrywa przegladarke mobilna
     *
     * @return boolean 
     */
    public static function is_mobile() {

        $user_agent = $_SERVER['HTTP_USER_AGENT'];

        $mobile_agents = Array(
            '240x320', 'acer', 'acoon',
            'acs-', 'abacho', 'ahong',
            'airness', 'alcatel', 'amoi',
            'android', 'anywhereyougo.com', 'applewebkit/525',
            'applewebkit/532', 'asus', 'audio',
            'au-mic', 'avantogo', 'becker',
            'benq', 'bilbo', 'bird',
            'blackberry', 'blazer', 'bleu',
            'cdm-', 'compal', 'coolpad',
            'danger', 'dbtel', 'dopod',
            'elaine', 'eric', 'etouch',
            'fly ', 'fly_', 'fly-',
            'go.web', 'goodaccess', 'gradiente',
            'grundig', 'haier', 'hedy',
            'hitachi', 'htc', 'huawei',
            'hutchison', 'inno', 'ipad',
            'ipaq', 'ipod', 'jbrowser',
            'kddi', 'kgt', 'kwc',
            'lenovo', 'lg ', 'lg2',
            'lg3', 'lg4', 'lg5',
            'lg7', 'lg8', 'lg9',
            'lg-', 'lge-', 'lge9',
            'longcos', 'maemo', 'mercator',
            'meridian', 'micromax', 'midp',
            'mini', 'mitsu', 'mmm',
            'mmp', 'mobi', 'mot-',
            'moto', 'nec-',
            'netfront', 'newgen', 'nexian',
            'nf-browser', 'nintendo', 'nitro',
            'nokia', 'nook', 'novarra',
            'obigo', 'palm', 'panasonic',
            'pantech', 'philips', 'phone',
            'pg-', 'playstation', 'pocket',
            'pt-', 'qc-', 'qtek',
            'rover', 'sagem', 'sama',
            'samu', 'sanyo', 'samsung',
            'sch-', 'scooter', 'sec-',
            'sendo', 'sgh-', 'sharp',
            'siemens', 'sie-', 'softbank',
            'sony', 'spice', 'sprint',
            'spv', 'symbian', 'tablet',
            'talkabout', 'tcl-', 'teleca',
            'telit', 'tianyu', 'tim-',
            'toshiba', 'tsm', 'up.browser',
            'utec', 'utstar', 'verykool',
            'virgin', 'vk-', 'voda',
            'voxtel', 'vx', 'wap',
            'wellco', 'wig browser', 'wii',
            'windows ce', 'wireless', 'xda',
            'xde', 'zte'
        );

        $is_mobile = false;

        foreach ($mobile_agents as $device) {

            if (stristr($user_agent, $device)) {

                $is_mobile = true;

                break;
            }
        }

        return $is_mobile;
    }

    /**
     * Pobiera pojedyncza lekcje
     *
     * @param string $class Klasa
     * @param string $day Dzien tyogdnia
     * @param string $lesson Lekcja
     * @return mixed 
     */
    public function getSingleLesson($class, $day, $lesson) {
        $condition = 'where klasa=\'' . $class . '\' and dzien=\'' . $day . '\' and lekcja=\'' . $lesson . '\'';
        $cols = array(
            'dzien',
            'klasa',
            'lekcja',
            'przedmiot',
            'skrot',
            'sala',
        );
        $result = $this->dbhandle->DbSelect('planlek', $cols, $condition);
        if (count($result) == 0) {
            $return = 'fetched:none';
        } else {
            $return = array(
                'dzien' => $result[0]['dzien'],
                'lekcja' => $result[0]['lekcja'],
                'przedmiot' => $result[0]['przedmiot'],
                'skrot' => $result[0]['skrot'],
                'sala' => $result[0]['sala'],
            );
        }

        return $return;
    }

    /**
     * Pobiera lekcje grupowa
     *
     * @param string $class Klasa
     * @param string $day Dzien tygodnia
     * @param string $lesson Lekcja
     * @return mixed 
     */
    public function getGroupLesson($class, $day, $lesson) {
        $condition = 'where klasa=\'' . $class . '\' and dzien=\'' . $day . '\' and lekcja=\'' . $lesson . '\' order by grupa asc';
        $cols = array(
            'dzien',
            'klasa',
            'grupa',
            'lekcja',
            'przedmiot',
            'skrot',
            'sala',
        );
        $result = $this->dbhandle->DbSelect('plan_grupy', $cols, $condition);
        if (count($result) == 0) {
            $return = 'fetched:none';
        } else {
            foreach ($result as $rowid => $rowcol) {
                $return[$rowcol['grupa']] = array(
                    'dzien' => $rowcol['dzien'],
                    'lekcja' => $rowcol['lekcja'],
                    'przedmiot' => $rowcol['przedmiot'],
                    'skrot' => $rowcol['skrot'],
                    'sala' => $rowcol['sala'],
                );
            }
        }

        return $return;
    }

}

class Core_Classes_Managment {

    public $dbhandle;

    public function __construct() {
        $this->dbhandle = Isf2::Connect();
    }

    /**
     * Pobiera klasy
     *
     * @return array
     */
    public function getClasses() {
        $result = $this->dbhandle->Select('klasy')->OrderBy(array('klasa' => 'asc'))
                        ->Execute()->FetchAll();
        return $result;
    }

    public function delClass($class) {
        $this->dbhandle->Delete('klasy')->Where(array('klasa' => $class))->Execute();
    }

}

class Core_Teacher_Managment {

    public $dbhandle;

    public function __construct() {
        $this->dbhandle = Isf2::Connect();
    }

    public function getTeachers() {
        return $this->dbhandle->Select('nauczyciele', array('*'))
                        ->OrderBy(array('skrot' => 'asc'))
                        ->Execute()->fetchAll();
    }

}