<div class="flex-column wrap inner-<?= $this->type ?>">
    <strong><?= $this->display_name ?></strong>
    <div class="flex-row">

        <?php // ----- LAYOUTS DROPDOWN -------
        obwp_dropdown($this, 'layout');
        ?>
        
        <div class="obwp-input-ctn">
            <input type="text" name="title" value="<?= $data['title'] ?? '' ?>" placeholder="Titre">
            <textarea type="text" name="slogan" placeholder="Slogan"><?= $data['slogan'] ?? '' ?></textarea>
        </div>
    </div>
    
    <div class="block-field" data-name="background">
        <label for="image">Background</label>
        <button type="button" class="button select-media">Choisir une image</button>

        <div class="preview-container"></div>
    </div>

</div>


<!-- SNIPPETS

à remplir :
    > name des input (avec nom du field)
    > les value $data[] (sauf pour textarea)
    > dropdown : si autre que layout, bien mettre <option value="" disabled selected>-- Choisissez une option --</option>

<input type="text" name="title" value="$data['title'] ?? '' " placeholder="Titre">
<textarea type="text" name="slogan" placeholder="Slogan">$data['slogan'] ?? '' </textarea>

Image import :
    <div class="block-field" data-name="image_input_name">
        <label for="image">Background</label>
        <button type="button" class="button select-media">Choisir une image</button>

        <div class="preview-container"></div>
    </div>

-->