<?php

/**
 * Plik jadra frameworku Intersys
 *
 * @author Michal Bocian <mhl.bocian@gmail.com>
 * @package isf\root
 * @version 1.0.1
 */

namespace isf;

/**
 * Sprawdza wersje PHP
 */
if (phpversion() < 5.3)
    die('Aplikacje oparte na ISFramework wymagaja PHP w wersji min. 5.3');
/**
 * Separator katalogow w zaleznosci od systemu
 */
define('DS', DIRECTORY_SEPARATOR);
/**
 * Sciezka aplikacji
 */
define('APP_PATH', realpath(__DIR__ . '/../'));
/**
 * Dolaczenie pliku konifguracyjnego
 */
require_once APP_PATH . DS . 'intersys' . DS . 'config.php';
/**
 * Dolaczenie pliku ze stalymi
 */
require_once APP_PATH . DS . 'intersys' . DS . 'defines.php';

namespace isf\template;

/**
 * Klasa obslugi szablonow
 * 
 * W przypadku skomlikowanych projektow uzywac
 * innego systemu szablonow
 *
 * @package isf\template
 * @todo obsluga petli foreach
 */
class Tpl_Load {

    /**
     *
     * @var text $template Tresc szablonu
     */
    protected $template;

    /**
     * Pobiera szablon z folderu <b>templates</b>.
     *
     * Nalezy napisac nazwe pliku w katalogu <b>templates</b>
     * <b>bez rozszerzenia</b>.
     *
     * Przyklad uzycia:
     * <code>
     * use isf\template\Tpl_Load;
     * $Tpl = new Tpl_Load('nazwa');
     * </code>
     *
     * @param text $template Nazwa pliku w katalogu <b>templates</b>
     */
    public function __construct($template) {
        $tplpath = APP_PATH . DS . 'templates' . DS . $template . '.html';
        if (!file_exists($tplpath)) {
            $this->template = 'Szablon <b>' . $tplpath . '</b> nie istnieje';
        } else {
            $th = file($tplpath);
            foreach ($th as $line => $value) {
                $this->template .= $value;
            }
        }
    }

    /**
     * Przypisuje do zmiennej szablonu wartosc
     * 
     * Musimy najpierw zdefiniowac szablon za pomaca konstruktora.
     * W szablonie posiadamy zmiena <b>test</b> w postaci <b>{$test}</b>
     * 
     * Przyklad uzycia:
     * <code>
     * <?php
     * //utworzenie obiektu Create_Template do zmiennej $Tpl
     * $Tpl->assign('test', 'wartosc');
     * ?>
     * </code>
     *
     * @param text $key Nazwa zmiennej w szablonie
     * @param text $value Wartosc w szablonie
     */
    public function assign($key, $value=null) {

        if (!\is_array($key)) {
            $this->template = str_replace('{$' . $key . '}', $value, $this->template);
        } else {
            $this->template = str_replace('{$' . $key . '}', '[not implemented]', $this->template);
        }
    }

    /**
     * Przetwarza i wyswietla szablon
     * 
     * Przyklad uzycia
     * <code>
     * <?php
     * //utworzenie obiektu Create_Template do zmiennej $Tpl
     * $Tpl->assign('test', 'wartosc');
     * $Tpl->render();
     * ?>
     * </code>
     *
     * @param bool $echo Czy funkcja ma wyswietlic, zamiast zwrocic wartosc?
     * @return text template Zwraca gotowy (przetworzony) szablon
     */
    public function render($echo=true) {
        //sprawdza czy szablon jest zdefiniowany
        if (empty($this->template))
            $this->template = 'Nie zdefiniowano szablonu!';
        //zamienia zmienne systemowe stalych {$const.nazwa_zmiennej}
        $pattern = '/{\$const.(.*?)}/e';
        $this->template = preg_replace($pattern, "''.constant('\\1').''", $this->template);
        //zamienia zmienne systemowe $_POST
        $pattern = '/{\$post.(.*?)}/e';
        $this->template = preg_replace($pattern, "''.\$_POST['\\1'].''", $this->template);
        //wyswietla lub zwraca wartosc szablonu
        if ($echo == true)
            echo $this->template;
        else
            return $this->template;
    }

}

namespace isf\db;

