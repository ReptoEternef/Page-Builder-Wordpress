<?php
/* 
à remplir :
    > name des input (avec nom du field)
    > les value $data[] (sauf pour textarea)
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
        
        <input type="text" name="title" value="<?= $data['title'] ?? '' ?>" placeholder="Titre">
        <textarea type="text" name="slogan" placeholder="Slogan"><?= $data['slogan'] ?? '' ?></textarea>
    </div>
    
    <div class="block-field" data-name="background">
        <label for="image">Background</label>
        <button type="button" class="button select-media">Choisir une image</button>

        <div class="preview-container"></div>
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