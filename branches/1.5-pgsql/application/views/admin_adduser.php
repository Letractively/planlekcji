<?php
$isf = new Kohana_Isf();
$isf->DbConnect();
$uid = $isf->DbSelect('uzytkownicy', array('*'), 'order by uid desc');
$uid = $uid[1]['uid']+1;
?>
<h1>Tworzenie nowego użytkownika</h1>
<form action="<?php echo URL::site('admin/douseradd'); ?>" method="post">
    <p>Login: <input type="text" name="inpLogin"/></p>
    <p>Hasło: <input type="text" name="inpHaslo"/></p>
    <input type="hidden" name="inpUid" value="<?php echo $uid; ?>"/>
    <button type="submit" name="btnSubmit">Dodaj użytkownika</button>
</form>
<?php if($err=='data'): ?>
<p class="error">Login zawiera niedozwolone znaki</p>
<?php endif; ?>
<?php if($err=='leng'): ?>
<p class="error">Login musi zawierać min. 5 znaków, a hasło min. 6</p>
<?php endif; ?>