/**
 * Klasa obluguje metody zwiazane z baza danych
 *
 * @package isf\Db
 * @todo
 *  - pelna obsluga CRUD
 *  - laczenie tabel
 */
class Db_Connect {

    /**
     * Uchwyt polaczenia z baza SQLite3
     *
     * @var text $dbhandle Uchwyt polaczenia
     */
    protected $dbhandle;

    /**
     * Laczy sie z baza danych
     *
     * Przyklad uzycia:
     * <code>
     * use isf\db\Db_Connect;
     * $Db = new Db_Connect();
     * </code>
     *
     * @todo
     *  - obsluga innych baz danych
     */
    public function __construct() {
        
        if (!class_exists('SQLite3')) {
            $_err = 'Aby korzystac z obslugi SQLite3, nalezy wlaczyc jego obsluge w PHP. ';
            die($_err);
        }
        
        $this->dbhandle = new \SQLite3(APP_PATH . DS . 'database' . DS . 'database.sqlite');
        if (!\file_exists(\APP_PATH . DS . 'database' . DS . 'database.sqlite'))
            die('Plik z baza danych nie istnieje! Sprawdz czy katalog ma wystarczajace uprawnienia');
            
    }

    /**
     * Funkcja pobiera rekordy w bazie danych
     *
     * SQL: wszystkie kolumny: $columns=array('*');
     *
     * Uzycie funkcji:
     * <code>
     * <?php
     * //utworzenie obiektu Db_Connect
     * $db->select('tabela', array('*');
     * </code>
     *
     * @param text $table Nazwa tabeli
     * @param array $columns Tablica z kolumnami
     * @param text $condition Warunek kwerendy SQL
     * @return array Tablica z rekordami
     */
    public function select($table, $columns, $condition) {
        if (empty($table) || empty($columns)) {
            die('Niepoprawny parametr $table lub $columns');
        }
        foreach ($columns as $col) {
            $cols .= $col . ', ';
        }
        $cols = substr($cols, 0, -2);
        $query = 'select ' . $cols . ' from ' . $table;
        if (!empty($condition))
            $query .= ' ' . $condition;
        $exec = $this->dbhandle->query($query);
        $r = 1;
        while ($row = $exec->fetchArray()) {
            $result[$r] = $row;
            $r++;
        }
        return $result;
    }

    /**
     * Dodaje rekord do bazy danych
     *
     * Domyslnie uzywa funkcji htmlspecialchars, jest mozliwosc wylaczenia
     * jej. 'htmlspecialchars' zamienia symbole, jak np. znaczniki na zwykle
     * kody ASCII, UTF, wiec nie sa interpretowane przez przegladarke.
     * Dla bezpieczenstwa jest wlaczana ta funkcja.
     *
     * Przyklad uzycia:
     * <code>
     * <?php
     * //utworzenie obiektu Db_Connect
     * $Db->insert('table', array(
     * 'kol1'=>'wartosc1',
     * 'kol2'=>'wartosc2',
     * 'kol3'=>'wartosc3',
     * );
     * </code>
     *
     * Aby wylaczyc obsluge <b>htmlspecialchars</b> jako trzeci argument nalezy
     * podac <b>false</b> bez cydzyslowia.
     *
     * @param text $table Nazwa tabeli
     * @param array $col_val Tablica w postaci kolumna=>wartosc
     * @param boolean $specialchars Czy uzyc funckji <b>htmlspecialchars</b>
     * @return boolean
     */
    public function insert($table, $col_val, $specialchars=true) {
        if (!is_array($col_val) || empty($table))
            die('Nieprawidlowy argument dla funkcji insert');
        $query = 'insert into ' . $table . ' (';
        foreach ($col_val as $col => $val) {
            $query .= '\'' . $col . '\', ';
        }
        $query = substr($query, 0, -2);
        $query .= ') values (';
        foreach ($col_val as $col => $val) {
            if ($val != 'null') {
                $query .= '\'' . $val . '\', ';
            } else {
                $query .= '' . $val . ', ';
            }
        }
        $query = substr($query, 0, -2);
        $query .= ')';
        if ($specialchars == true)
            $query = htmlspecialchars($query);
        $res = $this->dbhandle->exec($query);
        if ($res == true)
            return true;
        else
            return false;
    }

