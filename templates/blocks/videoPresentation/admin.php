<?php
/* 
à remplir :
    > name des input (avec nom du field)
    > les value $data[] (sauf pour textarea)
    > dropdown : si autre que layout, bien mettre <option value="" disabled selected>-- Choisissez une option --</option>
*/
?>

<div class="flex-column wrap">
    <strong><?= $this->display_name ?></strong>
    <div class="flex-row">

        <?php // ----- LAYOUTS DROPDOWN -------
        if (count($this->layouts) > 1) {
            ?>
            <!-- <label for="layout">Layout :</label> -->
            <select name="layout" id="">
                <?php
                if ($this->layouts) {
                    foreach ($this->layouts as $layout) {
                        ?> <option value="<?= esc_attr($layout) ?>" id=""><?= esc_attr($layout) ?></option> <?php
                    }
                }
                ?>
            </select>
            <?php
        } // ----------------------------------
        ?>
        
    </div>


    <div class="video-links">
        <input type="text" name="video_link_1" value="<?= $data['video_link_1'] ?? '' ?>" placeholder="lien vidéo 1">
        <input type="text" name="video_link_2" value="<?= $data['video_link_2'] ?? '' ?>" placeholder="lien vidéo 2">
        <input type="text" name="video_link_3" value="<?= $data['video_link_3'] ?? '' ?>" placeholder="lien vidéo 3">
        <input type="text" name="video_link_4" value="<?= $data['video_link_4'] ?? '' ?>" placeholder="lien vidéo 4">
        <input type="text" name="video_link_5" value="<?= $data['video_link_5'] ?? '' ?>" placeholder="lien vidéo 5">
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