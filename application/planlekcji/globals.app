<?php

class App_Globals {

    public static function getThemes() {
        $themes = array(
            'domyslny',
            'zielony',
            'pomarancz',
            'zloty',
        );
        return $themes;
    }

    public static function getSysLv() {
        $isf = new Kohana_Isf();
        $isf->DbConnect();
        $a = $isf->DbSelect('rejestr', array('*'), 'where opcja=\'edycja_danych\'');
        return $a[1]['wartosc'];
    }

}