    /**
     * Wykonuje zapytanie UPDATE jezyka SQL
     * 
     * Uaktualnia rekord o warunku $cond
     * 
     * Przyklad uzycia:
     * <code>
     * //utworzenie obiektu Db_Connect
     * $Db->update('tabela', array(
     *   'kolumna'=>'wartosc',
     *  ), 'id=33');
     * </code>
     *
     * @param text $table Nazwa tabeli
     * @param array $colvals Tablica kolumna=>wartosc do zmiany
     * @param text $cond Warunek <b>where</b>
     * @param text $usehtmlsc Czy zapisywac tagi HTML jako tekst (true)
     * @return bool Sprawdza poprawnosc zapytania 
     */
    public function update($table, $colvals, $cond, $usehtmlsc=true) {
        if (empty($table) || !is_array($colvals) || empty($cond))
            die('Sprawdz parametry funkcji <b>update</b>!');
        $query = 'update ' . $table . ' set ';
        foreach ($colvals as $col => $val) {
            $query .= $col . '=\'' . $val . '\', ';
        }
        $query = substr($query, 0, -2);
        $query .= ' where ' . $cond;
        if ($usehtmlsc == true)
            $query = htmlspecialchars($query);
        if ($this->dbhandle->exec($query) == true)
            return TRUE;
        else
            return FALSE;
    }

    /**
     * Usuwa rekord o danym warunku
     *
     * Przyklad uzycia:
     * <code>
     * //utworzenie obiektu Db_Connect
     * $Db->delete('tabela', 'kolumna=wartosc');
     * </code>
     *
     * @param text $table Nazwa tabeli
     * @param text $cond Warunek <b>where</b>
     * @return bool Sprawdza poprawnosc kwerendy
     */
    public function delete($table, $cond) {
        if (empty($table) || empty($cond))
            die('Sprawdz parametry funkcji <b>delete</b>');
        $query = 'delete from ' . $table . ' where ' . $cond;
        if ($this->dbhandle->exec($query) == true)
            return TRUE;
        else
            return FALSE;
    }

    /**
     * Tworzy tabele
     * 
     * Nazwa $name, tablica $columns (kolumna=>typ)
     *
     * Przyklad uzycia:
     * <code>
     * //utworzenie obiektu Db_Connect
     * $cols=array(
     *  'kolumna1'=>'typ',
     *  'kolumna2'=>'typ',
     *  'kolumna3'=>'typ',
     * );
     * $Db->tbl_create('nazwa', $cols);
     * </code>
     *
     * @param text $name Nazwa tabeli
     * @param array $columns Tablica kolumn i typow danych
     * @return bool Poprawnosc kwerendy SQL 
     */
    public function tbl_create($name, $columns) {
        if (empty($name) || !is_array(($columns)))
            die('Sprawdz parametr funkcji tbl_create');
        $query = 'create table ' . $name . '(';
        foreach ($columns as $col => $type) {
            $query .= '\'' . $col . '\' ' . $type . ', ';
        }
        $query = substr($query, 0, -2);
        $query .= ')';
        if ($this->dbhandle->exec($query))
            return TRUE;
        else
            return FALSE;
    }

}

namespace isf\auth;

/**
 * Klasa zawiera metody odpowiadajace za prace z ciasteczkami i sesjami
 *
 * @package isf\auth
 */
class Auth_Basic {

    /**
     * 
     * @var array $cookie Tablica ciasteczek
     */
    public $cookie;
    /**
     *
     * @var array $session Tablica sesyjna
     */
    public $session;
    private $ctime;
    private $cpath;

    /**
     * Ustawia srodowisko dla pracy z sesjami i ciasteczkami
     *
     * Domyslnie metoda pobiera z pliku <b>defines.php</b> stale:
     * <ul>
     * <li> <b>DEFAULT_COOKIE_TIME</b>
     * <li> <b>DEFAULT_COOKIE_PATH</b>
     * </ul>
     *
     * @param date $ctime Domyslny czas pracy ciasteczka (1h)
     * @param string $cpath Domyslny zakres dzialnia ciastka w domenie (cala domena)
     * @see defines.php
     */
    public function __construct($ctime=DEFAULT_COOKIE_TIME, $cpath=DEFAULT_COOKIE_PATH) {
        session_start(); // domyslna inicjacja sesji
        $this->ctime = $ctime;
        $this->cpath = $cpath;
        $this->cookie = $_COOKIE;
        $this->session = $_SESSION;
    }

