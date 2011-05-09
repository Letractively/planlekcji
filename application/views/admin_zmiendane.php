<?php $isf = new Kohana_Isf(); ?>
<?php $isf->DbConnect(); //połączenie z bazą   ?>
<?php
$nazwa = $isf->DbSelect('rejestr', array('*'), 'where opcja="nazwa_szkoly"');
$msg = $isf->DbSelect('rejestr', array('*'), 'where opcja="index_text"');
?>
<h1>Zmiana danych systemu Plan Lekcji</h1>
<form action="<?php echo URL::site('admin/dochange'); ?>" method="post">
    <h3>Nazwa szkoły: <input style="width: 100%" type="text" name="inpNazwa" value="<?php echo $nazwa[1]['wartosc']; ?>"/></h3>
    <h3>Wiadomość na stronie głównej</h3>
    <p>
        <textarea name="txtMsg" style="width: 100%" rows="50">
            <?php echo $msg[1]['wartosc']; ?>
        </textarea>
    </p>
    <button type="submit" name="btnSubmit">Zmień dane</button>
</form>