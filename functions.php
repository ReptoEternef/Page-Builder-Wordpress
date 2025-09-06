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
    $blocks_data = get_post_meta($post->ID, 'blocks_data', true);
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

function get_library() {
    $blocks_dir = get_template_directory() . '/templates/blocks';

        foreach (glob($blocks_dir . '/*', GLOB_ONLYDIR) as $block_folder) {
            $block_name = basename($block_folder); // ex: "text"

            // Get full path
            $class_file = $block_folder . '/' . ucfirst($block_name);
            // Get class name only
            $class_name = ucfirst($block_name);
            
            var_dump($class_name);
            //var_dump($class_file);
            

            if (file_exists($class_file)) {
                require_once $class_file;
                
                if (class_exists($class_name)) {
                    $availableBlocks[$block_name] = new $class_name();
                }

                $availableBlocks = [
                    'text' => new Text(),
                ];


            }
        }
}





// ================================
// Page Builder (Metabox + sauvegarde)
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



function render_admin_UI() {
    get_library();
}






// ================================
// AJAX pour le Page Builder
// ================================






// ================================
// Afficher les blocs en JSON
// ================================





