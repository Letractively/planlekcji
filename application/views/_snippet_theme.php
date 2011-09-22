<?php
if(!preg_match('/index.php/', $_SERVER['REQUEST_URI'])){
    $site = '';
}else{
    $site = preg_replace('/(.*?)\/index.php\//e', '', $_SERVER['REQUEST_URI']);
}
?>
<form action="<?php echo URL::site('default/look'); ?>" method="post" onchange="document.forms['lookf'].submit();" id="lookf" name="lookf">
    <span style="font-size: 8pt">Motyw</span>
    <select name="look" style="font-size: 8pt;">
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
    <input type="hidden" name="site" value="<?php echo $site; ?>"/>
</form>