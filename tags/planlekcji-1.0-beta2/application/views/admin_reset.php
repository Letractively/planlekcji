<h1>Resetowanie systemu Plan lekcji</h1>
<p>Jest to operacja polegająca na częściowym, bądź całkowitym usunięciu
wprowadzonych dancyh.</p>
<p style="font-weight: bold; color: red;">
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
            <b>Chcę usunąć powyższe elementy:</b> <input type="checkbox" name="cl"/></li>
    </ul>
    <button type="submit" name="btnSubmit">Zrozumiałem i chcę usunąć dane</button>
</form>