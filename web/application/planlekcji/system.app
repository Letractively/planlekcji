<?php

class Core_Tools {

    protected $dbhandle;

    public static function ShowError($message, $code='---', $self_doc=false) {

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
	} else {
	    echo $errorPage;
	}
	exit;
    }

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
	if (!extension_loaded('pdo_sqlite') || !extension_loaded('pdo_pgsql')) {
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

    public static function parseCfgFile() {
	$cfg = parse_ini_file(APP_ROOT . DS . 'resources' . DS . 'config.ini', true);
	foreach ($cfg as $group => $values) {
	    foreach ($values as $name => $value) {
		define($group . '_' . $name, $value);
	    }
	}
    }

    public static function is_mobile() {

	$user_agent = $_SERVER['HTTP_USER_AGENT'];

	$mobile_agents = Array(
	    "240x320",
	    "acer",
	    "acoon",
	    "acs-",
	    "abacho",
	    "ahong",
	    "airness",
	    "alcatel",
	    "amoi",
	    "android",
	    "anywhereyougo.com",
	    "applewebkit/525",
	    "applewebkit/532",
	    "asus",
	    "audio",
	    "au-mic",
	    "avantogo",
	    "becker",
	    "benq",
	    "bilbo",
	    "bird",
	    "blackberry",
	    "blazer",
	    "bleu",
	    "cdm-",
	    "compal",
	    "coolpad",
	    "danger",
	    "dbtel",
	    "dopod",
	    "elaine",
	    "eric",
	    "etouch",
	    "fly ",
	    "fly_",
	    "fly-",
	    "go.web",
	    "goodaccess",
	    "gradiente",
	    "grundig",
	    "haier",
	    "hedy",
	    "hitachi",
	    "htc",
	    "huawei",
	    "hutchison",
	    "inno",
	    "ipad",
	    "ipaq",
	    "ipod",
	    "jbrowser",
	    "kddi",
	    "kgt",
	    "kwc",
	    "lenovo",
	    "lg ",
	    "lg2",
	    "lg3",
	    "lg4",
	    "lg5",
	    "lg7",
	    "lg8",
	    "lg9",
	    "lg-",
	    "lge-",
	    "lge9",
	    "longcos",
	    "maemo",
	    "mercator",
	    "meridian",
	    "micromax",
	    "midp",
	    "mini",
	    "mitsu",
	    "mmm",
	    "mmp",
	    "mobi",
	    "mot-",
	    "moto",
	    "nec-",
	    "netfront",
	    "newgen",
	    "nexian",
	    "nf-browser",
	    "nintendo",
	    "nitro",
	    "nokia",
	    "nook",
	    "novarra",
	    "obigo",
	    "palm",
	    "panasonic",
	    "pantech",
	    "philips",
	    "phone",
	    "pg-",
	    "playstation",
	    "pocket",
	    "pt-",
	    "qc-",
	    "qtek",
	    "rover",
	    "sagem",
	    "sama",
	    "samu",
	    "sanyo",
	    "samsung",
	    "sch-",
	    "scooter",
	    "sec-",
	    "sendo",
	    "sgh-",
	    "sharp",
	    "siemens",
	    "sie-",
	    "softbank",
	    "sony",
	    "spice",
	    "sprint",
	    "spv",
	    "symbian",
	    "tablet",
	    "talkabout",
	    "tcl-",
	    "teleca",
	    "telit",
	    "tianyu",
	    "tim-",
	    "toshiba",
	    "tsm",
	    "up.browser",
	    "utec",
	    "utstar",
	    "verykool",
	    "virgin",
	    "vk-",
	    "voda",
	    "voxtel",
	    "vx",
	    "wap",
	    "wellco",
	    "wig browser",
	    "wii",
	    "windows ce",
	    "wireless",
	    "xda",
	    "xde",
	    "zte"
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

    public function Core_Tools() {
	$this->dbhandle = Kohana_Isf::factory();
	$this->dbhandle->Connect(APP_DBSYS);
    }

    public function getClasses() {
	$result = $this->dbhandle->DbSelect('klasy', array('*'), 'order by klasa asc');
	return $result;
    }

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

class MPZ {

    protected $CT;
    protected $DB;

    public function __construct() {
	$this->CT = new Core_Tools();
	$this->DB = Kohana_Isf::factory();
	$this->DB->Connect(APP_DBSYS);
    }

    public function getLessonHour($lesson) {
	$res = $this->DB->DbSelect('lek_godziny', array('godzina'), 'where lekcja=\'' . $lesson . '\'');
	if (count($res) == 0) {
	    $return = 'fetched:none';
	} else {
	    $return = $res[0]['godzina'];
	}

	return $return;
    }

    public function getLesson($class, $day, $lesson) {
	$single = $this->CT->getSingleLesson($class, $day, $lesson);
	if ($single == 'fetched:none') {
	    return $this->CT->getGroupLesson($class, $day, $lesson);
	} else {
	    return array('t_single' => $single);
	}
    }

}

class App_Globals {

    public static function getThemes() {
	$handle = opendir(DOCROOT . 'lib' . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR . 'themes');
	$themes = array();
	while (false !== ($file = readdir($handle))) {
	    if ($file != '.' xor $file != '..' xor $file != '.svn') {
		$file = str_replace('.css', '', $file);
		$themes[] = $file;
	    }
	}
	return $themes;
    }

    public static function getSysLv() {
	$isf = new Kohana_Isf();
	$isf->Connect(APP_DBSYS);
	$a = $isf->DbSelect('rejestr', array('*'), 'where opcja=\'edycja_danych\'');
	return $a[0]['wartosc'];
    }

    public static function getRegistryKey($key) {
	$a = Isf2::Connect()->Select('rejestr')
			->Where(array('opcja' => $key))
			->Execute()->fetchAll();
	if (count($a) == 0) {
	    return 'registry:key not exists';
	} else {
	    return $a[0]['wartosc'];
	}
    }

    public static function getTeacherSym($teacher) {
	$isf = new Kohana_Isf();
	$isf->Connect(APP_DBSYS);
	$res = $isf->DbSelect('nauczyciele', array('skrot'), 'where imie_naz=\'' . $teacher . '\'');
	if (count($res) == 0) {
	    $return = 'fetched:none';
	} else {
	    $return = $res[0]['skrot'];
	}
	return $return;
    }

    public static function getTeacherName($sym) {
	$isf = new Kohana_Isf();
	$isf->Connect(APP_DBSYS);
	$res = $isf->DbSelect('nauczyciele', array('imie_naz'), 'where skrot=\'' . $sym . '\'');
	if (count($res) == 0) {
	    $return = 'fetched:none';
	} else {
	    $return = $res[0]['imie_naz'];
	}
	return $return;
    }

    public static function writeXmlTimetables() {
	$CTools = new Core_Tools();

	$klasy = $CTools->getClasses();
	$dni = array('Poniedziałek', 'Wtorek', 'Środa', 'Czwartek', 'Piątek');
	$lekcje = App_Globals::getRegistryKey('ilosc_godzin_lek');

	foreach ($klasy as $k_rowid => $k_rowcol) {
	    $klasa = $k_rowcol['klasa'];
	    $xml = new XMLWriter();
	    $xml->openMemory();
	    $xml->setIndent(4);
	    $xml->startDocument('1.0', 'UTF-8');
	    $xml->writeComment('Wygenerowano aplikacją Internetowy Plan Lekcji');
	    $xml->startElement('timetable');
	    $xml->startAttribute('version');
	    $xml->text(App_Globals::getRegistryKey('app_ver'));
	    $xml->endAttribute();
	    $xml->startAttribute('class');
	    $xml->text($klasa);
	    $xml->endAttribute();
	    foreach ($dni as $dzien) {
		$xml->startElement('day');
		$xml->startAttribute('name');
		$xml->text($dzien);
		$xml->endAttribute();
		for ($l = 1; $l <= $lekcje; $l++) {
		    $xml->startElement('lesson');
		    $xml->startAttribute('l');
		    $xml->text($l);
		    $xml->endAttribute();

		    if (($lz = $CTools->getSingleLesson($klasa, $dzien, $l)) != 'fetched:none') {
			$xml->startAttribute('s');
			$xml->text($lz['przedmiot']);
			$xml->endAttribute();
			$xml->startAttribute('t');
			$xml->text($lz['skrot']);
			$xml->endAttribute();
			$xml->startAttribute('c');
			$xml->text($lz['sala']);
			$xml->endAttribute();
		    } else if (($lg = $CTools->getGroupLesson($klasa, $dzien, $l)) != 'fetched:none') {
			foreach ($lg as $grupa => $zaj) {
			    $xml->startElement('group');
			    $xml->startAttribute('g');
			    $xml->text($grupa);

			    $xml->startAttribute('s');
			    $xml->text($zaj['przedmiot']);
			    $xml->endAttribute();
			    $xml->startAttribute('t');
			    $xml->text($zaj['skrot']);
			    $xml->endAttribute();
			    $xml->startAttribute('c');
			    $xml->text($zaj['sala']);
			    $xml->endAttribute();

			    $xml->endElement();
			}
		    }

		    $xml->endElement();
		}
		$xml->endElement();
	    }
	    $xml->endElement();
	    $xml->endDocument();
	    $fh = fopen(APP_ROOT . DS . 'resources/timetables/' . $klasa . '.xml', 'w');
	    fputs($fh, $xml->flush());
	    fclose($fh);
	}
    }

}