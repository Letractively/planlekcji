<?php
$isf = new Kohana_Isf();
$isf->DbConnect();
$zast = $isf->DbSelect('zast_id', array('*'), 'order by dzien desc');
$ile = count($zast);
$enpl_days = array(
    'Monday' => 'Poniedziałek',
    'Tuesday' => 'Wtorek',
    'Wednesday' => 'Środa',
    'Thursday' => 'Czwartek',
    'Friday' => 'Piątek',
    'Saturday' => 'Sobota',
    'Sunday' => 'Niedziela',
);
?>
<h1>
    Zastępstwa
</h1>
<p>
    <font class="notice">█ nadchodzące</font>&emsp;
    <font class="error">█ minione</font>
</p>
<table class="przed">
    <thead style="background: #6699ff">
        <tr>
            <td>Data</td>
            <td>Za nauczyciela</td>
            <td>Dodatkowe informacje</td>
            <td></td>
        </tr>
    </thead>
    <?php if ($ile == 0): ?>
        <tr>
            <td colspan="4">
                <p class="info" style="text-align: center">
                    Brak zastępstw
                </p>
            </td>
        </tr>
    <?php else: ?>
        <?php foreach ($zast as $rowid => $rowcol): ?>
            <tr>
                <td>
                    <?php
                    $today = date('Y-m-d');
                    if ($rowcol['dzien'] >= $today) {
                        echo '<font class="notice">█</font>';
                    } else {
                        echo '<font class="error">█</font>';
                    }
                    echo ' ' . $rowcol['dzien'];
                    $day = date('l', strtotime($rowcol['dzien']));
                    echo ' (' . $enpl_days[$day] . ')';
                    ?>

                </td>
                <td><?php echo $rowcol['za_nl']; ?></td>
                <td><?php echo $rowcol['info']; ?></td>
                <td>
                    &nbsp;&emsp;<a href="<?php echo URL::site('zastepstwa/przeglad/' . $rowcol['zast_id']); ?>">[otwórz]</a>&emsp;&nbsp;
                    <?php if(isset($_COOKIE['PHPSESSID'])&&isset($_COOKIE['login'])): ?>
                    &nbsp;&emsp;<a href="#" onClick="confirmation(<?php echo $rowcol['zast_id']; ?>)">[usuń]</a>&emsp;&nbsp;
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
</table>
<script type="text/javascript">
function confirmation(n){
    var answer = confirm("Czy chcesz usunąć zastępstwo nr "+n);
    if(answer){
        window.location = "<?php echo URL::site('zastepstwa/usun');?>/"+n;
    }else{
    }
}
</script>