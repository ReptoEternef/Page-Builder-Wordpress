<?php
/* 
à remplir :
    > name des input (avec nom du field)
    > les value $data[] (sauf pour textarea)
    > dropdown : si autre que layout, bien mettre <option value="" disabled selected>-- Choisissez une option --</option>
*/
?>

<div class="flex-column wrap">
    <strong><?= $this->display_name ?></strong>
    <div class="flex-row">

        <?php // ----- LAYOUTS DROPDOWN -------
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
        
        <input type="text" name="title" value="<?= $data['title'] ?? '' ?>" placeholder="Titre">
        <textarea type="text" name="slogan" placeholder="Slogan"><?= $data['slogan'] ?? '' ?></textarea>
    </div>
    
    <!--                Image import                -->
    <div class="block-field" data-name="background">
        <label for="image">Background</label>
        <button type="button" class="button select-media">Choisir une image</button>

        <div class="preview-container"></div>
    </div>

    <div class="WYSIWYG">
                <!--      WYSIWYG (IDs must be unique)      -->
        <?php
        // ids in editors must match the names set in class
        $block_id = $block['display_order'] ?? uniqid();

        // Ajouter le format directement dans le tableau
        $editors = [
            'title' => [
                'id' => 'wys_' . $block_id . '_1',
                'display_name' => 'Titre',
                'format' => 'h2',
                'rows' => 1,
            ],
            'content' => [
                'id' => 'wys_' . $block_id . '_2',
                'display_name' => 'Contenu',
                'format' => 'p',
                'rows' => 8,
            ],
        ];

        foreach ($editors as $fieldName => $editorData) {
            $content = $values[$fieldName] ?? '';
            $editor_id = $editorData['id'];
            $format = $editorData['format'] ?? 'p';

            echo '<b>' . $editorData['display_name'] . '</b>';

            wp_editor($content, $editor_id, [
                'textarea_name' => $fieldName,
                'media_buttons' => false,
                'teeny' => false,
                'quicktags' => false,
                'wpautop' => true,
                'textarea_rows' => $editorData['rows'],
                'tinymce' => [
                    'forced_root_block' => $format,
                ],
            ]);
        }
        ?>
    </div>
</div>



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