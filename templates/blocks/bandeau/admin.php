<?php
/* 
à remplir :
    > name des input (avec nom du field)
    > les value $data[] (sauf pour textarea)
    > dropdown : si autre que layout, bien mettre <option value="" disabled selected>-- Choisissez une option --</option>
*/
?>

<div class="flex-column wrap inner-<?= $this->type ?>">
    <strong><?= $this->display_name ?></strong>
    <div class="">

        <div class="obwp-options">
            <?php obwp_dropdown($this, 'layout'); ?>
        </div>
        
        <input type="text" name="custom_css" value="<?= $data['custom_css'] ?? '' ?>" placeholder="Custom CSS">
        
        <?php echo createInput($data ?? '', 'text', 'title', 'Titre') ?>
        <?php echo createInput($data ?? '', 'textarea', 'subtitle', 'Sous-titre') ?>
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