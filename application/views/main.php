<?php
/*
 * Strona glowna Planu Lekcji
 * 
 * @author Michal Bocian <mhl.bocian@gmail.com>
 * 
 */
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<?php
/**
 * Instrukcje dotyczace zmiennych w szablone, gdy
 * nie sa zdefiniowane, ustawia je jako puste (null)
 */
if (!isset($content))
    $content = null;
if (!isset($_SESSION['token']))
    $_SESSION['token'] = null;
if (!isset($script))
    $script = null;
if (!isset($bodystr))
    $bodystr = null;
$isf = new Kohana_Isf();
$isf->DbConnect();
/** sprawdza jaki jest poziom edycji danych (0,1,3) */
$reg = $isf->DbSelect('rejestr', array('*'), 'where opcja=\'edycja_danych\'');
/** pobiera nazwe szkoly */
$ns = $isf->DbSelect('rejestr', array('*'), 'where opcja=\'nazwa_szkoly\'');
$appver = $isf->DbSelect('rejestr', array('*'), 'where opcja=\'app_ver\'');
$appver = $appver[1]['wartosc'];
?>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>Plan lekcji - <?php echo $ns[1]['wartosc']; ?></title>
        <?php echo $script; //wyswietla skrypt, np jquery ?>
        <link rel="stylesheet" type="text/css" href="<?php echo URL::base() ?>lib/css/style.css"/>
        <?php
        $isf->IE9_faviconset();
        $isf->IE9_WebAPP('Internetowy Plan Lekcji', 'Uruchom IPL 1.5', APP_PATH);
        $isf->IE9_apptask('Logowanie', 'index.php/admin/login');
        echo $isf->IE9_make();
        if (isset($_SESSION['token'])) {
            $zadmin = time() + 10 * 60;
            $toktime = strtotime($_SESSION['token_time']);
            if ($zadmin > $toktime) {
                $bodystr = 'onLoad="alert(\'RAND_TOKEN: token wygaśnie za chwilę\\nProszę go odnowić!\');"';
            }
        }
        ?>
    </head>
    <body <?php echo $bodystr; //argumenty html dla tagu body                                                      ?>>
        <div id="mainw">
            <table class="main">
                <tr style="vertical-align: top">
                    <td style="width: 20%; padding-right: 10px;" class="a_light_menu">
                        <div class="app_info">
                            <a href="<?php echo URL::site('default/index'); ?>">
                                <img src="<?php echo URL::base(); ?>lib/images/home.png" alt="" width="24" height="24"/></a>
                            Plan Lekcji

                        </div>
                        <div class="app_ver">
                            <?php echo $appver; ?>
                        </div>
                        <?php if (preg_match('#dev#', $appver)): ?>
                            <div class="a_error" style="width: 100%; font-size: x-small;">
                                Używasz wersji rozwojowej systemu
                            </div>
                        <?php endif; ?>
                        <?php
                        /*
                         * Część dla niezalogowanych
                         */
                        ?>
                        <?php
                        if ($_SESSION['token'] == null) {
                            ?>
                            <?php
                            if ($reg[1]['wartosc'] == 1): // czy jest edycja sal, przedmiotów
                                ?>
                                <p>
                                    <a href="<?php echo URL::site('admin/login'); ?>" style="font-size: 10pt; font-weight: bold;">
                                        <img src="<?php echo URL::base(); ?>lib/images/t1.png" alt="" width="24" height="24"/> Administracja
                                    </a>
                                </p>
                                <p class="info">System będzie niedostępny, dopóki opcja edycji sal, przedmiotów, itp.
                                    będzie <b>włączona</b>.</p>
                                <?php
                            else:
                                ?>
                                <p>
                                    <a href="<?php echo URL::site('admin/login'); ?>" style="font-size: 10pt; font-weight: bold;">
                                        <img src="<?php echo URL::base(); ?>lib/images/t1.png" alt="" width="24" height="24"/> Administracja
                                    </a>
                                </p>
                                <?php if ($reg[1]['wartosc'] == 3): //gdy system jest calkowicie otwarty bez edycji sal, czy planow ?>
                                    <p>
                                        <a href="<?php echo URL::site('zastepstwa/index'); ?>" style="font-size: 10pt; font-weight: bold;">
                                            <img src="<?php echo URL::base(); ?>lib/images/notes.png" alt="" width="24" height="24"/> Zastępstwa
                                        </a>
                                    </p>
                                    <hr/>
                                    <p>
                                        <img src="<?php echo URL::base(); ?>lib/images/t2.png" alt="" width="24" height="24"/>
                                        <a href="<?php echo URL::site('podglad/zestawienie'); ?>" style="font-size: 10pt; font-weight: bold;" target="_blank">
                                            Zestawienie planów
                                        </a>
                                    </p>
                                    <h3>Plany lekcji według klas</h3>
                                    <ul>
                                        <?php foreach ($isf->DbSelect('klasy', array('*'), 'order by klasa asc') as $rw => $rc): ?>
                                            <li><a target="_blank" href="<?php echo URL::site('podglad/klasa/' . $rc['klasa']); ?>"><?php echo $rc['klasa']; ?></a></li>
                                        <?php endforeach; ?>
                                    </ul>
                                    <h3>Plany lekcji według sali</h3>
                                    <ul>
                                        <?php foreach ($isf->DbSelect('sale', array('*'), 'order by sala asc') as $rw => $rc): ?>
                                            <li><a target="_blank" href="<?php echo URL::site('podglad/sala/' . $rc['sala']); ?>"><?php echo $rc['sala']; ?></a></li>
                                        <?php endforeach; ?>    
                                    </ul>
                                    <h3>Plany lekcji według nauczycieli</h3>
                                    <ul>
                                        <?php foreach ($isf->DbSelect('nauczyciele', array('*'), 'order by imie_naz asc') as $rw => $rc): ?>
                                            <li>(<?php echo $rc['skrot']; ?>) <a href="<?php echo URL::site('podglad/nauczyciel/' . $rc['skrot']); ?>" target="_blank"><?php echo $rc['imie_naz']; ?></a></li>
                                        <?php endforeach; ?>    
                                    </ul>
                                <?php else: ?>
                                    <p class="info">Dopóki system edycji planów będzie otwarty, nie ma możliwości
                                        podglądu planu zajęć oraz zastępstw.</p>
                                <?php endif; ?>
                            <?php
                            endif;
                        } else {
                            // tresc dla zalogowanych
                            ?>
                            <?php if ($reg[1]['wartosc'] == 1 && $_SESSION['user'] == 'root'): //gdy edycja sal etc ?>

                                <table border="0" width="100%">
                                    <thead class="a_odd">
                                        <tr style="text-align: center;">
                                            <td colspan="2">
                                                Menu administratora
                                            </td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                &bull; <a href="<?php echo URL::site('sale/index'); ?>">Sale</a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                &bull; <a href="<?php echo URL::site('przedmioty/index'); ?>">Przedmioty</a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                &bull; <a href="<?php echo URL::site('nauczyciele/index'); ?>">Nauczyciele</a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                &bull; <a href="<?php echo URL::site('klasy/index'); ?>">Klasy</a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                &bull; <a href="<?php echo URL::site('admin/users'); ?>">Użytkownicy</a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                &bull; <a href="<?php echo URL::site('godziny/index'); ?>">Godziny lekcyjne i przerwy</a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                &bull; <a href="<?php echo URL::site('regedit'); ?>">Podgląd rejestru</a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                &bull; <a href="<?php echo URL::site('admin/logs'); ?>">Podgląd dzienników</a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                &bull; <b><a href="<?php echo URL::site('admin/zamknij'); ?>">Zamknięcie edycji</a></b>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <p class="info">System aktualnie umożliwia edycję takich danych, jak sale, przedmioty,
                                    nauczyciele i inne. Dopóki nie zamkniesz systemu, edycja planów zajęć nie będzie dostępna.
                                    Późniejszy powrót do tej strony jest możliwy poprzez opcję <b>Wyczyść system</b>.</p>
                            <?php else: ?>
                                <?php if ($reg[1]['wartosc'] == 3 && $_SESSION['user'] != 'root'): //gdy system calkiem otwarty ?>
                                    <p>
                                        <a href="<?php echo URL::site('podglad/zestawienie'); ?>" style="font-size: 10pt; font-weight: bold;" target="_blank">
                                            <img src="<?php echo URL::base(); ?>lib/images/t2.png" alt="" width="24" height="24"/> Zestawienie planów
                                        </a>
                                    </p>
                                    <hr/>
                                    <p>
                                        <img src="<?php echo URL::base(); ?>lib/images/notes.png" alt="" width="24" height="24"/>
                                        <a href="<?php echo URL::site('zastepstwa/edycja'); ?>" style="font-size: 10pt; font-weight: bold;">
                                            Nowe zastępstwo
                                        </a>
                                    </p>
                                    <p>
                                        <img src="<?php echo URL::base(); ?>lib/images/notes.png" alt="" width="24" height="24"/>
                                        <a href="<?php echo URL::site('zastepstwa/index'); ?>" style="font-size: 10pt; font-weight: bold;">
                                            Przegląd zastępstw
                                        </a>
                                    </p>
                                    <p class="info">System edycji planów został zamknięty. Aby ponownie mieć dostęp do systemu,
                                        wykonaj reset, który utraci zapisane plany oraz zastępstwa.</p>
                                <?php endif; ?>
                                <?php if ($reg[1]['wartosc'] == 1 && $_SESSION['user'] != 'root'): //gdy edycja sys ?>
                                    <p class="error">Witaj <b><?php echo $_SESSION['user']; ?></b>. Niestety, nie masz dostępu do
                                        edycji sal, przedmiotów, godzin, klas i nauczycieli.</p>
                                <?php endif; ?>
                                <?php if ($reg[1]['wartosc'] == 0 && $_SESSION['user'] != 'root'): //gdy edycja planow ?>

                                    <div class="ui-state-highlight ui-corner-all" style="margin-bottom: 10px; padding: 0pt 0.7em; max-width: 100%;">
                                        <p class="info">
                                            <span class="ui-icon ui-icon-info" style="float: left; margin-right: 0.3em;"></span>
                                            System zastępstw będzie dostępny po zamknięciu edycji planów zajęć</p>
                                    </div>

                                    <table border="0" width="100%">
                                        <thead style="background-color: lightgray; text-align: center;">
                                            <tr>
                                                <td colspan="2">
                                                    Edycja planów
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="2">
                                                    <a href="<?php echo URL::site('admin/zamknij2'); ?>" id="closebtn">Zamknij edycję planów</a>
                                                </td>
                                            </tr>
                                        </thead>
                                        <?php foreach ($isf->DbSelect('klasy', array('klasa'), 'order by klasa asc') as $r => $c): ?>
                                            <tr valign="top">
                                                <td style="background-color: #ccccff;"><b><?php echo $c['klasa']; ?></b></td>
                                                <td style="background-color: #DDD;">
                                                    &bull; <a href="<?php echo URL::site('plan/klasa/' . $c['klasa']); ?>" target="_blank">Plan wspólny</a><br/>
                                                    <?php
                                                    $grp = $isf->DbSelect('rejestr', array('*'), 'where opcja=\'ilosc_grup\'');
                                                    ?>
                                                    <?php if ($grp[1]['wartosc'] > 0): ?>
                                                        &bull; <a href="<?php echo URL::site('plan/grupy/' . $c['klasa']); ?>" target="_blank">Plan grupowy</a>
                                                    <?php endif; ?>    
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </table>
                                    <?php
                                    /**
                                     * Skrypt JS dla menu planow
                                     */
                                    if (!isset($_SESSION['user']))
                                        $_SESSION['user'] = null;
                                    if ($_SESSION['user'] != 'root') {
                                        $isf->JQUi();
                                        $isf->JQUi_ButtonCreate('closebtn');
                                        echo $isf->JQUi_MakeScript();
                                    }
                                    ?>
                                <?php endif; ?>
                                <?php if ($reg[1]['wartosc'] != 1 && $_SESSION['user'] == 'root'): //gdy edycja planow i zamkn sys ?>
                                    <p>
                                        <img src="<?php echo URL::base(); ?>lib/images/keys.gif" alt="" width="24" height="24"/>
                                        <a href="<?php echo URL::site('admin/users'); ?>" >Użytkownicy i autoryzacja</a>
                                    </p>
                                    <p>
                                        <img src="<?php echo URL::base(); ?>lib/images/registry.png" alt="" width="24" height="24"/>
                                        <a href="<?php echo URL::site('regedit'); ?>" >Podgląd rejestru</a>
                                    </p>
                                    <p>
                                        <img src="<?php echo URL::base(); ?>lib/images/notes.png" alt="" width="24" height="24"/>
                                        <a href="<?php echo URL::site('admin/logs'); ?>" >Podgląd dzienników</a>
                                    </p>
                                    <p class="info">Jako <b>root</b> nie masz dostępu do edycji planów i zastępstw.
                                        Aby powrócić do ustawień sal, przedmiotów i nauczycieli wykonaj reset systemu,
                                        który usunie wszystkie plany.</p>
                                <?php endif; ?>
                                <hr/>
                                <?php if ($reg[1]['wartosc'] == 3): // gdy edycja planow zamknieta ?>
                                    <h3>Plany lekcji według klas</h3>
                                    <ul>
                                        <?php foreach ($isf->DbSelect('klasy', array('*'), 'order by klasa asc') as $rw => $rc): ?>
                                            <li><a href="<?php echo URL::site('podglad/klasa/' . $rc['klasa']); ?>" target="_blank"><?php echo $rc['klasa']; ?></a></li>
                                        <?php endforeach; ?>
                                    </ul>
                                    <h3>Plany lekcji według sali</h3>
                                    <ul>
                                        <?php foreach ($isf->DbSelect('sale', array('*'), 'order by sala asc') as $rw => $rc): ?>
                                            <li><a href="<?php echo URL::site('podglad/sala/' . $rc['sala']); ?>" target="_blank"><?php echo $rc['sala']; ?></a></li>
                                        <?php endforeach; ?>    
                                    </ul>
                                    <h3>Plany lekcji według nauczycieli</h3>
                                    <ul>
                                        <?php foreach ($isf->DbSelect('nauczyciele', array('*'), 'order by imie_naz asc') as $rw => $rc): ?>
                                            <li><?php echo $rc['skrot']; ?> <a href="<?php echo URL::site('podglad/nauczyciel/' . $rc['skrot']); ?>" target="_blank"><?php echo $rc['imie_naz']; ?></a></li>
                                        <?php endforeach; ?>    
                                    </ul>
                                <?php else: ?>
                                    <p class="info">Podgląd planów będzie dostępny po zamknięciu edycji planów zajęć.</p>
                                <?php endif; ?>
                            <?php endif; ?>
                        <?php } ?>
                    </td>
                    <td valign="top" style="width: 60%;">
                        <?php echo $content; ?>
                    </td>
                    <?php
                    /*
                     * Menu administratora (górne)
                     */
                    ?>
                    <?php if ($_SESSION['token'] != null): ?>
                        <?php
                        $zadmin = time() + 10 * 60;
                        $toktime = strtotime($_SESSION['token_time']);
                        if ($zadmin > $toktime) {
                            $tokenizer = '<i class="error" style="text-decoration:blink;"><b>' . $_SESSION['token_time'] . '</b></i>';
                        } else {
                            $tokenizer = '<i class="notice">' . $_SESSION['token_time'] . '</i>';
                        }
                        ?>
                        <td valign="top" style="width: 20%;">
                            <fieldset>
                                <legend>
                                    <img src="<?php echo URL::base() ?>lib/images/user.gif" alt=""/>
                                    Zalogowany jako: <b><?php echo $_SESSION['user']; ?></b>
                                </legend>
                                <ul style="font-size: 8pt; list-style: none; padding: 0px;">
                                    <li>
                                        <a href="<?php echo URL::site('admin/renew'); ?>">
                                            <img src="<?php echo URL::base() ?>lib/images/keylabel.gif" alt=""/>
                                            Odnów mój token
                                        </a>
                                    </li>
                                    <li>
                                        <a href="<?php echo URL::site('admin/haslo'); ?>">
                                            <img src="<?php echo URL::base() ?>lib/images/keys.gif" alt=""/>
                                            Zmień moje hasło
                                        </a>
                                    </li>
                                    <li>
                                        <a href="<?php echo URL::site('admin/logout'); ?>">
                                            <img src="<?php echo URL::base() ?>lib/images/keygenoff.gif" alt=""/>
                                            Wyloguj mnie
                                        </a>
                                    </li>
                                    <li>
                                        <b>Token wygasa o: </b> <?php echo $tokenizer; ?>
                                    </li>
                                </ul>
                            </fieldset>
                            <?php if ($reg[1]['wartosc'] == 3 && isset($_SESSION['token'])): ?>
                                <p>
                                    <img src="<?php echo URL::base(); ?>lib/images/save.png" alt="" width="16" height="16"/>
                                    <a href="#" onClick="window.open('<?php echo URL::base(); ?>export.php', 'moje', 'width=500,height=500,scrollbars=1')" >Eksport planu zajęć</a>
                                </p>
                            <?php endif; ?>
                            <?php if ($reg[1]['wartosc'] == 3 && $_SESSION['user'] == 'root'): ?>
                                <p>
                                    <img src="<?php echo URL::base(); ?>lib/images/registry.png" alt="" width="16" height="16"/>
                                    <a href="#" onClick="window.open('<?php echo URL::base(); ?>generator.php', 'moje', 'width=500,height=500,scrollbars=1')" >Generator planu zajęć (BETA)</a>
                                </p>
                            <?php endif; ?>
                            <?php if ($_SESSION['user'] == 'root'): ?>
                                <p>
                                    <a href="<?php echo URL::site('admin/zmiendane'); ?>">
                                        <img src="<?php echo URL::base() ?>lib/images/settings.gif" alt=""/>
                                        Ustawienia szkoły i strony głównej
                                    </a>
                                </p>
                                <p>
                                    <a  href="<?php echo url::site('admin/reset'); ?>">
                                        <img src="<?php echo URL::base() ?>lib/images/warn.gif" alt=""/>
                                        Wyczyść system
                                    </a>
                                </p>
                                <p>
                                    <img src="<?php echo URL::base(); ?>lib/images/save.png" alt="" width="16" height="16"/>
                                    <a href="#" onClick="window.open('<?php echo URL::base(); ?>tools/backup.php', 'moje', 'width=500,height=500,scrollbars=1')" >Kopia zapasowa systemu</a>
                                </p>
                            <?php endif; ?>
                        </td>
                    <?php endif; ?>
            </table>
            <div id="foot" class="a_light_menu">
                <b>Plan lekcji</b> - <?php echo $ns[1]['wartosc']; ?> |
                <a href="http://planlekcji.googlecode.com" target="_blank">strona projektu Plan Lekcji</a>
            </div>
        </div>
    </body>
</html>