    /**
     * Tworzy nowe ciasteczko
     *
     * @param text $name Nazwa ciasteczka
     * @param text $value Wartosc ciasteczka
     */
    public function newcookie($name, $value) {
        setcookie($name, $value, $this->ctime, $this->cpath);
    }

    /**
     * Sprawdza istnienie ciasteczka
     *
     * @param text $name Nazwa ciasteczka
     * @return boolean Zwraca true lub false gdy ciasteczko istnieje
     */
    public function chkcookie($name) {
        if (isset($this->cookie[$name]))
            return TRUE;
        else
            return FALSE;
    }

    /**
     * Pobiera wartosc ciasteczka
     *
     * @param text $name Nazwa ciasteczka
     * @return text Zwraca dane ciasteczka
     */
    public function getcookie($name) {
        if ($this->chkcookie($name))
            return $this->cookie[$name];
        else {
            return 'Ciasteczko ' . $name . ' nie istnieje';
        }
    }

    /**
     * Usuwa ciasteczko
     *
     * @param text $name Nazwa ciasteczka
     */
    public function delcookie($name) {
        setcookie($name, "", time() - 3600);
    }

    /**
     * Tworzy nowa sesje
     *
     * @param text $name Nazwa sesji
     * @param text $value Wartosc sesji
     */
    public function newsession($name, $value) {
        $this->session[$name] = $value;
    }

}

namespace isf\jquery;

/**
 * Framework JQuery UI
 *
 * @package isf\jquery
 */
class Ui {

    /**
     *
     * @var text Wygenerowany skrypt
     */
    private $script;
    /**
     *
     * @var text Sciezka do frameworka JQuery UI
     */
    private $jqpath;

