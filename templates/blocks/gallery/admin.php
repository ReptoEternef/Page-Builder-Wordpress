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
        
        <!-- Champs médias (images, vidéos) -->
        <div class="block-field" data-name="gallery" data-multiple="true">
            <label for="image">Galerie</label>
            <button type="button" class="button select-media">Choisir des images</button>

            <div class="preview-container"></div>
        </div>
        
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
            <label for="obwp-checkbox-label">
                <input type="checkbox" name="display_desc">
                <span class="prevent-select change-cursor">Afficher la description</span>
            </label>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Pour les WYSIWYG :
class="wysiwyg" ou wysiwyg-h2/h3 etc... -->