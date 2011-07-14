<?php
/*
 * Strona główna Planu Lekcji
 * 
 * Główny szablon
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
    <body <?php echo $bodystr; //argumenty html dla tagu body            ?>>
        <div id="top">
            <a href="<?php echo URL::site(''); ?>">
                <img src="<?php echo URL::base() ?>lib/images/logo.png" alt="<?php echo $ns[1]['wartosc']; ?>"
                     style="height: 70px;"/></a>
        </div>
        <hr/>
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
            <div id="menuad">
                Zalogowany jako: <b><?php echo $_SESSION['user']; ?></b>
                (<a href="<?php echo URL::site('admin/logout'); ?>">wyloguj</a>)
                &emsp;&bull;
                <b>ważność tokena:</b> <?php echo $tokenizer; ?>&nbsp;|
                <a href="<?php echo URL::site('admin/renew'); ?>" class="anac">odnów</a>
                &emsp;&bull;
                <a href="<?php echo URL::site('admin/haslo'); ?>">zmiana hasła</a>
                <?php if ($_SESSION['user'] == 'root'): ?>
                    &emsp;&bull;
                    <a href="<?php echo URL::site('admin/zmiendane'); ?>">ustawienia szkoły i strony głównej</a>&emsp;&bull;
                    <a class="anac" href="<?php echo url::site('admin/reset'); ?>">resetowanie systemu</a>
                <?php endif; ?>
            </div>
            <hr/>
        <?php endif; ?>
        <table class="main">
            <tr style="vertical-align: top"><td style="width: 20%">
                    <p>
                        <a href="<?php echo URL::site('default/index'); ?>" style="font-size: 10pt; font-weight: bold;">
                            <img src="<?php echo URL::base(); ?>lib/images/home.png" alt="" width="24" height="24"/>Strona główna</a>
                    </p>
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
                                        <img src="<?php echo URL::base(); ?>lib/images/t2.png" alt="" width="24" height="24"/> Zastępstwa
                                    </a>
                                </p>
                                <hr/>
                                <p>
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
                            <h3>Menu administratora</h3>
                            <ul>
                                <li>
                                    <a href="<?php echo URL::site('sale/index'); ?>">Sale</a>
                                </li>
                                </li>
                                <li><a href="<?php echo URL::site('przedmioty/index'); ?>">Przedmioty</a></li>
                                <li><a href="<?php echo URL::site('nauczyciele/index'); ?>">Nauczyciele</a></li>
                                <li><a href="<?php echo URL::site('klasy/index'); ?>">Klasy</a></li>
                                <li><a href="<?php echo URL::site('admin/users'); ?>">Użytkownicy</a></li>
                                <li>
                                    <a href="<?php echo URL::site('godziny/index'); ?>">Godziny lekcyjne i przerwy</a>
                                </li>
                                <li><b><a href="<?php echo URL::site('admin/zamknij'); ?>">Zamknięcie edycji</a></b></li>
                            </ul>
                            <ul>
                                <li><a href="<?php echo URL::site('regedit'); ?>">Podgląd rejestru</a></li>
                                <li><a href="<?php echo URL::site('admin/logs'); ?>">Podgląd dzienników</a></li>
                            </ul>
                            <p class="info">Dopóki nie zamkniesz edycji danych, nie będziesz mógł tworzyć planów.
                                Zamknięcie edycji oznacza <b>brak możliwości</b> ponownej edycji danych, chyba, że
                                wykonasz reset systemu, który wiąże się z utratą pewnych danych.</p>
                        <?php else: ?>
                            <?php if ($reg[1]['wartosc'] == 3 && $_SESSION['user'] != 'root'): //gdy system calkiem otwarty ?>
                                <p>
                                    <a href="<?php echo URL::site('podglad/zestawienie'); ?>" style="font-size: 10pt; font-weight: bold;" target="_blank">
                                        <img src="<?php echo URL::base(); ?>lib/images/t2.png" alt="" width="24" height="24"/> Zestawienie planów
                                    </a>
                                </p>
                                <hr/>
                                <p>
                                    <a href="<?php echo URL::site('zastepstwa/edycja'); ?>" style="font-size: 10pt; font-weight: bold;">
                                        Nowe zastępstwo
                                    </a>
                                </p>
                                <p>
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
                                <p class="info">System zastępstw będzie dostępny po zamknięciu edycji planów zajęć</p>
                                <a href="<?php echo URL::site('admin/zamknij2'); ?>" class="anac">Zamknij edycję planów</a>
                                <h3>Edycja planów</h3>
                                <ul>
                                    <?php foreach ($isf->DbSelect('klasy', array('klasa'), 'order by klasa asc') as $r => $c): ?>
                                        <li><a href="#" id="class_plan_<?php echo $c['klasa']; ?>"><?php echo $c['klasa']; ?></a></li>
                                        <li id="ul_classedit_<?php echo $c['klasa']; ?>" style="display:none; list-style: none; margin-top: 0px;">
                                            <ul>
                                                <li>
                                                    <a href="<?php echo URL::site('plan/klasa/' . $c['klasa']); ?>" target="_blank">Plan wspólny</a>
                                                </li>
                                                <?php
                                                $grp = $isf->DbSelect('rejestr', array('*'), 'where opcja="ilosc_grup"');
                                                ?>
                                                <?php if ($grp[1]['wartosc'] > 0): ?>
                                                    <li><a href="<?php echo URL::site('plan/grupy/' . $c['klasa']); ?>" target="_blank">Plan grupowy</a></li>
                                                <?php endif; ?>    
                                                <li style="list-style: none">&nbsp;</li>
                                            </ul>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                            <?php if ($reg[1]['wartosc'] != 1 && $_SESSION['user'] == 'root'): //gdy edycja sal etc ?>
                                <p>
                                    <img src="<?php echo URL::base(); ?>lib/images/t1.png" alt="" width="24" height="24"/>
                                    <a href="<?php echo URL::site('admin/users'); ?>" class="anac">Użytkownicy i autoryzacja</a>
                                </p>
                                <ul>
                                    <li><a href="<?php echo URL::site('regedit'); ?>">Podgląd rejestru</a></li>
                                    <li><a href="<?php echo URL::site('admin/logs'); ?>">Podgląd dzienników</a></li>
                                </ul>
                                <p class="error">Jako <b>root</b> nie masz dostępu do edycji planów i zastępstw.
                                    Aby powrócić do ustawień sal, przedmiotów i nauczycieli wykonaj reset systemu,
                                    który usunie wszystkie plany.</p>
                            <?php endif; ?>
                            <hr/>
                            <?php if ($reg[1]['wartosc'] == 3): // gdy edycja planow zamknieta ?>
                                <?php if ($_SESSION['token'] != null): ?>
                                        <img src="<?php echo URL::base(); ?>lib/images/save.png" alt="" width="24" height="24"/>
                                        <a href="#" onClick="window.open('<?php echo URL::base(); ?>export.php', 'moje', 'width=500,height=500')" class="anac">Eksport planu zajęć</a>
                                <?php endif; ?>
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
                <td>
                    <?php echo $content; ?>
                </td>
        </table>
        <hr/>
        <div id="foot">
            <p>
                <img src="<?php echo URL::base(); ?>lib/images/gplv3.png" alt="GNU GPL v3 logo"/>
                <b>Plan lekcji</b> - <?php echo $ns[1]['wartosc']; ?> |
                <a href="http://planlekcji.googlecode.com" target="_blank">strona projektu Plan Lekcji</a></p>
        </div>
        <?php
        /**
         * Skrypt JS dla menu planow
         */
        if (!isset($_SESSION['user']))
            $_SESSION['user'] = null;
        if ($_SESSION['user'] != 'root') {
            $isf->JQUi();
            foreach ($isf->DbSelect('klasy', array('*')) as $rid => $rcl) {
                $f1 = '$("#class_plan_' . $rcl['klasa'] . '").click(function(){$("#ul_classedit_' . $rcl['klasa'] . '").toggle();});';
                $isf->JQUi_CustomFunction($f1);
            }
            echo $isf->JQUi_MakeScript();
        }
        ?>
    </body>
</html>
