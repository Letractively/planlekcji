<?php

/**
 * Dodaje nowa wiadomosc do loga systemowego
 *
 * @param string $modul
 * @param string $wiadomosc 
 */
function insert_log($modul, $wiadomosc) {
    $db = new Kohana_Isf();
    $db->Connect(APP_DBSYS);
    $db->DbInsert('log', array('data' => date('d.m.Y H:i:s'), 'modul' => $modul, 'wiadomosc' => $wiadomosc));
}

/**
 * Zwraca token sesyjny
 *
 * @param string $uid login uzytkownika
 * @return string token
 */
function gentoken($uid) {
    return md5(sha1('1s#plan!!002' . $uid . 'r98mMjs7^A2b' . rand(1000, 9999)) . time());
}

/**
 * Sprawdza czy uzytkownik posiada token
 *
 * @param string $token token sesji
 * @return mixed true lub okiekt tabeli db 
 */
function checkauth($token) {
    $db = new Kohana_Isf();
    $db->Connect(APP_DBSYS);
    $res = $db->DbSelect('uzytkownicy', array('*'), 'where webapi_token=\'' . $token . '\'');
    if (count($res) != 1) {
	return false;
    } else {
	return $res;
    }
}

/**
 * Logowanie uzytkownika
 *
 * @param string $username nazwa uzytkownika
 * @param string $password haslo
 * @param string $token token logowania
 * @return string token lub auth:failed 
 */
function doLogin($username, $password, $token) {

    $db = new Kohana_Isf();
    $db->Connect(APP_DBSYS);

    $token = md5('plan' . $token);
    $haslo = md5('plan' . sha1('lekcji' . $password));
    $uid = $db->DbSelect('uzytkownicy', array('*'), 'where login=\'' . $username . '\'');
    $tokena = $db->DbSelect('tokeny', array('*'), 'where login=\'' . $username . '\' and token=\'' . $token . '\'');
    if (count($uid) != 1) {
	return 'auth:failed';
    }
    if ($uid[1]['ilosc_prob'] >= 3) {
	return 'auth:locked';
	exit;
    }
    if ($uid[1]['haslo'] != $haslo) {
	if ($username != 'root') {
	    $nr = $uid[1]['ilosc_prob'] + 1;
	    $db->DbUpdate('uzytkownicy', array('ilosc_prob' => $nr), 'login=\'' . $username . '\'');
	}
	return 'auth:failed';
	exit;
    }
    if (count($tokena) == 0) {
	if ($username != 'root') {
	    $nr = $uid[1]['ilosc_prob'] + 1;
	    $db->DbUpdate('uzytkownicy', array('ilosc_prob' => $nr), 'login=\'' . $username . '\'');
	}
	return 'auth:failed';
	exit;
    } else {
	$timestamp = (time() + 3600 * 3);
	$token_x = gentoken($uid[1]['login']);
	if ($username != 'root') {
	    $db->DbDelete('tokeny', 'login=\'' . $username . '\' and token=\'' . $token . '\'');
	}
	$arr = array(
	    'ilosc_prob' => '0',
	    'webapi_token' => $token_x,
	    'webapi_timestamp' => $timestamp
	);
	$db->DbUpdate('uzytkownicy', $arr, 'login=\'' . $username . '\'');

	return $token_x;
    }
}

/**
 * Zwraca date wygasniecia tokena sesji
 *
 * @param string $token token sesji
 * @return string auth:failed lub data wygasniecia tokena
 */
function doShowAuthTime($token) {
    $r = checkauth($token);
    if ($r == false) {
	return 'auth:failed';
    } else {
	return date('Y-m-d H:i:s', $r[1]['webapi_timestamp']);
    }
}

/**
 * Pobiera klucz rejestru systemowego
 *
 * @param string $token token zalogowanego uzytkownika
 * @param string $key nazwa klucza
 * @return string fetch:failed lub wartosc klucza 
 */
function doGetRegistryKey($token, $key) {
    if (!checkauth($token)) {
	return 'auth:failed';
    } else {
	$db = new Kohana_Isf();
	$db->Connect(APP_DBSYS);
	$res = $db->DbSelect('rejestr', array('*'), 'where opcja=\'' . $key . '\'');
	if (count($res) == 0) {
	    return 'fetch:failed';
	} else {
	    return $res[1]['wartosc'];
	}
    }
}

/**
 * Odnawia token
 *
 * @param string $token token sesji
 * @return string auth:renew 
 */
function doRenewToken($token) {
    $db = new Kohana_Isf();
    $db->Connect(APP_DBSYS);
    $timestamp = (time() + 3600 * 3);
    $db->DbUpdate('uzytkownicy', array('webapi_timestamp' => $timestamp), 'webapi_token=\'' . $token . '\'');
    return 'auth:renew';
}

/**
 * Wylogowuje
 *
 * @param string $token token sesji
 * @return string auth:logout 
 */
