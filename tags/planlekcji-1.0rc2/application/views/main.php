<?php
/*
 * Strona główna Planu Lekcji
 * 
 * 
 */
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<?php
if (!isset($content))
    $content = null;
if (!isset($script))
    $script = null;
$isf = new Kohana_Isf();
$isf->DbConnect();
$reg = $isf->DbSelect('rejestr', array('*'), 'where opcja=\'edycja_danych\'');
$ns = $isf->DbSelect('rejestr', array('*'), 'where opcja=\'nazwa_szkoly\'');
?>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>Plan lekcji - <?php echo $ns[1]['wartosc']; ?></title>
        <?php echo $script ?>
        <link rel="stylesheet" type="text/css" href="<?php echo URL::base() ?>lib/css/style.css"/>
    </head>
    <body>
        <div id="main">
            <div id="top">
                <img src="<?php echo URL::base() ?>lib/images/logo.png" alt="<?php echo $ns[1]['wartosc']; ?>"
                     style="height: 70px;"/>
            </div>
            <hr/>
            <div id="middle">
                <div id="menu">
                    <?php
                    if (!isset($_SESSION['valid']) || !isset($_COOKIE['PHPSESSID'])) {
                        ?>
                        <?php
                        if ($reg[1]['wartosc'] == 1):
                            ?>
                            <p class="info">System będzie niedostępny, dopóki opcja edycji systemu
                                będzie <b>włączona</b>.</p>
                            <?php
                        else:
                            ?>
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
                                    <li><a target="_blank" href="<?php echo URL::site('podglad/nauczyciel/' . $rc['imie_naz']); ?>"><?php echo $rc['imie_naz']; ?></a></li>
                                <?php endforeach; ?>    
                            </ul>
                        <?php
                        endif;
                    } else {
                        ?>
                        <p>Witaj, <b><?php echo $_COOKIE['login']; ?></b>!
                            <a href="<?php echo URL::site('admin/logout'); ?>">[ wyloguj ]</a>
                        <ul>
                            <li><a href="<?php echo URL::site('admin/haslo'); ?>">[ zmiana hasła ]</a></li>
                            <li><a href="<?php echo URL::site('admin/zmiendane'); ?>">[ zmiana danych szkoły ]</a></li>
                            <li><a style="color:red;" href="<?php echo url::site('admin/reset'); ?>">[ resetowanie systemu ]</a></li>
                        </ul>


                        </p>
                        <?php if ($reg[1]['wartosc'] == 1): ?>
                            <hr/>
                            <h3>Menu administratora</h3>
                            <ul id="menu_js">
                                <li>
                                    <a href="<?php echo URL::site('sale/index'); ?>">Przegląd sal</a>
                                </li>
                                </li>
                                <li><a href="<?php echo URL::site('przedmioty/index'); ?>">Przegląd przedmiotów</a></li>
                                <li><a href="<?php echo URL::site('nauczyciele/index'); ?>">Przegląd nauczycieli</a></li>
                                <li><a href="<?php echo URL::site('klasy/index'); ?>">Przegląd klas</a></li>
                                <li>
                                    <a href="<?php echo URL::site('godziny/index'); ?>">Ustawienia godzin lekcyjnych</a>
                                </li>
                                <li><b><a href="<?php echo URL::site('admin/zamknij'); ?>">Zamknij edycję</a></b></li>
                            </ul>
                            <p class="info">Dopóki nie zamkniesz edycji danych, nie możesz tworzyć planów.
                                Zamknięcie edycji oznacza <b>brak możliwości</b> ponownej edycji danych.</p>
                        <?php else: ?>
                            <p class="info">Tryb edycji <b>wyłączony</b>. System planów jest <b>aktywny</b>.</p>
                            <hr/>
                            <h3>Edycja planów</h3>
                            <ul>
                                <li><b><a href="<?php echo URL::site('plan/grupy'); ?>">[ lekcja z podziałem na grupy ]</a></b></li>
                                <?php foreach ($isf->DbSelect('klasy', array('klasa'), 'order by klasa asc') as $r => $c): ?>
                                    <li><a href="<?php echo URL::site('plan/edycja/' . $c['klasa']); ?>" target="_blank"><?php echo $c['klasa']; ?></a></li>
                                <?php endforeach; ?> 
                            </ul>
                            <hr/>
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
                                    <li><a href="<?php echo URL::site('podglad/nauczyciel/' . $rc['imie_naz']); ?>" target="_blank"><?php echo $rc['imie_naz']; ?></a></li>
                                <?php endforeach; ?>    
                            </ul>
                        <?php endif; ?>
                    <?php } ?>
                </div>
                <div id="content">
                    <?php echo $content; ?>
                </div>
            </div>
            <br style="clear:both;"/>
            <br/>
            <hr/>
            <div id="foot">
                <p><b>Plan lekcji</b> - <?php echo $ns[1]['wartosc']; ?></p>
            </div>
        </div>
    </body>
</html>