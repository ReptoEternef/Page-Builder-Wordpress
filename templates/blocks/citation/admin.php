<div class="obwp-block-admin">
    <div class="obwp-block-header">
        <strong class="obwp-block-title"><?= $this->display_name ?></strong>
    </div>
    
    <div class="obwp-block-body">

        <!-- Options système (layouts, contexte couleur, etc.) -->
        <?php if (!empty($this->layouts) || in_array('color_context', $this->fields)): ?>
        <div class="obwp-system-options">
            <?php 
            obwp_dropdown($this, 'layout');
            obwp_dropdown($this, 'color_context');
            ?>
        </div>
        <?php endif; ?>
        
        <!-- Champs de contenu principal -->
        <div class="obwp-content-fields">
            <textarea type="text" name="citation" placeholder="citation"><?= $data['citation'] ?? '' ?></textarea>
            <input type="text" name="source" value="<?= $data['source'] ?? '' ?>" placeholder="Titre">
            <input type="text" name="link" value="<?= $data['link'] ?? '' ?>" placeholder="Lien">
            <input type="text" name="link_text" value="<?= $data['link_text'] ?? '' ?>" placeholder="Texte du lien">
        </div>
        
        <!-- Champs médias (images, vidéos) -->
        <?php if (in_array('background', $this->fields) || in_array('image', $this->fields)): ?>
            <div class="block-field" data-name="image_input_name">
                <label for="image">Background</label>
                <button type="button" class="button select-media">Choisir une image</button>

                <div class="preview-container"></div>
            </div>
        <?php endif; ?>
        
        <!-- Custom CSS (toujours en dernier) -->
        <?php if (in_array('custom_css', $this->fields)): ?>
        <div class="obwp-advanced-options">
            <input type="text" name="custom_css" placeholder="Custom CSS" class="obwp-input-full">
        </div>
        <?php endif; ?>
        
        <!-- Full Width Option -->
        <?php if (in_array('full-width', $this->fields)): ?>
        <div class="obwp-full-width-option">
            <label class="obwp-checkbox-label">
                <input type="checkbox" name="full-width">
                <span class="prevent-select change-cursor">Pleine largeur</span>
            </label>
        </div>
        <?php endif; ?>
    </div>
</div>

<!--
Pour les WYSIWYG :
class="wysiwyg" ou wysiwyg-h2/h3 etc...

Pour les champs dynamiques :
    <?php //add_field_btn('input', 'video_link', 'Lien vidéo', 'Ajouter vidéo', 'notrad') ?>
    <div class="added-fields">
    </div>
-->