    /**
     * Tworzy nowy obiekt JQueryUI
     * 
     * Gdy parametr $style jest pusty, ladowany jest domyslny szablon
     * zdefiniowany w pliku defines.php, stala JQUI_DEF_THEME
     * 
     * Przykladowe uzycie:
     * <code>
     * use isf\jquery\Ui;
     * $jqui = new Ui(); // mozna dodac parametr $style
     * </code>
     *
     * @param text $style Nazwa stylu w katalogu <b>/templates/css</b>
     */
    public function __construct($style=JQUI_DEF_THEME) {
        $respath = HTTP_ADDR . 'intersys/jquery';
        $path = array(
            1 => $respath . '/css/' . $style . '/style.css',
            2 => $respath . '/js/jquery-ui.js',
            3 => $respath . '/js/jquery.js',
        );
        $this->jqpath = HTTP_ADDR . 'intersys/jquery';
        $this->script = '
            <link type="text/css" href="' . $path[1] . '" rel="stylesheet" />
            <script type="text/javascript" src="' . $path[3] . '"></script>
            <script type="text/javascript" src="' . $path[2] . '"></script>
            <script type="text/javascript">
            $(function(){
            ';
    }

    /**
     * Tworzy unikalny identyfikator obiektu na podstawie nazwy
     *
     * @param text $name Nazwa obiektu
     * @return text Unikalny identyfikator obiektu
     */
    private function hashname($name) {
        $name = $name . 'isf';
        $name = $name . md5($name);
        $name = substr($name, 0, -15);
        return $name;
    }

    /**
     * Mozliwosc dodania wlasnej funkcji JavaScript
     * 
     * <b>UWAGA!</b> Gdy drugi parametr $ui_script (domyslnie true),
     * bedzie mial wartosc domyslna, wowczas skrypt dodany zostanie
     * do glownego skryptu JQuery UI, wykonywany podczas zaladowania strony.
     * Aby tego uniknac i wstawic skrypt do dowolnego miejsca w kodzie,
     * parametr $ui_script, powinien miec wartosc false
     * 
     * Przyklad uzycia:
     * <code>
     * //tworzenie obiektu Ui w zmiennej $Ui
     * $Ui->customfunc(' alert("test"); '); //nalezy dodawac srednik na koncu operacji
     * </code>
     *
     * @param text $function Funkcja w JavaScript
     * @param bool $ui_script Czy umiescic kod w skrypcie JQuery UI
     * @return text Zwraca kod, gdy $ui_script jest false
     */
    public function customfunc($function, $ui_script=true) {
        if ($ui_script == true) {
            $this->script .= '
                ' . $function . '
            ';
        } else {
            return $function;
        }
    }

    /**
     * Zamyka okno dialogowe
     * 
     * Gdy parametr $name nie jest ustawiony, domysla wartosc
     * <b>this</b>, wskazuje na aktualnie otwarte okienko.
     * 
     * <b>UWAGA!</b> Tej funkcji nalezy uzywac w stosunku do innej funkcji
     * wskazujacej, np. na zdarzenie obslugujace hiperlacze (patrz: funkcja
     * <b>anchor_action</b>, w klasie isf\jquery\Ui)
     * 
     * Przyklad uzycia:
     * <code>
     * //tworzenie obiektu Ui w zmiennej $Ui
     * $Ui->dialog_close(); // zamyka otwarte okienko
     * $Ui->dialog_close('okienko'); // zamyka okienko o nazwie 'okienko' (przyklad)
     * </code>
     *
     * @param text $name Domyslnie 'this'
     * @return text Zwraca skrypt
     */
    public function dialog_close($name='this') {
        if ($name != 'this') {
            return '$("#isf_dialog_' . $this->hashname($name) . '").dialog("close");';
        } else {
            return '$(this).dialog("close");';
        }
    }

    /**
     * Otwiera okienko dialogowe
     * 
     * Gdy parametr $name nie jest okreslony, wowczas otwiera
     * okiengo, ktore jest okreslone w skrypcie (?)
     * 
     * <b>UWAGA!</b> Tej funkcji nalezy uzywac w stosunku do innej funkcji
     * wskazujacej, np. na zdarzenie obslugujace hiperlacze (patrz: funkcja
     * <b>anchor_action</b>, w klasie isf\jquery\Ui)
     * 
     * Przyklad uzycia:
     * <code>
     * //tworzenie obiektu Ui w zmiennej $Ui
     * $Ui->dialog_open('test'); // otwiera okienko o nazwie <b>test</b> (przyklad)
     * </code>
     *
     * @param text $name
     * @return text Zwraca skrypt
     */
    public function dialog_open($name='this') {
        if ($name != 'this') {
            return '$("#isf_dialog_' . $this->hashname($name) . '").dialog("open");';
        } else {
            return '$(this).dialog("open");';
        }
    }

    /**
     * Tworzy okienko dialogowe
     * 
     * <b>UWAGA!</b> Oprocz utworzenia stosownego kodu JQuery UI, zwraca rowniez
     * kod HTML, ktory nalezy np. z uzyciem systemu szablonow, dopisac
     * do okreslonej zmiennej.
     * 
     * Przy parametrach $autoopen oraz $modal, ktorych wartoscia jest
     * <b>ciag znakow</b> true lub false, nie mozna uzywac ich, jako
     * zmiennej typu <b>boolean</b>, gdyz jest to zmienna typu <b>string</b>
     * 
     * Przyklad uzycia z systemem szablonow:
     * <code>
     * //tworzenie obiektu Ui w zmiennej $Ui
     * //zalecane uzycie osobnej tablicy
     * $wind['title']='tytul okienka';
     * $wind['content']='tresc okienka';
     * $wind_b=array(
     *  'Zamknij'=>$Ui->dialog_close(), //uzywa funkcji <b>dialog_close</b>
     * ); // tablica przyciskow
     * $wind_html = $Ui->dialog('wind', $wind, $wind_b);
     * $tpl->assign('wind', $wind_html); // przypisuje do zmiennej kod HTML 
     * </code>
     *
     * @param string $name Nazwa okienka dialogowego
     * @param array $content Tablica zawartosci (title, content)
     * @param array $buttons Tablica przyciskow (nazwa, akcja)
     * @param text $autoopen Autootwieranie
     * @param text $modal Przyciemnianie strony
     * @param integer $height Wysokosc okienka
     * @return string Zwraca kod HTML
     */
    public function dialog($name, $content, $buttons, $autoopen='false', $modal='false', $height=null) {
        $name = 'isf_dialog_' . $this->hashname($name);
        $script = '$(\'#' . $name . '\').dialog({
            autoOpen: ' . $autoopen . ',
            width: 500,';
        if ($height != null) {
            $script .= '
        	height: ' . $height . ',
        	';
        }
        $script .= '
            show: "fade",
            hide: "fade",
            modal: ' . $modal . ',
            buttons: {';
        foreach ($buttons as $aname => $action) {
            $script .= '
                "' . $aname . '": function(){
                ' . $action . '
                },';
        }
        $script = substr($script, 0, -1);
        $script .= '
            }
            });';
        $this->script .= $script;
        $return = '<div id="' . $name . '" title="' . $content['title'] . '">' . $content['content'] . '</div>';
        return $return;
    }

