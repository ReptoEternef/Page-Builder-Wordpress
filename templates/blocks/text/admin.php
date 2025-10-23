<div class="flex-column wrap full-width" data-order="">
    <strong><?= $this->display_name ?></strong>

    <?php
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
    }
    ?>

    <input type="text" name="custom_css" value="<?= $data['custom_css'] ?? ''?>" placeholder="Custom CSS">
        
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
        $format = $editorData['format'] ?? 'p'; // valeur par défaut
        
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
                'toolbar1' => 'bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist | link unlink removeformat',
            ],
        ]);
    }
    ?>
</div>