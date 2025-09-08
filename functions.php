<?php
// ================================
// Timber & Setup theme
// ================================
if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
    require_once __DIR__ . '/vendor/autoload.php';
} else {
    add_action('admin_notices', function(){
        echo '<div class="notice notice-error"><p>Timber n\'est pas installé — exécute <code>composer require timber/timber</code> dans le dossier du thème.</p></div>';
    });
    return;
}

\Timber\Timber::$dirname = ['templates'];

add_action('after_setup_theme', function() {
    add_theme_support('post-thumbnails');
    add_theme_support('title-tag');
    add_theme_support('custom-logo', [
        'height'      => 100,
        'width'       => 400,
        'flex-height' => true,
        'flex-width'  => true,
    ]);

    register_nav_menus([
        'primary' => 'Menu Principal',
    ]);
});

// ================================
// Context Timber & options thème
// ================================
add_filter('timber/context', function($context) {
    $custom_logo_id = get_theme_mod('custom_logo');
    $context['logo_url'] = wp_get_attachment_image_url($custom_logo_id, 'full');
    return $context;
});



// ================================
// Enqueue CSS & JS
// ================================
add_action('wp_enqueue_scripts', function() {

    wp_enqueue_style(
        'main-style',
        get_template_directory_uri() . '/assets/css/style.css',
        [],
        filemtime(get_template_directory() . '/assets/css/style.css')
    );

    wp_enqueue_script(
        'main-js',
        get_template_directory_uri() . '/assets/js/main.js',
        [],
        filemtime(get_template_directory() . '/assets/js/main.js'),
        true
    );
});

add_action('admin_enqueue_scripts', function($hook) {
    global $post;
    
    // ENQUEUE HERE TO CSS ADMIN PAGE BLOCKS
    wp_enqueue_style(
        'admin-style',
        get_template_directory_uri() . '/assets/css/admin.css',
        [],
        filemtime(get_template_directory() . '/assets/css/admin.css')
    );
    
    if ($hook === 'post.php' && $post && $post->post_type === 'page') {
        wp_enqueue_script(
            'page-blocks-js',
            get_template_directory_uri() . '/assets/js/page-blocks.js',
            [],
            filemtime(get_template_directory() . '/assets/js/page-blocks.js'),
            true
        );
    }
    
    wp_enqueue_script(
        'iconify',
        'https://code.iconify.design/3/3.1.1/iconify.min.js',
        [],
        null,
        true
    );
});


// ================================
// ENQUEUE BLOCKS' CSS & JS
// ================================

// (Only from blocks on page #optimazor2000)
add_action('wp_enqueue_scripts', function() {
    if (!is_singular()) return; // seulement sur les pages/posts

    global $post;
    if (!$post) return;
    $blocks_data = get_post_meta($post->ID, '_page_blocks', true);
    $blocks = $blocks_data ? json_decode($blocks_data, true) : [];

    if (!$blocks) return;

    $types = array_unique(array_column($blocks, 'type'));

    foreach ($types as $block_type) {
        $css_file = get_template_directory() . "/templates/blocks/$block_type/assets/css/style.css";
        $js_file = get_template_directory() . "/templates/blocks/$block_type/assets/js/script.js";

        if (file_exists($css_file)) {
            wp_enqueue_style(
                "block-$block_type",
                get_template_directory_uri() . "/templates/blocks/$block_type/assets/css/style.css",
                [],
                filemtime($css_file)
            );
        }
        if (file_exists($js_file)) {
            wp_enqueue_script(
                "block-$block_type",
                get_template_directory_uri() . "/templates/blocks/$block_type/assets/js/script.js",
                [],
                filemtime($js_file),
                true
            );
        }
    }
});