    /**
     * Tworzy kod obslugi zdarzen dla hiperlacza o danym <b>id</b>
     * 
     * Przyklad uzycia:
     * <code>
     * //tworzenie obiektu Ui w zmiennej $Ui
     * $Ui->anchor_action('okienko_a', $Ui->dialog_open('wind'));
     * //Dla hiperlacza o id='okienko_a', zdarzeniem jest otwarcie okienka wind
     * </code>
     *
     * @param type $name
     * @param type $action 
     */
    public function anchor_action($name, $action) {
        $this->script .= '
            $("#' . $name . '").click(function(){
                ' . $action . '
            });';
    }

    /**
     * Tworzy przycisk z elementu o danym id
     *
     * @param text $name Nazwa elementu HTML
     */
    public function button_create($name) {
        $this->script .= '
            $("#' . $name . '").button();';
    }

    /**
     * Tworzy widget <b>tabs</b>
     * 
     * Funkcja zwraca kod HTML, dlatego nalezy uzywac jej, np.
     * z systemem szablonow, aby kod przypisac do zmiennej w szablonie.
     * 
     * Tablica $content (title, content)
     * <code>
     * //zalecane zdefiniowanie tablicy
     * $tabs['nazwa_zakladki']=array(
     *  'title'=>'tytul',
     *  'content'=>'tresc zakladki'
     * );
     * </code>
     * 
     * Przykladowe uzycie:
     * <code>
     * //tworzenie obiektu Ui w zmiennej $Ui
     * //utworzenie tablicy $content opisanej wyzej
     * //zdefiniowanie obiektu szablonu $tpl
     * $tabs=$Ui->tabs_create('nazwa_elementu', $tablica_zawartosci);
     * $tpl->assign('nazwa_zmiennej', $tabs);
     * </code>
     *
     * @param text $name Nazwa elementu
     * @param array $content Tablica zawartosci
     * @return text Zwraca kod HTML 
     */
    public function tabs_create($name, $content) {
        $name = $this->hashname($name);
        $this->script .= '
            $("#isf_tabs_' . $name . '").tabs();';
        $code = '<div id="isf_tabs_' . $name . '"><ul>';
        foreach ($content as $tabname => $tabcont) {
            $code .= '<li><a href="#isf_tabs_' . $name . '_' . $tabname . '">' . $tabcont['title'] . '</a></li>';
        }
        $code .= '</ul>';
        foreach ($content as $tabname => $tabcont) {
            $code .= '<div id="isf_tabs_' . $name . '_' . $tabname . '">' . $tabcont['content'] . '</div>';
        }
        $code .= '</div>';
        return $code;
    }

    /**
     * Tworzy pasek postepu
     * 
     * Funkcja zwraca kod HTML, dlatego nalezy jej uzyc np. z systemem
     * szablonow
     * 
     * <b>Funkcja nie jest w pelni sprawna</b>
     *
     * @param text $name Nazwa paska postepu
     * @param text $options Opcje funkcji JQuery UI
     */
    public function progressbar($name, $options=null) {
        $name = 'isf_pgbar_' . $this->hashname($name);
        if ($options == null) {
            $this->script .= '
                $("#' . $name . '").progressbar();';
        } else {
            $this->script .= '
                $("#' . $name . '").progressbar({' . $options . '});';
        }
        $return = '<div id="' . $name . '"></div>';
    }

