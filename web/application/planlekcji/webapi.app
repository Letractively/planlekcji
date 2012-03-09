<?php

/**
 * Definiuje API systemu IPL
 * 
 * @package ipl\api
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
    $res = Isf2::Connect()->Select('uzytkownicy')
                    ->Where(array('webapi_token' => $token))
                    ->Execute()->fetchAll();
    if (count($res) != 1) {
        return false;
    } else {
        return $res;
    }
}

//////////////////////////// AUTHENTICATION METHODS

/**
 * Logowanie uzytkownika
 *
 * @param string $username nazwa uzytkownika
 * @param string $password haslo
 * @param string $token token logowania
 * @return string token lub auth:failed 
 */
function doLogin($username, $password, $token) {
    if (!defined('ldap_enable') || ldap_enable != "true") {
        $msg = App_Auth::doUserLogin($username, $password, $token);
    } else {
        $msg = App_Auth::doLDAPLogin($username, $password);
    }
    return $msg;
}

/**
 * Wylogowuje
 *
 * @param string $token token sesji
 * @return string auth:logout 
 */
function doLogout($token) {
    Isf2::Connect()->Update('uzytkownicy', array(
                'webapi_timestamp' => '', 'webapi_token' => ''
            ))
            ->Where(array('webapi_token' => $token))
            ->Execute();
    return 'auth:logout';
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
        return date('Y-m-d H:i:s', $r[0]['webapi_timestamp']);
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

    $db = Isf2::Connect();

    $oldm = md5('plan' . sha1('lekcji' . $old));
    $newm = md5('plan' . sha1('lekcji' . $new));

    $old_user = $db->Select('uzytkownicy', array('haslo'))
                    ->Where(array('haslo' => $oldm))
                    ->Execute()->FetchAll();

    if (count($old_user) != 1) {
        return 'auth:failed';
    } else {
        $db->Update('uzytkownicy', array('haslo' => $newm))
                ->Where(array('webapi_token' => $token, 'haslo' => $oldm))
                ->Execute();
        return 'auth:chpasswd';
    }
}

/**
 * Odnawia token
 *
 * @param string $token token sesji
 * @return string auth:renew 
 */
function doRenewToken($token) {
    $timestamp = (time() + 3600 * 3);
    Isf2::Connect()->Update('uzytkownicy', array('webapi_timestamp' => $timestamp))
            ->Where(array('webapi_token' => $token))
            ->Execute();
    return 'auth:renew';
}

//////////////////////////// CLASSES MANAGMENT

/**
 * Pobiera klasy w systemie
 *
 * @param token $token Token sesji
 * @return mixed 
 */
