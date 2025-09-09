<?php
/* 
à remplir :
    > name des input (avec nom du field)
    > les value $data[] (sauf pour textarea)
    > dropdown : si autre que layout, bien mettre <option value="" disabled selected>-- Choisissez une option --</option>
*/



?>

<div class="">
    <strong><?= $this->display_name ?></strong>
    <div class="flex-row">

        <?php // ----- dd_layouts DROPDOWN -------
        if (count($this->layouts) > 1) {
            ?>
            <!-- <label for="layout">Layout :</label> -->
            <select name="layout" id="">
                <?php
                if ($this->layouts) {
                    foreach ($this->layouts as $layout) {
                        ?> <option value="<?= esc_attr($layout) ?>" id=""><?= esc_attr($layout) ?></option> <?php
                    }
                }
                ?>
            </select>
            <?php
        } // ----------------------------------
        ?>
        
        
    </div>
    
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
<?php

/* SNIPPETS & TIPS

<?= $this->display_name ?>

<?= $data['field'] ?? '' ?>

<input type="text" name="NOM_DU_FIELD" value="<?= $data['title'] ?? '' ?>" placeholder="Titre">
<textarea type="text" name="NOM_DU_FIELD" placeholder="Slogan"><?= $data['slogan'] ?? '' ?></textarea>

> IMPORT D'IMAGES
bien penser à :
    class="block-field"
    data-name="field de la classe"

<div class="block-field" data-name="background">
    <label for="image">Background</label>
    <button type="button" class="button select-media">Choisir une image</button>

    <div class="preview-container"></div>
</div>

*/