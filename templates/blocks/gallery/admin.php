<div class="flex-column wrap">
    <strong><?= $this->display_name ?></strong>

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

    <input type="text" name="custom_css" value="<?= $data['custom_css'] ?? ''?>" placeholder="Custom CSS">
    <label for="display_desc">Afficher la description</label>
    <input type="checkbox" name="display_desc" id="">

    <div class="block-field" data-multiple="true" data-name="gallery">
        <label for="image">Image</label>
        <!-- <input type="text" data-multiple="true" hidden class="hero-image" name="image" value="" placeholder="Lien de l'image"> -->
        <button type="button" data-multiple="true" class="button select-media">Choisir une image</button>
        
        <div class="preview-container"></div>
    </div>
</div>