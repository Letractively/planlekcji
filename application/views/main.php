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
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>Plan lekcji - <?php echo App_Globals::getRegistryKey('nazwa_szkoly'); ?></title>
        <!-- [SEKCJA]: JavaScript -->
        <?php echo $script; ?>
        <script type="text/javascript">
            function ipl_togglediv(id) {
                var e = document.getElementById(id);
                if(e.style.display == '')
                    e.style.display = 'none';
                else
                    e.style.display = '';
            }

        </script>
        <!-- [/SEKCJA] -->
        <link rel="stylesheet" type="text/css" href="<?php echo URL::base() ?>lib/css/style.css"/>
        <link rel="stylesheet" type="text/css" href="<?php echo URL::base() ?>lib/css/themes/<?php echo $_SESSION['app_theme']; ?>.css"/>
        <!-- [SEKCJA]: Dla przeglądarki -->
        <?php
        $isf = new Kohana_Isf();
        $isf->IE9_faviconset();
        $isf->IE9_WebAPP('Internetowy Plan Lekcji', 'Uruchom IPL', APP_PATH);
        $isf->IE9_apptask('Logowanie', 'index.php/admin/login');
        if (App_Globals::getSysLv() == 3) {
            $isf->IE9_apptask('Zestawienie planów', 'index.php/podglad/zestawienie');
            $isf->IE9_apptask('Zastępstwa', 'index.php/zastepstwa/index');
        }
        echo $isf->IE9_make();
        if (isset($_SESSION['token'])) {
            $zadmin = time() + 10 * 60;
            $toktime = strtotime($_SESSION['token_time']);
            if ($zadmin > $toktime) {
                $bodystr = 'onLoad="alert(\'RAND_TOKEN: token wygaśnie za chwilę\\nProszę go odnowić!\');"';
            }
        }
        ?>
        <style>
            body{
                background-image: url('<?php echo URL::base(); ?>lib/images/background.png');
            }
        </style>
        <!-- [/SEKCJA] -->
    </head>
    <body <?php echo $bodystr; ?>>
        <!-- [SEKCJA]: STRONA GŁÓWNA -->
        <div id="mainw" style="width: 1000px; margin: 0 auto; background-color: white;">
            <table class="main">
                <tr style="vertical-align: top">
                    <!-- [SEKCJA]: PANEL LEWY -->
                    <td style="width: 250px; padding-right: 10px; padding-top: 0px; padding-left: 0px;" class="a_light_menu">
                        <div class="app_info">
                            <a href="<?php echo URL::site('default/index'); ?>">
                                <img src="<?php echo URL::base(); ?>lib/icons/home.png" alt=""/></a>
                            Plan Lekcji
                            <?php echo View::factory()->render('_snippet_theme'); ?>
                        </div>
                        <?php if (preg_match('#dev#', $appver)): ?>
                            <div class="a_error" style="width: 100%; font-size: x-small;">
                                Używasz wersji rozwojowej systemu
                            </div>
                        <?php endif; ?>
                        <div id="sidebar_menu" style="padding-left: 10px;">
                            <?php echo View::factory()->render('_sidebar_menu'); ?>
                        </div>
                        <p class="app_ver">
                            <a href="#" onClick="ipl_togglediv('ipl_tr_bottom');">Pokaż informacje o systemie</a>
                        </p>
                    </td>
                    <!-- [/SEKCJA] -->
                    <!-- [SEKCJA]: PANEL TRESCI -->
                    <td valign="top" style="width: 750px; margin-top: 0px; padding-top: 0px;">
                        <?php echo $content; ?>
                    </td>
                    <!-- [/SEKCJA] -->
                    <?php if ($_SESSION['token'] != null): ?>
                        <!-- [SEKCJA]: PANEL PRAWY -->
                        <?php echo View::factory()->render('_sidebar_right'); ?>
                        <!-- [/SEKCJA] -->
                    <?php endif; ?>

                <tr class="app_ver" id="ipl_tr_bottom" style="display: none;">
                    <?php if ($_SESSION['token'] == null): ?>
                        <?php $colspan = '2'; ?>
                    <?php else: ?>
                        <?php $colspan = '3'; ?>
                    <?php endif; ?>
                    <!-- [SEKCJA]: PANEL DOLNY -->
                    <td colspan="<?php echo $colspan; ?>">
                        <?php echo View::factory()->render('_panel_bottom'); ?>
                    </td>
                    <!-- [/SEKCJA]-->
                </tr>
            </table>
        </div>
    </body>
</html>
