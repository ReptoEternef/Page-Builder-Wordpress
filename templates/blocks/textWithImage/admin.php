<div class="flex-row wrap center" data-order="">
    <div class="flex-column" style="<?= $data['custom_css'] ?? '' ?>">
        <strong><?= $this->display_name ?></strong>
        <div class="flex-column" style="width: 100%;">
            <input type="text" name="title" value="<?= $data['title'] ?? '' ?>" style="width: 100%;" placeholder="Titre">
            <textarea name="content" rows="4" placeholder="content"><?= $data['content'] ?? '' ?></textarea>
        </div>
    </div>
    <div class="flex-column">
        
        <div class="block-field">
            <label for="image">Image</label>
            <input type="text" hidden class="hero-image" name="image" value="" placeholder="Lien de l'image">
            <button type="button" class="button select-media" data-target=".hero-image">Choisir une image</button>
            <img class="preview-image" name="image" src="" style="max-width:200px; display:none;">
        </div>


    </div>
</div>