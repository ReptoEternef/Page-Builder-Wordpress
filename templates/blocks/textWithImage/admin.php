<div class="flex-column" data-order="">
    <strong><?= $this->display_name ?></strong>

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

    <div class="flex-column">
        
        <div class="block-field" data-name="image">
            <label for="image">Image</label>
            <button type="button" class="button select-media">Choisir une image</button>
            
            <div class="preview-container"></div>
        </div>
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