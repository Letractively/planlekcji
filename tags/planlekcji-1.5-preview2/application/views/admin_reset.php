<?php
/*
 * Resetowanie systemu Plan Lekcji
 * 
 * 
 */
?>
<h1>Resetowanie systemu Plan lekcji</h1>
<p>Jest to operacja polegająca na częściowym, bądź całkowitym usunięciu
wprowadzonych dancyh.</p>
<p class="error">
    Jest to operacja nieodwracalna!
</p>
<form action="<?php echo url::site('admin/doreset'); ?>" method="post">
    <p><b>Zostaną usunięte dane z następujących elementów:</b></p>
    <ul>
        <li>Dane planów zajęć</li>
        <li><b>Usuń także:</b>
            <ul>
                <li>zdefiniowane sale</li>
                <li>zdefiniowani nauczyciele</li>
                <li>zdefiniowane klasy</li>
                <li>zdefiniowane godziny dzwonków</li>
            </ul>
            <p>
                <b>Chcę usunąć powyższe elementy:</b> <input type="checkbox" name="cl"/></li>
            </p>
    </ul>
    <button type="submit" name="btnSubmit">Zrozumiałem i chcę usunąć dane</button>
</form>
<p class="error">Nastąpi powrót do systemu edycji danych, takich jak sale, przedmioty, itd.</p>