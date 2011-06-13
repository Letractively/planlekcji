<?php
/*
 * Resetowanie systemu Plan Lekcji
 */
?>
<h1>Resetowanie systemu Plan lekcji</h1>
<p>Jest to operacja polegająca na usunięciu wszystkich danych, w zależności
    od dokonanego wyboru</p>
<h3>&bull; Usuń tylko plany zajęć oraz zastępstwa</h3>
<p class="info">
    Ta opcja różni się od poniższej powrotem do edycji planów zajęć
<form action="<?php echo url::site('admin/planreset'); ?>" method="post">
    <button type="submit" name="btnSubmit">Usuń tylko dane planów zajęć i zastępstw</button>
</form>
</p>
<h3>&bull; Czyszczenie danych systemowych</h3>
<p class="info">Opcja spowoduje usunięcie danych i powrót do edycji systemu</p>
<form action="<?php echo url::site('admin/doreset'); ?>" method="post">
    <p><b>Elementy do usunięcia</b></p>
    <ul>
        <li>Dane planów zajęć</li>
    </ul>
    <p>
        <b>Opcjonalne elementy do usunięcia</b>&emsp;
        <input type="checkbox" name="cl"/>
    </p>
    <ul>
        <li><b>Usuń także:</b>
            <ul>
                <li>zdefiniowane sale</li>
                <li>zdefiniowani nauczyciele</li>
                <li>zdefiniowane klasy</li>
                <li>zdefiniowane godziny dzwonków</li>
            </ul>
        </li>
        </p>
    </ul>
    <button type="submit" name="btnSubmit">Usuń trwale dane systemu</button>
</form>