<style>
    @import url('<?php echo URL::base() . 'lib/css/style.css'; ?>');
</style>
<div id="error" class="tableDiv">
    <div class="tableRow">
	<div class="tableCell CTError">
	    Wystąpił błąd systemu
	</div>
    </div>
    <div class="tableRow CTErrorMessage">
	<p>Kod błędu: <span class="CTErrorText"><?php echo $code; ?></span></p>
	<p>Treść: <span class="CTErrorText"><?php echo $message; ?></span></p>
    </div>
</div>