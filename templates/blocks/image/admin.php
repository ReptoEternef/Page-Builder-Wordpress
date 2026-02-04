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
                    <input type="text" name="alt" placeholder="Texte alternatif (important pour l'accessibilité)">
                    <input type="text" name="caption" placeholder="Légende de l'image">
                    <input type="text" name="link" placeholder="Lien (optionnel)">

                    <!-- Champ média -->
                    <div class="obwp-media-fields">
                        <div class="block-field" data-name="image">
                            <label class="obwp-label">Image</label>
                            <button type="button" class="button select-media">Choisir une image</button>
                            <div class="preview-container"></div>
                        </div>
                    </div>
                </div>
                
                <div class="obwp-field-col">
                    <label for="alignment">Alignement</label>
                    <select name="alignment">
                        <option value="center">Centré</option>
                        <option value="left">Gauche</option>
                        <option value="right">Droite</option>
                    </select>
                    
                    <label for="object_fit">Object Fit</label>
                    <select name="object_fit">
                        <option value="cover">Cover (remplir)</option>
                        <option value="contain">Contain (contenir)</option>
                        <option value="fill">Fill (étirer)</option>
                        <option value="none">None (taille réelle)</option>
                        <option value="scale-down">Scale Down</option>
                    </select>
                    
                    <label for="object_position">Object Position</label>
                    <select name="object_position">
                        <option value="center">Center (centre)</option>
                        <option value="top">Top (haut)</option>
                        <option value="bottom">Bottom (bas)</option>
                        <option value="left">Left (gauche)</option>
                        <option value="right">Right (droite)</option>
                        <option value="top left">Top Left (haut gauche)</option>
                        <option value="top right">Top Right (haut droite)</option>
                        <option value="bottom left">Bottom Left (bas gauche)</option>
                        <option value="bottom right">Bottom Right (bas droite)</option>
                    </select>
                    
                    <label for="width">Largeur</label>
                    <div class="obwp-input-with-unit">
                        <input type="number" name="width" placeholder="Largeur">
                        <select name="width_unit">
                            <option value="px">px</option>
                            <option value="%">%</option>
                        </select>
                    </div>
                    
                    <label for="height">Hauteur</label>
                    <div class="obwp-input-with-unit">
                        <input type="number" name="height" placeholder="Hauteur">
                        <select name="height_unit">
                            <option value="px">px</option>
                            <option value="%">%</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        

        
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
