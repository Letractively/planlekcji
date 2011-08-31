<?php $isf = new Kohana_Isf(); ?>
<?php $isf->DbConnect(); ?>
<p/>
<div class="ui-state-highlight ui-corner-all" style="margin-bottom: 10px; padding: 0pt 0.7em; max-width: 100%;">
    <p class="info">
        <span class="ui-icon ui-icon-info" style="float: left; margin-right: 0.3em;"></span>
        System zastępstw będzie dostępny po zamknięciu edycji planów zajęć</p>
</div>

<table border="0" width="100%">
    <thead class="a_even" style="text-align: center;">
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
        <tr valign="center">
            <td class="a_even" style="text-align: center;"><b><?php echo $c['klasa']; ?></b></td>
            <td>
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
