<?php
$isf = new Kohana_Isf();
$isf->DbConnect();
$ile = count($isf->DbSelect('log', array('*')));
$ile = ceil($ile / 15);
$offset = 15 * ($page - 1);
$limit = 15;
$res = $isf->DbSelect('log', array('*'), 'order by id desc limit ' . $limit . ' offset ' . $offset);
?>
<table style="width: 100%;">
    <thead>
        <tr>
            <td colspan="4" style="background: tan; text-align: center;">
                <a href="<?php echo URL::site('admin/dellogs'); ?>">
                    Usuń wszystkie logi
                </a>
            </td>
        </tr>
        <tr style="background-color: darkgray;">
            <td>ID</td>
            <td>Data</td>
            <td>Moduł</td>
            <td></td>
        </tr>
    </thead>
    <?php foreach ($res as $rowid => $rowcol): ?>
        <tr>
            <td><?php echo $rowcol['id']; ?></td>
            <td><?php echo $rowcol['data']; ?></td>
            <td><?php echo $rowcol['modul']; ?></td>
            <td><i><?php echo $rowcol['wiadomosc']; ?></i></td>
        </tr>
    <?php endforeach; ?>
    <?php if ($ile == 0): ?>
        <tr><td colspan="4"><i>Brak dzienników aplikacji</i></td></tr>
    <?php endif; ?>
    <tr>
        <td colspan="4" style="text-align: center; background-color: tan;">
            <p></p>
            <p class="grplek">
                <b>Strona: </b>
                <?php for ($i = 1; $i <= $ile; $i++): ?>
                    <?php if ($page == $i): ?>
                        <?php echo $i; ?>&emsp;
                    <?php else: ?>
                        <a href="<?php echo URL::site('admin/logs/' . $i); ?>"><?php echo $i; ?></a>&emsp;
                    <?php endif; ?>
                <?php endfor; ?>
                <?php if ($ile == 0): ?>
                    <i>brak stron</i>
                <?php endif; ?>
            </p>
        </td>
    </tr>
</table>