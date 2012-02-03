<?php

class Core_Tools {

    protected $dbhandle;

    public static function ShowError($message, $code='---') {
	$errorPage = View::factory('_cterror');
	$errorPage->set('message', $message);
	$errorPage->set('code', $code);
	return $errorPage->render();
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
	    $fh = fopen(APPROOTPATH . DS . 'resources/timetables/' . $klasa . '.xml', 'w');
	    fputs($fh, $xml->flush());
	    fclose($fh);
	}
    }

}