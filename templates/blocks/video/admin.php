<?php
/* 
à remplir :
    > name des input (avec nom du field)
    > les value $data[] (sauf pour textarea)
*/
?>

<div class="flex-column wrap">
    <strong><?= $this->display_name ?></strong>
    <div>
        <label for="">Lien</label>
        <input type="text" class="video-encoder" name="video_link" value="<?= $data['video_link'] ?? '' ?>" placeholder="Lien de la vidéo">
    </div>
    <div class="">
        <label for="">Largeur</label>
        <input type="text" class="video-encoder" name="video_width" value="<?= $data['video_width'] ?? '' ?>" placeholder="Largeur de la vidéo">
        <label for="">Hauteur</label>
        <input type="text" class="video-encoder" name="video_height" value="<?= $data['video_height'] ?? '' ?>" placeholder="Hauteur de la vidéo">
    </div>
</div>



<?php
// Enqueue JS to encode



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