function doGetClasses($token) {
    if (!checkauth($token)) {
        return 'auth:failed';
    } else {
        return Isf2::Connect()->Select('klasy')
                        ->OrderBy(array('klasa' => 'asc'))
                        ->Execute()->fetchAll();
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
        $exist = Isf2::Connect()->Select('klasy')
                        ->Where(array('klasa' => $class))
                        ->Execute()->fetchAll();
        if (count($exist) != 0) {
            return 'class:exists';
        } else {
            if (preg_match('/([.!@#$;%^&*()_+|])/i', $class)) {
                return 'class:nameerror';
            } else {
                Isf2::Connect()->Insert('klasy', array('klasa' => $class))
                        ->Execute();
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
        $exist = Isf2::Connect()->Select('klasy')
                        ->Where(array('klasa' => $class))
                        ->Execute()->FetchAll();
        if (count($exist) == 0) {
            return 'class:notexists';
        } else {
            Isf2::Connect()->Delete('klasy')
                    ->Where(array('klasa' => $class))
                    ->Execute();
            return 'class:deleted';
        }
    }
}

//////////////////////////// CLASSROOM MANAGMENT

/**
 * Pobiera klasy w systemie
 *
 * @param token $token Token sesji
 * @return mixed 
 */
function doGetClassrooms($token) {
    if (!checkauth($token)) {
        return 'auth:failed';
    } else {
        return Isf2::Connect()->Select('sale')
                        ->OrderBy(array('sala' => 'asc'))
                        ->Execute()->fetchAll();
    }
}

/**
 * Dodaje sale
 *
 * @param string $token token
 * @param string $class nazwa sali
 * @return type  class:exists, class:added, auth:failed
 */
function doAddClassroom($token, $classroom) {
    if (!checkauth($token)) {
        return 'auth:failed';
    } else {
        $exist = Isf2::Connect()->Select('sale')
                        ->Where(array('sala' => $class))
                        ->Execute()->fetchAll();
        if (count($exist) != 0) {
            return 'class:exists';
        } else {
            if (preg_match('/([.!@#$;%^&*()_+|])/i', $class)) {
                return 'class:nameerror';
            } else {
                Isf2::Connect()->Insert('sale', array('sala' => $class))
                        ->Execute();
                return 'class:added';
            }
        }
    }
}

/**
 * Usuwa sale
 *
 * @param string $token
 * @param string $classroom
 * @return string classroom:removed auth:failed
 */
function doDelClassroom($token, $classroom){
    if (!checkauth($token)) {
        return 'auth:failed';
    } else {
        Isf2::Connect()->Delete('sale')
                ->Where(array('sala'=>$classroom))
                ->Execute();
        Isf2::Connect()->Delete('przedmiot_sale')
                ->Where(array('sala'=>$classroom))
                ->Execute();
        return 'classroom:removed';
    }
}

//////////////////////////// SYSTEM MANAGMENT

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
        $res = Isf2::Connect()->Select('rejestr')
                        ->Where(array('opcja' => $key))
                        ->Execute()->fetchAll();
        if (count($res) == 0) {
            return 'fetch:failed';
        } else {
            return $res[0]['wartosc'];
        }
    }
}

/**
 * Czysci system
 *
 * @param string $token Token sesji
 * @param string $param Tryb
 * @return string 
 */
function doSystemClean($token, $param) {
    if (!checkauth($token)) {
        return 'auth:failed';
    } else {
        /*
          $isf = new Kohana_Isf();
          $isf->Connect(APP_DBSYS);
          $isf->DbDelete('planlek', 'klasa like \'%\'');
          $isf->DbDelete('plan_grupy', 'klasa like \'%\'');
          $isf->DbDelete('zast_id', 'zast_id like \'%\'');
          $isf->DbDelete('zastepstwa', 'zast_id like \'%\'');
          $isf->DbUpdate('rejestr', array('wartosc' => '1'), 'opcja=\'edycja_danych\'');
         */
        $db = Isf2::Connect();
        $db->Delete('planlek')->Execute();
        $db->Delete('plan_grupy')->Execute();
        $db->Delete('zastepstwa')->Execute();
        $db->Delete('zast_id')->Execute();
        $db->Update('rejestr', array('wartosc' => '1'))
                ->Where(array('opcja' => 'edycja_danych'))
                ->Execute();

        if ($param == 'permament') {
            $db->Delete('klasy')->Execute();
            $db->Delete('lek_godziny')->Execute();
            $db->Delete('nauczyciele')->Execute();
            $db->Delete('nl_klasy')->Execute();
            $db->Delete('nl_przedm')->Execute();
            $db->Delete('przedmiot_sale')->Execute();
            $db->Delete('przedmioty')->Execute();
            $db->Delete('sale')->Execute();
            $db->Update('rejestr', array('wartosc' => '1'))
                    ->Where(array('opcja' => 'ilosc_godzin_lek'))
                    ->Execute();
        }
        insert_log('webapi.sysapi', 'Uzytkownik dokonal ' . (($param == 'permament') ? 'kompletnego' : 'czesciowego') . ' czyszczenia poprzez IPL-CLI');
        return 'sys:cleaned';
    }
}