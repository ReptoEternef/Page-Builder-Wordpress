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
            <?php
                $args = [
                    'post_type' => 'movie',
                    'posts_per_page' => -1,
                ];
                $posts = get_posts($args);
                //echo $data['custom_post_type'];

                $custom_post_types = get_post_types([
                    '_builtin' => false
                ], 'objects');

                if (!empty($custom_post_types)) {
                    ?>
                    <label for="layout">Post types :</label>            
                    <select name="custom_post_type" id="">
                        <option value="" disabled selected>-- Choisissez une option --</option>
                        <?php
                        foreach ($custom_post_types as $slug => $cpt) {
                            if (str_starts_with($slug, 'acf')) continue;

                            ?> <option value="<?= esc_attr($slug) ?>" id=""><?= esc_attr($cpt->label) ?></option> <?php
                        }
                        ?>

                    </select>
                    <?php
                } else {
                    echo 'No custom cpost type created';
                }
            ?>
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