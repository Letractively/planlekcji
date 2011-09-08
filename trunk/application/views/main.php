<?php
/*
 * Strona glowna Planu Lekcji
 * 
 * @author Michal Bocian <mhl.bocian@gmail.com>
 * 
 */
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
$appver = App_Globals::getRegistryKey('app_ver');
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>Plan lekcji - <?php echo App_Globals::getRegistryKey('nazwa_szkoly'); ?></title>
        <!-- [SEKCJA]: JavaScript -->
        <?php echo $script; ?>
        <!-- [/SEKCJA] -->
        <link rel="stylesheet" type="text/css" href="<?php echo URL::base() ?>lib/css/style.css"/>
        <link rel="stylesheet" type="text/css" href="<?php echo URL::base() ?>lib/css/themes/<?php echo $_SESSION['app_theme']; ?>.css"/>
        <!-- [SEKCJA]: Dla przeglądarki -->
        <?php
        $isf = new Kohana_Isf();
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
        <!-- [/SEKCJA] -->
    </head>
    <body <?php echo $bodystr; ?>>

        <?php if (Kohana_Isf::factory()->detect_ie()): ?>
            <!-- [SEKCJA]: KOD DLA INTERNET EXPLORER -->
            <div class="a_error" style="width: 100%">
                Ta aplikacja jest niezgodna z przeglądarką Internet Explorer. Przepraszamy!
            </div>
            <!-- [/SEKCJA] -->
        <?php endif; ?>
        <!-- [SEKCJA]: STRONA GŁÓWNA -->
        <div id="mainw">
            <table class="main">
                <tr style="vertical-align: top">
                    <!-- [SEKCJA]: PANEL LEWY -->
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

                        <?php echo View::factory()->render('_sidebar_menu'); ?>
                    </td>
                    <!-- [/SEKCJA] -->
                    <!-- [SEKCJA]: PANEL TRESCI -->
                    <td valign="top" style="width: 60%;">
                        <?php echo $content; ?>
                    </td>
                    <!-- [/SEKCJA] -->
                    <?php if ($_SESSION['token'] != null): ?>
                        <!-- [SEKCJA]: PANEL PRAWY -->
                        <?php echo View::factory()->render('_sidebar_right'); ?>
                        <!-- [/SEKCJA] -->
                    <?php endif; ?>

                <tr class="app_ver">
                    <?php if ($_SESSION['token'] == null): ?>
                        <?php $colspan = '2'; ?>
                    <?php else: ?>
                        <?php $colspan = '3'; ?>
                    <?php endif; ?>
                    <!-- [SEKCJA]: PANEL DOLNY -->
                    <td colspan="<?php echo $colspan; ?>">
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
                                <b>Plan lekcji </b><?php echo $appver; ?> - <?php echo App_Globals::getRegistryKey('nazwa_szkoly'); ?> |
                                <a href="http://planlekcji.googlecode.com" target="_blank">strona projektu Plan Lekcji</a>
                            </div>
                        </div>
                    </td>
                    <!-- [/SEKCJA]-->
                </tr>
            </table>
        </div>
    </body>
</html>
