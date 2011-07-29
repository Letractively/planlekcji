<?php
$isf = new Kohana_Isf();
$isf->DbConnect();
?>
<h1>Zarządzanie użytkownikami</h1>
<h3><a href="<?php echo URL::site('admin/adduser'); ?>">Dodaj użytkownika</a></h3>
<?php $res = $isf->DbSelect('uzytkownicy', array('*'), 'where login != \'root\''); ?>
<table class="przed">
    <thead style="background: #ABC6DD">
        <tr>
            <td>ID</td>
            <td>login (dostępne tokeny)</td>
            <td>Stan konta</td>
            <td>Zalogowany</td>
            <td>Akcja</td>
        </tr>
    </thead>
    <?php if (count($res) == 0): ?>
        <tr>
            <td colspan="5"><p class="error" style="text-align: center; text-decoration: blink;">Brak użytkowników w bazie</p></td>
        </tr>
    <?php endif; ?>
    <?php foreach ($res as $rowid => $rowcol): ?>
        <tr>
            <td><?php echo $rowcol['uid']; ?></td>
            <td><?php echo $rowcol['login']; ?> (<?php echo count($isf->DbSelect('tokeny', array('*'), 'where login=\''.$rowcol['login'].'\'')); ?>)</td>
            <?php if ($rowcol['ilosc_prob'] >= 3): ?>
                <td><p class="error">█ zablokowany</p></td>
            <?php else: ?>
                <td><p class="notice">█ aktywny</p></td>
            <?php endif; ?>
            <?php if ($rowcol['webapi_timestamp'] < time()): ?>
                <td><p class="error">█ niezalogowany</p></td>
            <?php else: ?>
                <td><p class="notice">█ zalogowany</p></td>
            <?php endif; ?>
            <td>
                <a href="#" onClick="deluser(<?php echo $rowcol['uid']; ?>);">usuń</a>&emsp;
                <?php if ($rowcol['ilosc_prob'] > 3): ?>
                    <a href="<?php echo URL::site('admin/userdel/' . $rowcol['uid']); ?>" class="error">odblokuj</a>&emsp;
                <?php endif; ?>
                <a href="<?php echo URL::site('admin/token/' . $rowcol['uid']); ?>" target="_blank">generuj tokeny</a>
            </td>
        </tr>
    <?php endforeach; ?>
</table>
<?php if (count($res) == 0): ?>
<p class="info">Dopóki nie utworzysz innych użytkowników, dostęp do edycji planu lekcji i zastępstw będzie
    niedostępny. Użytkonik <b>root</b> nie ma dostępu do tej części aplikacji.</p>
<?php endif; ?>
<script type="text/javascript">
    function deluser(n){
        var answer = confirm("Czy napewno chcesz usunąć użytkownika nr "+n+"?");
        if(answer){
            document.location.href = "<?php echo URL::site('admin/userdel'); ?>/"+n+"";
        }else{
        }
    }
</script>
