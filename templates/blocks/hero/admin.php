<div class="flex-column wrap">
    <div class="flex-row" style="<?= $data['custom_css'] ?? '' ?>">
        <strong><?= $this->display_name ?></strong>
        
        <input type="text" name="title" value="<?= $data['title'] ?? '' ?>" placeholder="Titre">
        <input type="text" name="slogan" value="<?= $data['slogan'] ?? '' ?>" placeholder="Slogan">    
    </div>
    
    <div class="block-field">
        <label for="image">Background</label>
        <input type="text" hidden class="hero-image" name="image" value="" placeholder="image">
        <button type="button" class="button select-media" data-target=".hero-image">Choisir une image</button>
        <img class="preview-image" src="" name="image" style="max-width:200px; display:none;">
    </div>
</div>