    /**
     * Tworzy widget <b>accordion</b>
     * 
     * Zalecany sposob zdefinowania zmiennej $content
     * <code>
     * $content[]=array(
     *  'title'=>'tytul sekcji',
     *  'content'=>'tresc sekcji',
     * );
     * $content[]=array(
     *  'title'=>'tytul sekcji2',
     *  'content'=>'tresc sekcji2',
     * );
     * </code>
     * 
     * Przyklad uzycia z systemem szablonow:
     * <code>
     * //tworzenie obiektu Ui w zmiennej $Ui
     * //tworzenie obiektu Template
     * $tpl->assing('zmienna', $Ui->accordion_create('nazwa', $content));
     * </code>
     *
     * @param text $name
     * @param array $content
     * @return text Zwraca kod HTML 
     */
    public function accordion_create($name, $content) {
        $name = $this->hashname($name);
        $this->script .= '
            $("#isf_accor_' . $name . '").accordion({autoHeight:false});';
        $return = '<div id="isf_accor_' . $name . '">';
        foreach ($content as $number => $colval) {
            $return .= '<h3><a href="#">' . $colval['title'] . '</a></h3>
                <div>' . $colval['content'] . '</div>';
        }
        $return .= '</div>';
        return $return;
    }

    /**
     * Tworzy element HTML, specjalnie przygotowany do obslugi
     * zapytan AJAX (domyslnie z paskiem postepu, opcjonalnie z przyciskiem
     * ukrycia elementu).
     *
     * Funkcja generuje gotowy kod HTML, dlatego nalezy uzyc jej w kontekscie
     * innej funkcji, np. przypisania wartosci zmiennej do szablonu.
     * 
     * Przyklad uzycia z systemem szablonow:
     * <code>
     * //tworzenie obiektu Ui w zmiennej $Ui
     * //tworzenie obiektu Template
     * $tpl->assign('zmienna', ajaxdiv_create('nazwa', true, true));
     * // Tworzy element div o unikalnej nazwie, z paskiem postepu, oraz
     * // opcjonalnym przyciskiem ukrycia elementu
     * </code>
     * 
     * @param text $name Nazwa elementu
     * @param bool $progressgif Wyswietlanie animowanego gif-a
     * @param bool $hiddenbtn Pokazanie przycisku ukrycia elementu
     * @return text Zwraca kod HTML
     */
    public function ajaxdiv_create($name, $progressgif=true, $hiddenbtn=false) {
        $name = $this->hashname($name);
        $script = '<div id="isf_adiv_' . $name . '" style="display: none;">';
        if ($progressgif == true) {
            $script .= '<div id="isf_adc_' . $name . '">
                <img src="' . $this->jqpath . '/css/load.gif" id="isf_adl_' . $hash . '"></div>';
        } else {
            $script .= '<div id="isf_adc_' . $name . '"></div>';
        }
        if ($hiddenbtn == true) {
            $script .= '<p><a href="#" id="isf_ada_' . $name . '">Ukryj panel</a></p>';
            $this->button_create('isf_ada_' . $name);
            $this->anchor_action('isf_ada_' . $name, '$("#isf_adiv_' . $name . '").hide("slow");');
        }
        $script .= '</div>';
        return $script;
    }

    /**
     * Wykonuje zapytanie AJAX dla elementu <b>ajaxdiv</b>
     * 
     * Funkcja generuje czysty kod JQuery UI, dlatego nalezy uzyc jej w
     * konteksie innej funkcji, np. obslugi zdarzenia dla hiprelacza.
     *
     * @param text $divname Nazwa elementu <b>ajaxdiv</b>
     * @param text $url Adres URL strony z zapytaniem
     * @return text Zwraca kod JQuery
     */
    public function ajaxdiv_doajax($divname, $url) {
        $divname = $this->hashname($divname);
        $script = '
            $("#isf_adiv_' . $divname . '").show("slow", function(){
                $.ajax({
                    url: \'' . $url . '\',
                    success: function(data){
                        $("#isf_adl_' . $divname . '").fadeOut("slow");
                        $("#isf_adc_' . $divname . '").html(data);
                    }
                });
            });';
        return $script;
    }

    /**
     * Generuje niestandardowy kod zapytania AJAX
     * 
     * Funkcje nalezy uzyc w kontekscie innej funkcji, np. do obslugi
     * zdarzen hiperlacza (<b>zobacz</b>: anchor_action)
     *
     * @param text $url Adres URL strony z zapytaniem
     * @param text $success Funkcja JS, gdy zapytanie zostanie wykonane
     * @return text Zwraca kod JQuery zapytania AJAX
     */
    public function do_ajax($url, $success) {
        $script = '
            $.ajax({
                url: \'' . $url . '\',
                success: function(data){
                    ' . $success . '
                }
            });';
        return $script;
    }

