<?php

// 0. Fonctions utilitaires / helpers
// 1. Timber & Setup theme
// 2. Theme options & option page
// 3. Enqueue scripts/styles
// 4. Hooks & filters
// 5. Metabox Wordpress


//=============================================================================================================================================================
//                                                          0. Fonctions utilitaires / helpers
//=============================================================================================================================================================

// Get a list of all blocks and make class instances so they can be used in page editor
function obwp_get_library() {
    $blocks_dir = get_template_directory() . '/templates/blocks';
    $blocks_library = [];

    foreach (glob($blocks_dir . '/*', GLOB_ONLYDIR) as $block_folder) {
        $block_name = basename($block_folder);  // ex: "text"
        $class_name = ucfirst($block_name);     // ex: "Text"
        $class_path = $block_folder . '/' . $class_name . '.php';

        if (file_exists($class_path)) {
            require_once $class_path;
            
            if (class_exists($class_name)) {
                $blocks_library[$block_name] = new $class_name();
            }
        }
    }
        
    return $blocks_library;
}

// Dropdown to select a block to add
function obwp_dropdown_block_selector($blocks_library) {
    ?>
    <!-- <label for="block-selector">Liste des blocs :</label> -->
    <select name="blocks" id="block-type-selector">
        <?php
        foreach ($blocks_library as $block) {
            ?>
            <option value="<?= esc_attr($block->type) ?>"><?= esc_html($block->display_name) ?></option>
            <?php
        }
        ?>
    </select>
    <?php
}
function obwp_dropdown_lang_selector($lang_array) {
    ?>
    <!-- <label for="block-selector">Liste des blocs :</label> -->
    <select name="langs" id="lang-selector">
        <?php
        foreach ($lang_array as $lang) {
            ?>
            <option value="<?= esc_attr($lang) ?>"><?= esc_html($lang) ?></option>
            <?php
        }
        ?>
    </select>
    <?php
}


// Simple function to get the JSON of saved blocks in page from the meta key _page_blocks
function obwp_get_blocks_in_page(int $post_id): array {
    $json_meta = get_post_meta($post_id, '_page_blocks', true);

    return $json_meta ? json_decode($json_meta, true) : [];
}

// Displays an error if metabox's callback is missing
function meta_box_error() {
    echo "Error with callback function. Page builder's path missing. File : admin-page-builder.php";
}

function createInput($data, $inputType, $name, $placeholder) {
    $lang = obwp_get_current_lang();

    switch ($inputType) {
        case 'text':
            ?> <input type="text" name="<?= $name ?>" placeholder="<?= $placeholder ?>" value="<?= $data[$name][$lang] ?? '' ?>"> <?php
            break;
        
        case 'textarea':
            ?> <textarea type="text" name="<?= $name ?>" placeholder="<?= $placeholder ?>"><?= $data[$name][$lang] ?? '' ?></textarea> <?php
            break;
        
        default:
            # code...
            break;
    }
}

function layoutsDropdown($object) {
    if (count($object->layouts) > 1) {
    ?>
    <!-- <label for="layout">Layout :</label> -->
    <select name="layout" id="">
        <?php
        if ($object->layouts) {
            foreach ($object->layouts as $layout) {
                ?> <option value="<?= esc_attr($layout) ?>" id=""><?= esc_attr($layout) ?></option> <?php
            }
        }
        ?>
    </select>
    <?php
}
}

function obwp_get_available_langs() {
    $options = get_option('obwp_options', []);
    $langs = $options['available_langs'] ?? [];

    if (!is_array($langs)) {
        return [];
    }

    return array_values(array_map('sanitize_key', $langs));
}

function obwp_get_default_lang() {
    $langs = obwp_get_available_langs();
    return $langs[0] ?? 'fr';
}

function obwp_get_current_lang() {
    $available = obwp_get_available_langs();
    $default = obwp_get_default_lang();

    if (empty($available)) {
        return $default;
    }

    $lang = $_GET['lang'] ?? $default;
    $lang = sanitize_key($lang);

    return in_array($lang, $available, true) ? $lang : $default;
}


//=============================================================================================================================================================
//                                                          1. Timber & Setup theme
//=============================================================================================================================================================

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

// Context Timber & options thème
add_filter('timber/context', function($context) {
    $custom_logo_id = get_theme_mod('custom_logo');
    $context['logo_url'] = wp_get_attachment_image_url($custom_logo_id, 'full');
    $context['options'] = get_option('obwp_options', []);
    $context['lang'] = obwp_get_current_lang();
    return $context;
});

$page_builder_path = get_template_directory() . '/includes/admin-page-builder.php';
if (file_exists($page_builder_path)) {
    require_once $page_builder_path;
}




//=============================================================================================================================================================
//                                                          2. Theme options & option page
//=============================================================================================================================================================

// Get rid of Gutemberg
add_action('admin_init', function() {
    remove_post_type_support('page', 'editor');
    remove_post_type_support('page', 'comments');
});

// 1. Ajouter la page d'options
add_action('admin_menu', function() {
    add_theme_page(
        'Options du thème',      // Titre de la page
        'Options du thème',      // Titre du menu
        'manage_options',        // Capacité
        'obwp-theme-options',    // Slug
        'render_theme_options'   // Callback
    );
});

function render_theme_options() {
    include get_template_directory() . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'admin-option-page.php';
}

add_action('admin_init', function() {
    register_setting('obwp_options_group', 'obwp_options');
});

add_filter('timber/twig', function ($twig) {
    $twig->enableDebug();
    return $twig;
});




