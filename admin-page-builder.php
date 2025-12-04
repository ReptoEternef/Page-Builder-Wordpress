<?php
function render_admin_UI($post) {
    $post = get_post();

    // Get library of all available blocks
    $blocks_library = obwp_get_library();

    // BLOCKS INIT
    $page_blocks = obwp_get_blocks_in_page($post->ID);
    if (!is_array($page_blocks)) {
        $page_blocks = [];
    }

    // TOOLBAR / HIDDEN INPUT (dropdown / buttons / '_page_blocks' hidden input)
    // Hidden_input stores a JSON created in JS of data/values from blocks. Avoids AJAX / useless server requests
    ?> 
    <div class="inside">
        <!-- dropdown -->
        <?php obwp_dropdown_block_selector($blocks_library); ?>
        <!-- buttons -->
        <button type="button" id="add_block_btn">Ajouter un bloc</button>
        <button type="button" id="debug_btn">debug</button>
        <!-- hidden input -->
        <input type="text" hidden name="_page_blocks" id="blocks_data" value="<?php echo esc_attr(json_encode($page_blocks ?: [])); ?>"></input>
    </div>
    <?php


    // RENDER ADMIN
    if (is_array($page_blocks)) {
        foreach ($page_blocks as $block) {
            $type = $block['type'] ?? '';
            if (isset($blocks_library[$type])) {
                $blocks_library[$type]->renderAdmin($block['values'] ?? []);
            }
        }
    }

    // BLOCKS LIBRARY FOR JS
    $library_array = (array) $blocks_library;
    foreach ($library_array as $block) {
        $block_array = (array) $block;
        $type = $block_array['type'];
        $blocks_library[$type]->html = $blocks_library[$type]->getHTML();
    }

    wp_localize_script('page-blocks-js', 'php', [
        'ajaxurl' => admin_url('admin-ajax.php'),
        'pageBlocks' => $page_blocks,
        'blocksLibrary' => $blocks_library
    ]);
}