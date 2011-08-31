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
        <link rel="stylesheet" type="text/css" href="<?php echo URL::base() ?>lib/css/themes/<?php echo $_SESSION['app_theme']; ?>.css"/>
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
    <body <?php echo $bodystr; ?>>

        <?php if (Kohana_Isf::factory()->detect_ie()): ?>
            <!-- pasek o niezgodnosci -->
            <div class="a_error" style="width: 100%">
                Ta aplikacja jest niezgodna z przeglądarką Internet Explorer. Przepraszamy!
            </div>
        <?php endif; ?>

        <div id="mainw">
            <table class="main">
                <tr style="vertical-align: top">
                    <td style="width: 20%; padding-right: 10px;" class="a_light_menu">

                        <div class="app_info">
                            <a href="<?php echo URL::site('default/index'); ?>">
                                <img src="<?php echo URL::base(); ?>lib/images/home.png" alt="" width="24" height="24"/></a>
                            Plan Lekcji
                        </div>

                        <?php if (preg_match('#dev#', $appver)): ?>
                            <div class="a_error" style="width: 100%; font-size: x-small;">
                                Używasz wersji rozwojowej systemu
                            </div>
                        <?php endif; ?>

                        <?php if ($_SESSION['token'] == null): ?>
                            <!-- menu dla niezalogowanych -->
                            <?php echo View::factory()->render('_menu'); ?>
                        <?php else: ?>

                            <?php if (App_Globals::getSysLv() == 1 && $_SESSION['user'] == 'root'): //gdy edycja sal etc  ?>
                                <!-- menu dla roota -->
                                <?php echo View::factory()->render('_menu_root_1'); ?>
                            <?php else: ?>

                                <?php if (App_Globals::getSysLv() == 3 && $_SESSION['user'] != 'root'): //gdy system calkiem otwarty ?>
                                    <!-- menu uzytkownika -->
                                    <?php echo View::factory()->render('_menu_user_3'); ?>
                                <?php endif; ?>

                                <?php if ($reg[1]['wartosc'] == 1 && $_SESSION['user'] != 'root'): //gdy edycja sys ?>
                                    <!-- uzytkownik -->
                                    <p class="error">Witaj <b><?php echo $_SESSION['user']; ?></b>. Niestety, nie masz dostępu do
                                        edycji sal, przedmiotów, godzin, klas i nauczycieli.</p>
                                <?php endif; ?>

                                <?php if ($reg[1]['wartosc'] == 0 && $_SESSION['user'] != 'root'): //gdy edycja planow ?>  
                                    <!-- uzytkownik -->
                                    <?php echo View::factory()->render('_menu_user_0'); ?>
                                <?php endif; ?>

                                <?php if ($reg[1]['wartosc'] != 1 && $_SESSION['user'] == 'root'): //gdy edycja planow i zamkn sys ?>
                                    <!-- menu dla root -->
                                    <?php echo View::factory()->render('_menu_close_root'); ?>
                                <?php endif; ?>

                                <hr/>

                                <!-- plany lekcji -->
                                <?php echo View::factory()->render('_menu_plany'); ?>

                            <?php endif; ?>
                        <?php endif; ?>
                    </td>
                    <td valign="top" style="width: 60%;">
                        <?php echo $content; ?>
                    </td>
                    <!-- menu uzytkownika (boczne) -->
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
                                    <img src="<?php echo URL::base(); ?>lib/images/betasign.png" alt="" height="12"/>
                                </p>
                                <?php if ($reg[1]['wartosc'] == 0 && $_SESSION['user'] == 'root'): ?>
                                    <p>
                                        <img src="<?php echo URL::base(); ?>lib/images/registry.png" alt="" width="16" height="16"/>
                                        <a href="#" onClick="window.open('<?php echo URL::base(); ?>generator.php', 'moje', 'width=500,height=500,scrollbars=1')" >Generator planu zajęć</a>
                                    </p>
                                <?php endif; ?>
                                <p>
                                    <img src="<?php echo URL::base(); ?>lib/images/save.png" alt="" width="16" height="16"/>
                                    <a href="#" onClick="window.open('<?php echo URL::base(); ?>tools/backup.php', 'moje', 'width=500,height=500,scrollbars=1')" >Kopia zapasowa systemu</a>
                                </p>
                            <?php endif; ?>
                            <?php if ($_SESSION['user'] != 'root' && $reg[1]['wartosc'] == 0): ?>
                                <p>
                                    <img src="<?php echo URL::base(); ?>lib/images/betasign.png" alt="" height="12"/>
                                </p>
                                <p>
                                    <img src="<?php echo URL::base() ?>lib/images/warn.gif" alt=""/>
                                    <a href="#" onClick="window.open('<?php echo URL::base(); ?>generator.php', 'moje', 'width=500,height=500,scrollbars=1')" >
                                        Generator planów zajęć
                                    </a>
                                </p>
                            <?php endif; ?>
                        </td>
                    <?php endif; ?>
                <tr class="app_ver">
                    <td colspan="
                    <?php if ($_SESSION['token'] == null): ?>
                            2
                        <?php else: ?>
                            3
                        <?php endif; ?>
                        ">
                        <div>
                            <form action="<?php echo URL::site('default/look'); ?>" method="post" onchange="document.forms['lookf'].submit();" id="lookf" name="lookf">
                                Wybierz wygląd:
                                <select name="look">
                                    <?php foreach (App_Globals::getThemes() as $theme): ?>
                                        <?php if ($_SESSION['app_theme'] == $theme): ?>
                                            <option selected><?php echo $theme; ?></option>
                                        <?php else: ?>
                                            <option><?php echo $theme; ?></option>    
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </select>
                                <input type="hidden" name="site" value="<?php echo str_replace('index.php/', '', $_SERVER['REQUEST_URI']); ?>"/>
                            </form>
                            <div id="foot">
                                <b>Plan lekcji </b><?php echo $appver; ?> - <?php echo $ns[1]['wartosc']; ?> |
                                <a href="http://planlekcji.googlecode.com" target="_blank">strona projektu Plan Lekcji</a>
                            </div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </body>
</html>
