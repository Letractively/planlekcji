<?php

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
        $isf->DbConnect();
        $a = $isf->DbSelect('rejestr', array('*'), 'where opcja=\'edycja_danych\'');
        return $a[1]['wartosc'];
    }

    public static function getRegistryKey($key) {
        $isf = new Kohana_Isf();
        $isf->DbConnect();
        $a = $isf->DbSelect('rejestr', array('*'), 'where opcja=\'' . $key . '\'');
        if (count($a) == 0) {
            return 'registry:key not exists';
        } else {
            return $a[1]['wartosc'];
        }
    }

}