<?php
/* 
à remplir :
    > name des input (avec nom du field)
    > les value $data[] (sauf pour textarea)
*/
?>

<?php
/* Preview Google Maps pour l'admin avec largeur/hauteur fixes */

$address = $data['address'] ?? '';
$source = "https://maps.google.com/maps?width=100%&height=100%&hl=en&q=" . urlencode($address) . "&t=&z=14&ie=UTF8&iwloc=B&output=embed";

// Valeurs fixes pour le preview
$mapHeight = '200px';
$mapWidth  = '200px';
?>

<div class="flex-row wrap">
    <div class="flex-column">
        <strong><?= $this->display_name ?></strong>
        
        <input type="text" name="address" value="<?= $address ?>" placeholder="Adresse">
        <input type="text" name="width" value="<?= $data['width'] ?? '' ?>" placeholder="Largeur">
        <input type="text" name="height" value="<?= $data['height'] ?? '' ?>" placeholder="Hauteur">
    </div>

    <div class="gmaps-preview">
        <div class="mapouter container">
            <div class="gmap_canvas">
                <iframe class="gmap_iframe" width="100%" height="100%" frameborder="0" scrolling="no" marginheight="0" marginwidth="0"
                        src="<?= $source ?>"></iframe>
            </div>
            <style>
                .mapouter {
                    position: relative;
                    text-align: right;
                    width: <?= $mapWidth ?>;
                    height: <?= $mapHeight ?>;
                }
                .gmap_canvas {
                    overflow: hidden;
                    background: none !important;
                    width: <?= $mapWidth ?>;
                    height: <?= $mapHeight ?>;
                }
                .gmap_iframe {
                    width: <?= $mapWidth ?>;
                    height: <?= $mapHeight ?> !important;
                }
            </style>
        </div>
    </div>
</div>

<?php

/* SNIPPETS & TIPS

<?= $this->display_name ?>

<?= $data['field'] ?? '' ?>

<input type="text" name="NOM_DU_FIELD" value="<?= $data['title'] ?? '' ?>" placeholder="Titre">
<textarea type="text" name="NOM_DU_FIELD" placeholder="Slogan"><?= $data['slogan'] ?? '' ?></textarea>

> IMPORT D'IMAGES
bien penser à :
    class="block-field"
    data-name="field de la classe"

<div class="block-field" data-name="background">
    <label for="image">Background</label>
    <button type="button" class="button select-media">Choisir une image</button>

    <div class="preview-container"></div>
</div>

*/