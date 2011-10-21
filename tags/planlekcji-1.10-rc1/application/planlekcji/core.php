<?php

require_once 'modules/isf/classes/kohana/isf.php';

class Core_Administrator {
    
}

class Core_Install {

    public $Isf;
    public $PgSQLArgs = array();

    public function __construct($type=null) {
        $this->Isf = new \Kohana_Isf();
    }

    public function Connect($type) {
        switch ($type) {
            case 'sqlite':
                $this->Isf->DbConnect();
                return 'db:cpass';
                break;
            case 'pgsql':
                $this->Isf->DbConnect($this->PgSQLArgs);
                return 'db:cpass';
                break;
            default:
                return 'db:cfailed';
                break;
        }
    }

    public function DbInit($szkola, $ver) {
        $this->Isf->DbDelete('rejestr', 'opcja like \'%\'');
        $this->Isf->DbDelete('uzytkownicy', 'login like \'%\'');
        $this->Isf->DbDelete('tokeny', 'token like \'%\'');
        $this->Isf->DbTblCreate('przedmioty', array(
            'przedmiot' => 'text not null'
        ));

        $this->Isf->DbTblCreate('sale', array(
            'sala' => 'text not null'
        ));

        $this->Isf->DbTblCreate('przedmiot_sale', array(
            'przedmiot' => 'text not null',
            'sala' => 'text not null'
        ));

        $this->Isf->DbTblCreate('klasy', array(
            'klasa' => 'text not null'
        ));

        $this->Isf->DbTblCreate('nauczyciele', array(
            'imie_naz' => 'text not null',
            'skrot' => 'text not null'
        ));

        $this->Isf->DbTblCreate('nl_przedm', array(
            'nauczyciel' => 'text not null',
            'przedmiot' => 'text not null'
        ));

        $this->Isf->DbTblCreate('nl_klasy', array(
            'nauczyciel' => 'text not null',
            'klasa' => 'text not null'
        ));

        $this->Isf->DbTblCreate('rejestr', array(
            'opcja' => 'text not null',
            'wartosc' => 'text'
        ));

        $this->Isf->DbTblCreate('planlek', array(
            'dzien' => 'text',
            'klasa' => 'text',
            'lekcja' => 'text',
            'przedmiot' => 'text',
            'sala' => 'text',
            'nauczyciel' => 'text',
            'skrot' => 'text'
        ));

        $this->Isf->DbTblCreate('uzytkownicy', array(
            'uid' => 'numeric not null',
            'login' => 'text not null',
            'haslo' => 'text not null',
            'webapi_token' => 'text',
            'webapi_timestamp' => 'text',
            'ilosc_prob' => 'numeric'
        ));

        $this->Isf->DbTblCreate('log', array(
            'id' => 'serial',
            'data' => 'text not null',
            'modul' => 'text not null',
            'wiadomosc' => 'text',
        ));

        $this->Isf->DbTblCreate('tokeny', array(
            'login' => 'text',
            'token' => 'text',
        ));

        $this->Isf->DbTblCreate('plan_grupy', array(
            'dzien' => 'text',
            'lekcja' => 'text',
            'klasa' => 'text',
            'grupa' => 'text',
            'przedmiot' => 'text',
            'nauczyciel' => 'text',
            'skrot' => 'text',
            'sala' => 'text'
        ));

        $this->Isf->DbTblCreate('zast_id', array(
            'zast_id' => 'serial',
            'dzien' => 'text',
            'za_nl' => 'text',
            'info' => 'text',
        ));

        $this->Isf->DbTblCreate('zastepstwa', array(
            'zast_id' => 'text',
            'lekcja' => 'text',
            'przedmiot' => 'text',
            'nauczyciel' => 'text',
            'sala' => 'text',
        ));

        $this->Isf->DbTblCreate('lek_godziny', array(
            'lekcja' => 'text',
            'godzina' => 'text',
            'dl_prz' => 'text'
        ));

        $this->Isf->DbInsert('rejestr', array(
            'opcja' => 'edycja_danych',
            'wartosc' => '1'
        ));

        $this->Isf->DbInsert('rejestr', array(
            'opcja' => 'ilosc_godzin_lek',
            'wartosc' => '1'
        ));

        $this->Isf->DbInsert('rejestr', array(
            'opcja' => 'dlugosc_lekcji',
            'wartosc' => '45'
        ));

        $this->Isf->DbInsert('rejestr', array(
            'opcja' => 'nazwa_szkoly',
            'wartosc' => $szkola
        ));

        $this->Isf->DbInsert('rejestr', array(
            'opcja' => 'index_text',
            'wartosc' => '<h1>Witaj w Planie Lekcji ' . $ver . '</h1><p>Dziękujemy za skorzystanie z systemu Plan Lekcji</p>
                <p>Proszę zmienić hasła do panelu administracyjnego oraz treść tej strony w bocznym panelu użytkownika.</p>'
                ), false);

        $this->Isf->DbInsert('rejestr', array(
            'opcja' => 'ilosc_grup',
            'wartosc' => '0'
        ));

        $this->Isf->DbInsert('rejestr', array(
            'opcja' => 'godz_rozp_zaj',
            'wartosc' => '08:00'
        ));

        $this->Isf->DbInsert('rejestr', array(
            'opcja' => 'installed',
            'wartosc' => '1'
        ));

        $this->Isf->DbInsert('rejestr', array(
            'opcja' => 'app_ver',
            'wartosc' => $ver
        ));

        $this->Isf->DbInsert('rejestr', array(
            'opcja' => 'randtoken_version',
            'wartosc' => $ver
        ));

        $this->Isf->DbInsert('log', array(
            'data' => date('d.m.Y H:i:s'),
            'modul' => 'plan.install',
            'wiadomosc' => 'Instalacja systemu'
        ));

        $this->Isf->DbInsert('log', array(
            'data' => date('d.m.Y H:i:s'),
            'modul' => 'plan.install',
            'wiadomosc' => 'Tworzenie administratora'
        ));

        $pass = substr(md5(@date('Y:m:d')), 0, 8);
        $pass = rand(1, 100) . $pass;

        $this->Isf->DbInsert('uzytkownicy', array(
            'uid' => 1,
            'login' => 'root',
            'haslo' => md5('plan' . sha1('lekcji' . $pass)),
        ));

        $token = substr(md5(time() . 'plan'), 0, 6);

        $this->Isf->DbInsert('tokeny', array('login' => 'root', 'token' => md5('plan' . $token)));

        return array('pass' => $pass, 'token' => $token);
    }

}