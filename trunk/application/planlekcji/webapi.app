<?php

/**
 * 
 */

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
    $db->DbConnect();
    $res = $db->DbSelect('uzytkownicy', array('*'), 'where webapi_token=\'' . $token . '\'');
    if (count($res) != 1) {
        return false;
    } else {
        return $res;
    }
}

/**
 * Logowanie uzytkownika root
 *
 * @param string $username nazwa uzytkownika
 * @param string $password haslo
 * @param string $token token logowania
 * @return string token lub auth:failed 
 */
function doLogin($username, $password, $token) {
    $db = new Kohana_Isf();
    $db->DbConnect();
    $haslo = md5('plan' . sha1('lekcji' . $password));
    $uid = $db->DbSelect('uzytkownicy', array('*'), 'where login=\'' . $username . '\' and haslo=\'' . $haslo . '\'');
    $tok = $db->DbSelect('tokeny', array('*'), 'where login=\'' . $username . '\' and token=\'' . md5('plan' . $token) . '\'');
    if (count($uid) != 1 || count($tok) != 1) {
        return 'auth:failed';
    } else {
        if ($uid[1]['webapi_timestamp'] >= time()) {
            return $uid[1]['webapi_token'];
        } else {
            $timestamp = (time() + 3600 * 3);
            $token = gentoken($uid[1]['login']);
            $arr = array(
                'webapi_token' => $token,
                'webapi_timestamp' => $timestamp
            );
            $db->DbUpdate('uzytkownicy', $arr, 'login=\'' . $username . '\'');
            return $token;
        }
    }
}

/**
 * Logowanie zwyklego uzytkownika
 *
 * @param string $username uzytkownik
 * @param string $password haslo
 * @param string $token token logowania
 * @return string token lub auth:failed 
 */
function doUserLogin($username, $password, $token) {
    $db = new Kohana_Isf();
    $db->DbConnect();
    $token = md5('plan' . $token);
    $haslo = md5('plan' . sha1('lekcji' . $password));
    $uid = $db->DbSelect('uzytkownicy', array('*'), 'where login=\'' . $username . '\'');
    if (count($uid) != 1) {
        return 'auth:failed';
    } else {
        if ($uid[1]['ilosc_prob'] >= 3) {
            return 'auth:locked';
            exit;
        }
        if ($uid[1]['haslo'] != $haslo) {
            $nr = $uid[1]['ilosc_prob'] + 1;
            $db->DbUpdate('uzytkownicy', array('ilosc_prob' => $nr), 'login=\'' . $username . '\'');
            return 'auth:failed';
            exit;
        }
        $tokena = $db->DbSelect('tokeny', array('*'), 'where login=\'' . $username . '\' and token=\'' . $token . '\'');
        if (count($tokena) == 0) {
            $nr = $uid[1]['ilosc_prob'] + 1;
            $db->DbUpdate('uzytkownicy', array('ilosc_prob' => $nr), 'login=\'' . $username . '\'');
            return 'auth:failed';
            exit;
        } else {
            if ($uid[1]['webapi_timestamp'] >= time()) {
                return $uid[1]['webapi_token'];
            } else {
                $timestamp = (time() + 3600 * 3);
                $token_x = gentoken($uid[1]['login']);
                $db->DbDelete('tokeny', 'login=\'' . $username . '\' and token=\'' . $token . '\'');
                $arr = array(
                    'ilosc_prob' => '0',
                    'webapi_token' => $token_x,
                    'webapi_timestamp' => $timestamp
                );
                $db->DbUpdate('uzytkownicy', $arr, 'login=\'' . $username . '\'');
                return $token_x;
            }
        }
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
        $db->DbConnect();
        $res = $Db->DbSelect('rejestr', array('*'), 'where opcja=\'' . $key . '\'');
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
    $db->DbConnect();
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
    $db->DbConnect();
    $db->DbUpdate('uzytkownicy', array('webapi_timestamp' => '', 'webapi_token' => ''), 'webapi_token=\'' . $token . '\'');
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
        $db->DbConnect();
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
 * Zmienia haslo uzytkownika
 *
 * @param string $token token
 * @param string $old stare haslo
 * @param string $new nowe haslo
 * @return string auth:chpasswd, auth:failed 
 */
function doChangePass($token, $old, $new) {
    $db = new Kohana_Isf();
    $db->DbConnect();
    $oldm = md5('plan' . sha1('lekcji' . $old));
    $newm = md5('plan' . sha1('lekcji' . $new));
    if ($db->DbUpdate('uzytkownicy', array('haslo' => $newm), 'webapi_token=\'' . $token . '\' and haslo=\'' . $oldm . '\'')):
        return 'auth:chpasswd';
    else:
        return 'auth:failed';
    endif;
}