function doLogout($token) {
    $db = new Kohana_Isf();
    $db->Connect(APP_DBSYS);
    $db->DbUpdate('uzytkownicy', array('webapi_timestamp' => '', 'webapi_token' => ''), 'webapi_token=\'' . $token . '\'');
    insert_log('webapi.auth', 'Uzytkownik ' . $username . ' wylogowal sie poprzez IPL-CLI');
    return 'auth:logout';
}

/**
 * Dodaje klase
 *
 * @param string $token token
 * @param string $class nazwa klasy
 * @return type  class:exists, class:added, auth:failed
 */
function doAddClassroom($token, $class) {
    if (!checkauth($token)) {
	return 'auth:failed';
    } else {
	$db = new Kohana_Isf();
	$db->Connect(APP_DBSYS);
	if (count($db->DbSelect('sale', array('*'), 'where sala=\'' . $class . '\'')) != 0) {
	    return 'class:exists';
	} else {
	    if (preg_match('/([.!@#$;%^&*()_+|])/i', $class)) {
		return 'class:nameerror';
	    } else {
		$db->DbInsert('sale', array('sala' => $class));
		return 'class:added';
	    }
	}
    }
}

/**
 * Dodaje klase
 *
 * @param string $token Token sesji
 * @param string  $class Klasa
 * @return string
 */
function doAddClass($token, $class) {
    if (!checkauth($token)) {
	return 'auth:failed';
    } else {
	$db = new Kohana_Isf();
	$db->Connect(APP_DBSYS);
	if (count($db->DbSelect('klasy', array('*'), 'where klasa=\'' . $class . '\'')) != 0) {
	    return 'class:exists';
	} else {
	    if (preg_match('/([.!@#$;%^&*()_+|])/i', $class)) {
		return 'class:nameerror';
	    } else {
		$db->DbInsert('klasy', array('klasa' => $class));
		return 'class:added';
	    }
	}
    }
}

/**
 * Usuwa klasę
 *
 * @param string $token
 * @param string $class
 * @return string 
 */
function doDelClass($token, $class) {
    if (!checkauth($token)) {
	return 'auth:failed';
    } else {
	$db = new Kohana_Isf();
	$db->Connect(APP_DBSYS);
	if (count($db->DbSelect('klasy', array('*'), 'where klasa=\'' . $class . '\'')) == 0) {
	    return 'class:notexists';
	} else {
	    $db->DbDelete('klasy', 'klasa=\'' . $class . '\'');
	    return 'class:deleted';
	}
    }
}

/**
 * Zmienia haslo uzytkownika
 *
 * @param string $token token
 * @param string $old stare haslo
 * @param string $new nowe haslo
 * @return string auth:chpasswd, auth:failed 
 */
function doChangePass($token, $old, $new) {
    $db = new Kohana_Isf();
    $db->Connect(APP_DBSYS);
    $oldm = md5('plan' . sha1('lekcji' . $old));
    $newm = md5('plan' . sha1('lekcji' . $new));
    if ($db->DbUpdate('uzytkownicy', array('haslo' => $newm), 'webapi_token=\'' . $token . '\' and haslo=\'' . $oldm . '\'')):
	return 'auth:chpasswd';
    else:
	return 'auth:failed';
    endif;
}

function doShowClasses($token) {
    if (!checkauth($token)) {
	return 'auth:failed';
    } else {
	$isf = new Kohana_Isf();
	$isf->Connect(APP_DBSYS);
	return $isf->DbSelect('klasy', array('klasa'), 'order by klasa asc');
    }
}

function doSysClean($token, $param) {
    if (!checkauth($token)) {
	return 'auth:failed';
    } else {
	$isf = new Kohana_Isf();
	$isf->Connect(APP_DBSYS);
	$isf->DbDelete('planlek', 'klasa like \'%\'');
	$isf->DbDelete('plan_grupy', 'klasa like \'%\'');
	$isf->DbDelete('zast_id', 'zast_id like \'%\'');
	$isf->DbDelete('zastepstwa', 'zast_id like \'%\'');
	$isf->DbUpdate('rejestr', array('wartosc' => '1'), 'opcja=\'edycja_danych\'');
	if ($param == 'permament') {
	    $isf->DbDelete('klasy', 'klasa like \'%\'');
	    $isf->DbDelete('lek_godziny', 'lekcja like \'%\'');
	    $isf->DbDelete('nauczyciele', 'imie_naz like \'%\'');
	    $isf->DbDelete('nl_klasy', 'klasa like \'%\'');
	    $isf->DbDelete('nl_przedm', 'przedmiot like \'%\'');
	    $isf->DbDelete('przedmiot_sale', 'sala like \'%\'');
	    $isf->DbDelete('przedmioty', 'przedmiot like \'%\'');
	    $isf->DbDelete('sale', 'sala like \'%\'');
	    $isf->DbUpdate('rejestr', array('wartosc' => '1'), 'opcja=\'ilosc_godzin_lek\'');
	}
	insert_log('webapi.sysapi', 'Uzytkownik ' . $username . ' dokonal ' . (($param == 'permament') ? 'kompletnego' : 'czesciowego') . ' czyszczenia poprzez IPL-CLI');
	return 'sys:cleaned';
    }
}