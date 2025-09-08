<div class="flex-row wrap center" data-order="">
    <div class="flex-column" style="<?= $data['custom_css'] ?? '' ?>">
        <strong><?= $this->display_name ?></strong>
        <div class="flex-column" style="width: 100%;">
            <input type="text" name="title" value="<?= $data['title'] ?? '' ?>" style="width: 100%;" placeholder="Titre">
            <textarea name="content" rows="4" placeholder="content"><?= $data['content'] ?? '' ?></textarea>
        </div>
    </div>
    <div class="flex-column">
        
        <div class="block-field" data-name="image">
            <label for="image">Image</label>
            <button type="button" class="button select-media">Choisir une image</button>
            
            <div class="preview-container"></div>
        </div>


    </div>
</div>