    /**
     * Generuje kod JQuery UI
     * 
     * Funkcji nalezy uzywac, np. z systemem szablonow, aby przypisac
     * jej wartosc do zmiennej w <b>head</b>
     * 
     * Przyklad uzycia:
     * <code>
     * //tworzenie obiektu Ui w zmiennej $Ui
     * //tworzenie obiektu Template
     * $tpl->assign('nazwa_zmiennej_head', $Ui->make_script();
     * </code>
     *
     * @return text Zwraca gotowy kod JQuery UI
     */
    public function make_script() {
        $this->script .= '
            });
            </script>';
        return $this->script;
    }

}

namespace isf\utils;

/**
 * Klasa naglowkow do sekcji head
 * 
 * Definiuje rozne zasoby strony jak RSS, ikone strony
 * oraz znaczniki dla aplikacji webowej Internet Explorer 9,
 * ktora umozliwia podpiecie strony do paska zadan Windows 7
 * 
 * @package isf\utils
 */
class WebResources {

    /**
     *
     * @var text Gotowy skrypt dla znacznikow
     */
    private $script;

    /**
     * Znaczniki umozliwiajace podpiecie aplikacji do paska zadan Windows 7
     * 
     * Tylko Internet Explorer 9
     * 
     * Przyklad uzycia:
     * <code>
     * //tworzenie obiektu np. $tools = new \isf\utils\WebResources();
     * $tools->IE9_WebAPP('Moja strona', 'Otwórz moją stronę');
     * </code>
     *
     * @param text $app_name Nazwa aplikacji
     * @param text $tooltip Opis strony
     * @param text $s_url Adres aplikacji, domyslnie /
     * @param array $win Wymiary okna (szer, wys)
     */
    public function IE9_WebAPP($app_name, $tooltip, $s_url='/', $win=array(800, 600)) {
        $this->script .= '
            <meta name="application-name" content="' . $app_name . '"/>';
        $this->script .= '
            <meta name="msapplication-tooltip" content="' . $tooltip . '"/>';
        $this->script .= '
            <meta name="msapplication-starturl" content="' . $s_url . '"/>';
        $this->script .= '
            <meta name="msapplication-window" content="width=' . $win[0] . ';height=' . $win[1] . '"/>';
    }

    /**
     * Ustawia ikone aplikacji
     * 
     * Domyslnie [adres_http_aplikacji]/favicon.ico
     * 
     * Przyklad uzycia:
     * <code>
     * //tworzenie obiektu np. $tools = new \isf\utils\WebResources();
     * $tools->favicon_set(); //ustawia domyslna sciezke
     * </code>
     *
     * @param string $path Adres ikony
     */
    public function favicon_set($path=null) {
        if ($path == null)
            $path = HTTP_ADDR . 'favicon.ico';
        $this->script .= '
            <link rel="shortcut icon" href="' . $path . '" />';
    }

    /**
     * Tworzy zadanie dla paska zadan Windows 7
     * 
     * Tylko Internet Explorer 9
     * 
     * Przyklad uzycia:
     * <code>
     * //tworzenie obiektu np. $tools = new \isf\utils\WebResources();
     * //odwolanie do metody IE9_WebAPP
     * $tools->IE9_apptask('Moje zadanie', '/strona.php');
     * </code>
     *
     * @param text $name Nazwa zadania
     * @param text $uri Adres pliku. Np. /index.php
     * @param text $icon Adres ikony, domyslnie favicon.ico
     */
    public function IE9_apptask($name, $uri, $icon=null) {
        if ($icon == null)
            $icon = HTTP_ADDR . 'favicon.ico';
        $this->script .= '
            <meta name="msapplication-task" content="name=' . $name . ';action-uri=' . $uri . ';icon-uri=' . $icon . '"/>';
    }

    /**
     * Zwraca gotowy skrypt
     * 
     * Przyklad uzycia z <b>systemem szablonow</b>
     * <code>
     * //tworzenie obiektu np. $tools = new \isf\utils\WebResources();
     * //szablon strony np. zmienna $tpl
     * $tpl->assign('zmienna_w_head', $tools->make_script());
     * </code>
     *
     * @return text Zwraca gotowy skrypt
     */
    public function make_script() {
        return $this->script;
    }

}