//=============================================================================================================================================================
//                                                          3. Enqueue scripts/styles
//=============================================================================================================================================================

// Enqueue CSS & JS
add_action('wp_enqueue_scripts', function() {

    wp_enqueue_style(
        'main-style',
        get_template_directory_uri() . '/assets/css/style.css',
        [],
        filemtime(get_template_directory() . '/assets/css/style.css')
    );
    wp_enqueue_style(
        'theme-style',
        get_template_directory_uri() . '/assets/css/theme.css',
        [],
        filemtime(get_template_directory() . '/assets/css/theme.css')
    );

    wp_enqueue_script(
        'TinyMCE',
        'https://cdn.tiny.cloud/1/no-api-key/tinymce/5/tinymce.min.js',
        [],
        null,
        true
    );

    wp_enqueue_script(
        'main-js',
        get_template_directory_uri() . '/assets/js/main.js',
        ['TinyMCE'],
        filemtime(get_template_directory() . '/assets/js/main.js'),
        true
    );

    wp_enqueue_script(
        'iconify',
        'https://code.iconify.design/3/3.1.1/iconify.min.js',
        [],
        null,
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
    
/*     if ($hook === 'post.php' && $post && $post->post_type === 'page') {
        wp_enqueue_script(
            'page-blocks-js',
            get_template_directory_uri() . '/assets/js/page-blocks.js',
            [],
            filemtime(get_template_directory() . '/assets/js/page-blocks.js'),
            true
        );
    } */
    if ($hook === 'post.php' && $post && $post->post_type === 'page') {
        wp_enqueue_script(
            'page-builder-js',
            get_template_directory_uri() . '/assets/js/page-builder.js',
            [],
            filemtime(get_template_directory() . '/assets/js/page-builder.js'),
            true
        );
    }
    if ($hook === 'appearance_page_obwp-theme-options') {
        wp_enqueue_script(
            'obwp-option-page-js',
            get_template_directory_uri() . '/assets/js/obwp-option-page.js',
            [],
            filemtime(get_template_directory() . '/assets/js/obwp-option-page.js'),
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


// ENQUEUE BLOCKS' CSS & JS (FRONT)
// (Only from blocks on page #optimizor2000)
function obwp_enqueue_blocks_assets(array $blocks) {
    foreach ($blocks as $block) {

        $block_type = $block['type'] ?? null;
        if (!$block_type) {
            continue;
        }

        $css_file = get_template_directory() . "/templates/blocks/$block_type/assets/css/style.css";
        $js_file  = get_template_directory() . "/templates/blocks/$block_type/assets/js/script.js";

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

        // 🔁 Récursivité : on traite les enfants
        if (!empty($block['children']) && is_array($block['children'])) {
            obwp_enqueue_blocks_assets($block['children']);
        }
    }
}

add_action('wp_enqueue_scripts', function() {

    if (!is_singular()) return;

    global $post;
    if (!$post) return;

    $page_blocks = obwp_get_blocks_in_page($post->ID);
    if (!$page_blocks) return;

    obwp_enqueue_blocks_assets($page_blocks);
});



function add_field_btn($type, $name, $placeholder, $text) {
    ?> <button class="add-field" data-field-type="<?= $type ?>" data-field-name="<?= $name ?>" data-field-placeholder="<?= $placeholder ?>"><?= $text ?></button> <?php
}

//=============================================================================================================================================================
//                                                          4. Hooks & filters
//=============================================================================================================================================================

// ================================
//            SAUVEGARDE
// ================================
add_action('save_post', function($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;
    
    if (isset($_POST['_page_blocks'])) {
        $blocks_json = wp_unslash($_POST['_page_blocks']);
        $decoded = json_decode($blocks_json, true);
        
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            // Encode proprement le JSON, en laissant json_encode gérer les guillemets et apostrophes
            $blocks_json = json_encode($decoded, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        } else {
            $blocks_json = wp_unslash($blocks_json);
        }
        
        update_post_meta($post_id, '_page_blocks', wp_slash($blocks_json));
    }
});


add_action('wp_print_scripts', function() {
    wp_deregister_script('tinymce');
});
add_action('wp_print_scripts', function() {
    wp_dequeue_script('editor');      // WP editor core
    wp_dequeue_script('wp-editor');   // TinyMCE wrapper
});




//=============================================================================================================================================================
//                                                          5. Metabox Wordpress
//=============================================================================================================================================================

// Metabox template
/* add_meta_box(
    $id,
    $title,
    $callback,
    $screen = null,
    $context = 'advanced',
    $priority = 'default',
    $callback_args = null
); */

// Main Metabox, thats the page editor. Also checks if the callback function exists.
add_action('add_meta_boxes', function() {
    $render_function = '';
    if (function_exists('render_admin_UI')) {
        $render_function = 'render_admin_UI';
    } else {
        $render_function = 'meta_box_error';
    }
    add_meta_box(
        '_page_blocks',
        'Open Builder : Blocs de la page',
        $render_function,
        'page',
        'normal',
        'high'
    );
});

// Display the JSON that will be saved in DB
add_action('add_meta_boxes', function($post) {

    add_meta_box(
        'page_blocks_json',
        'JSON des blocs',
        'render_blocks_json_meta_box',
        'page',
        'side',
        'default'
    );
});

// Callback for the displayed JSON metabox
function render_blocks_json_meta_box($post) {
    $page_blocks = obwp_get_blocks_in_page($post->ID);
    ?>
    <label for="blocks-json">JSON actuel :</label>
    <?php
}

// Would be great to have a metabox to select which block to place