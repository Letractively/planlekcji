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
        <?php if (Kohana_Isf::factory()->detect_ie()): ?>
            <button type="submit" name="btnLookSubmit">Zmień styl</button>
        <?php endif; ?>
        <input type="hidden" name="site" value="<?php echo str_replace('index.php/', '', $_SERVER['REQUEST_URI']); ?>"/>
    </form>
    <div id="foot">
        <b>Plan lekcji </b><?php echo App_Globals::getRegistryKey('app_ver'); ?> - <?php echo App_Globals::getRegistryKey('nazwa_szkoly'); ?> |
        <a href="http://planlekcji.googlecode.com" target="_blank">strona projektu Plan Lekcji</a>
    </div>
</div>