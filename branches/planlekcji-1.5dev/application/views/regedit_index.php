<?php
$isf = new Kohana_Isf();
$isf->DbConnect();
?>
<h1>Podgląd rejestru systetmowego</h1>
<table class="przed">
    <thead style="background: #ff6666">
        <tr>
            <td>Klucz</td>
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
                    <?php echo htmlspecialchars($rowcol['wartosc']); ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<br/>