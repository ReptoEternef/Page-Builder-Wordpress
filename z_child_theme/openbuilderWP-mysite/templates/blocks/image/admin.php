<div class="flex-column wrap inner-<?= $this->type ?>">
    <strong><?= $this->display_name ?></strong>
    <div class="flex-row">

        <?php // ----- LAYOUTS DROPDOWN -------
        layoutsDropdown($this);
        ?>
        
        <div class="obwp-input-ctn">
            <input type="text" name="custom_css" data-name="<?php $data['custom_css'] ?>" placeholder="Custom CSS">
            <input type="text" name="height" data-name="<?php $data['height'] ?>" placeholder="Hauteur (en px)">
            <input type="text" name="alt" data-name="<?php $data['alt'] ?>" placeholder="Texte alternatif">
        </div>
    </div>
    
    <div class="block-field" data-name="image">
        <label for="image">Image</label>
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