// ================================
//            FUNCTIONS
// ================================
// Get a list of all blocks and make class instances
function get_library() {
    $blocks_dir = get_template_directory() . '/templates/blocks';
    $availableBlocks = [];

        foreach (glob($blocks_dir . '/*', GLOB_ONLYDIR) as $block_folder) {
            $block_name = basename($block_folder); // ex: "text"

            // Get full path
            $class_path = $block_folder . '/' . ucfirst($block_name) . '.php';
            // Get class name only
            $class_name = ucfirst($block_name);
            
            //var_dump($class_name);
            //var_dump($class_path);

            if (file_exists($class_path)) {
                require_once $class_path;
                
                if (class_exists($class_name)) {
                    $availableBlocks[$block_name] = new $class_name();
                }
            }
        }
        
    return $availableBlocks;
}

// Dropdown to select a block to add
function dropdown_block_selector($blocks_library) {
    ?>
    <!-- <label for="block-selector">Liste des blocs :</label> -->
    <select name="blocks" id="block-type-selector">
        <?php
        foreach ($blocks_library as $block) {
            var_dump($block->display_name);
            ?>
            <option value="<?= esc_attr($block->block_type) ?>"><?= esc_html($block->display_name) ?></option>
            <?php
        }
        ?>
    </select>
    <?php
}



// ================================
// Page Builder
// ================================

// Get rid of Gutemberg
add_action('admin_init', function() {
    remove_post_type_support('page', 'editor');
});

// Ajouter la metabox
add_action('add_meta_boxes', function() {
    add_meta_box(
        '_page_blocks',
        'Blocs de la page',
        'render_admin_UI',
        'page',
        'normal',
        'high'
    );
});



function render_admin_UI($post) {
    $post = get_post();
    if ( ! $post || ! $post->ID ) {
        echo 'Publiez la page avant de pouvoir utiliser le page builder.';
        return;
    }
    // Get library of all available blocks
    $blocks_library = get_library();

    // BLOCKS INIT
    $page_blocks = get_post_meta($post->ID, '_page_blocks', true);
    $page_blocks = $page_blocks ? json_decode($page_blocks, true) : [];

    // Dropdown to select a block to add
    ?> 
    <div class="inside">
        <?php dropdown_block_selector($blocks_library); ?>
        <button type="button" id="add_block_btn">Ajouter un bloc</button>
        <button type="button" id="debug_btn">debug</button>
        <input type="text" name="_page_blocks" id="blocks_data" value="<?php echo esc_attr(json_encode($page_blocks ?: [])); ?>"></input>
    </div>
    <?php // JSON INPUT NOT HIDDEN FOR DEBUG


// Render admin
foreach ($page_blocks as $block) {
    $type = $block['type'];
    $blocks_library[$type]->renderAdmin($block['values']);
}


// BLOCKS LIBRARY FOR JS
//var_dump($library_array);
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



// ================================
//            SAUVEGARDE
// ================================

add_action('save_post', function($post_id) {
    // security
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    if (isset($_POST['_page_blocks'])) {
        $blocks_json = wp_unslash($_POST['_page_blocks']);
        update_post_meta($post_id, '_page_blocks', $blocks_json);
    }
});



// ================================
// AJAX pour le Page Builder
// ================================






// ================================
// Afficher les blocs en JSON
// ================================

add_action('add_meta_boxes', function() {
    add_meta_box(
        'page_blocks_json',
        'JSON des blocs',
        'render_blocks_json_meta_box',
        'page',
        'side', // position sur la droite
        'default'
    );
});

function render_blocks_json_meta_box($post) {
    // Récupère le JSON actuel
    //$json_blocks = get_post_meta($post->ID, '_page_blocks', true);
    $page_blocks = get_post_meta($post->ID, '_page_blocks', true);
    $page_blocks = $page_blocks ? json_decode($page_blocks, true) : [];
    ?>
    <label for="blocks-json">JSON actuel :</label>
    <textarea id="blocks-json" rows="10" style="width:100%;"><?php echo htmlspecialchars(json_encode($page_blocks, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)); ?></textarea>
    <?php //echo esc_attr(json_encode($page_blocks ?: [])); ?>
    <?php
}