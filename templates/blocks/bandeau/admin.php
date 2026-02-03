<div class="obwp-block-admin">
    <div class="obwp-block-header">
        <strong class="obwp-block-title"><?= $this->display_name ?></strong>
    </div>
    
    <div class="obwp-block-body">

        <!-- Options système -->
        <?php if (!empty($this->layouts) && count($this->layouts) > 1): ?>
        <div class="obwp-system-options">
            <?php obwp_dropdown($this, 'layout'); ?>
        </div>
        <?php endif; ?>
        
        <!-- Champs de contenu -->
        <div class="obwp-content-fields">
            <div class="obwp-field-row">

                <div class="obwp-field-col">
                    <input type="text" name="title" placeholder="Titre">
                    <textarea name="subtitle" placeholder="Sous-titre"></textarea>
                </div>

            </div>
        </div>
        
        <!-- Champs médias -->
        <div class="block-field" data-name="background">
            <label class="obwp-label">Image de fond</label>
            <button type="button" class="button select-media">Choisir une image</button>
            <div class="preview-container"></div>
        </div>
<!--         <div class="obwp-media-fields">
        </div>

        <div class="block-field" data-name="background">
            <label for="image">Background</label>
            <button type="button" class="button select-media">Choisir une image</button>
            <div class="preview-container"></div>
        </div> -->
        
        <!-- Custom CSS -->
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