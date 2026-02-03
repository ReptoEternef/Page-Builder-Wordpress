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
            <b>Titre</b>
            <textarea class="wysiwyg-h2" name="title" rows="1"></textarea>

            <b>Contenu</b>
            <textarea class="wysiwyg" name="content" rows="8"></textarea>
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
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Pour les WYSIWYG :
class="wysiwyg" ou wysiwyg-h2/h3 etc... -->