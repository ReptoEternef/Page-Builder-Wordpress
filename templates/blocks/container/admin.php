<?php
/* 
à remplir :
    > name des input (avec nom du field)
    > les value $data[] (sauf pour textarea)
    > dropdown : si autre que layout, bien mettre <option value="" disabled selected>-- Choisissez une option --</option>
*/
?>

<div class="obwp-block-admin inner-<?= $this->type ?>">
    <div class="obwp-block-header">
        <strong class="obwp-block-title"><?= $this->display_name ?></strong>
    </div>
    <div class="flex-row">

        <!-- Options système (layouts, contexte couleur, etc.) -->
        <?php if (!empty($this->layouts) || in_array('color_context', $this->fields)): ?>
        <div class="obwp-system-options">
            <?php 
            obwp_dropdown($this, 'layout');
            obwp_dropdown_block_selector(obwp_get_library());
            ?>
            <button type="button" class="container-btn">Ajouter un bloc</button>
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
        <?php endif; ?>
        
        <!-- Custom CSS (toujours en dernier) -->
        <?php if (in_array('custom_css', $this->fields)): ?>
        <div class="obwp-advanced-options">
            <input type="text" name="custom_css" placeholder="Custom CSS" class="obwp-input-full">
        </div>
        <?php endif; ?>    
    </div>
</div>