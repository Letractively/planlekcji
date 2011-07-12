<?php
$isf = new Kohana_Isf();
$isf->DbConnect();
?>
<h1>Podgląd rejestru systetmowego</h1>
<p class="error">Modyfikacja rejestru grozi nieprawidłowym działaniem systemu!</p>
<form action="<?php echo URL::site('regedit/zmien'); ?>" method="post">
    <table class="przed">
        <thead style="background: #ff6666">
            <tr>
                <td>Klucz</td>
                <td>Wartość</td>
                <td>Obecne ustawienie</td>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($isf->DbSelect('rejestr', array('*')) as $rowid => $rowcol): ?>
                <tr>
                    <td>
                        <?php echo $rowcol['opcja']; ?>
                    </td>
                    <td>
                        <input type="text" name="<?php echo $rowcol['opcja']; ?>" value="<?php echo htmlspecialchars($rowcol['wartosc']); ?>"/>
                    </td>
                    <td>
                        <?php echo htmlspecialchars($rowcol['wartosc']); ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <br/>
    <button type="submit" name="btnSubmit">Zapisz zmiany</button>
</form>