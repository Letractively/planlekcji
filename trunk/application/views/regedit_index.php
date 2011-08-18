<?php
$isf = new Kohana_Isf();
$isf->DbConnect();
?>
<table>
    <thead style="text-align: center;">
        <tr style="background-color: tan">
            <td colspan="2">
                Zarządzanie rejestrem systemowym
            </td>
        </tr>
        <tr style="background-color: darkgray">
            <td>Klucz</td>
            <td>Obecne ustawienie</td>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($isf->DbSelect('rejestr', array('*')) as $rowid => $rowcol): ?>
            <tr>
                <td>
                    <b><?php echo $rowcol['opcja']; ?></b>
                </td>
                <td>
                    <?php echo htmlspecialchars($rowcol['wartosc']); ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<br/>