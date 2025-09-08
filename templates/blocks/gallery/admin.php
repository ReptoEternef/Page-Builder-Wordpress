<div class="flex-column wrap">
        <strong><?= $this->display_name ?></strong>

        <input type="text" name="custom_css" value="<?= $data['custom_css'] ?? ''?>" placeholder="Custom CSS">

        <div class="block-field" data-multiple="true" data-name="gallery">
            <label for="image">Image</label>
            <!-- <input type="text" data-multiple="true" hidden class="hero-image" name="image" value="" placeholder="Lien de l'image"> -->
            <button type="button" data-multiple="true" class="button select-media">Choisir une image</button>
            
            <div class="preview-container"></div>
        </div>
</div>