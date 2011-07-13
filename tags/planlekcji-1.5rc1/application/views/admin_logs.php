<?php
$isf = new Kohana_Isf();
$isf->DbConnect();
$ile = count($isf->DbSelect('log', array('*')));
$ile = ceil($ile / 15);
$offset = 15 * ($page - 1);
$limit = 15;
$res = $isf->DbSelect('log', array('*'), 'order by id desc limit ' . $limit . ' offset ' . $offset);
?>
<h1>Podgląd dziennika systemowego</h1>
<table class="przed">
    <thead style="background: #88d20b">
        <tr>
            <td>ID</td>
            <td>Data</td>
            <td>Moduł</td>
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
</table>
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
</p>