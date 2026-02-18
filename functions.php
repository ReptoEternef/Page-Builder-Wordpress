<?php

// 0. Fonctions utilitaires / helpers
// 1. Timber & Setup theme
// 2. Theme options & option page
// 3. Enqueue scripts/styles
// 4. Hooks & filters
// 5. Metabox Wordpress
// 6. Updates Wordpress


//=============================================================================================================================================================
//                                                          0. Fonctions utilitaires / helpers
//=============================================================================================================================================================

// Get a list of all blocks and make class instances so they can be used in page editor
function obwp_get_library() {
    $blocks_library = [];
    
    // 1. Charger les blocs du thème ENFANT EN PREMIER
    if (is_child_theme()) {
        $child_blocks_dir = get_stylesheet_directory() . '/templates/blocks';
        if (is_dir($child_blocks_dir)) {
            $blocks_library = obwp_scan_blocks_directory($child_blocks_dir, $blocks_library, 'child');
        }
    }
    
    // 2. Charger les blocs du parent (les blocs déjà chargés par l'enfant seront skipés)
    $parent_blocks_dir = get_template_directory() . '/templates/blocks';
    if (is_dir($parent_blocks_dir)) {
        $blocks_library = obwp_scan_blocks_directory($parent_blocks_dir, $blocks_library, 'parent');
    }
        
    return $blocks_library;
}

// Helper function to scan a blocks directory and populate the library
function obwp_scan_blocks_directory($blocks_dir, $blocks_library = [], $source = 'parent') {
    foreach (glob($blocks_dir . '/*', GLOB_ONLYDIR) as $block_folder) {
        $block_name = basename($block_folder);
        $class_name = ucfirst($block_name);

        // Classe déjà chargée (vient de l'enfant si on est côté parent, ou d'un require précédent)
        // On instancie si pas encore fait, puis on skip le require
        if (class_exists($class_name)) {
            if (!isset($blocks_library[$block_name])) {
                $blocks_library[$block_name] = new $class_name();
            }
            continue;
        }

        // Classe pas encore chargée, on require et on instancie
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
    <div>
        <label for="block-selector">Liste des blocs</label>
        <select name="blocks" id="block-type-selector">
            <?php
            foreach ($blocks_library as $block) {
                ?>
                <option value="<?= esc_attr($block->type) ?>"><?= esc_html($block->display_name) ?></option>
                <?php
            }
            ?>
        </select>
    </div>
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

function obwp_dropdown($object, $option) {
    // $object is $this
    // $option can be either 'layout' or 'color_context' for instance

    if (count($object->layouts) > 1) {
    ?>
    <div>
        <label for="<?php echo $option ?>"><?php echo $option ?></label>
        <select name="<?php echo $option ?>" id="">
            <?php
            if ($option === 'layout' && $object->layouts) {
                foreach ($object->layouts as $layout) {
                    ?> <option value="<?= esc_attr($layout) ?>" id=""><?= esc_attr($layout) ?></option> <?php
                }
            }
            else if ($option === 'color_context') {
                ?> <option value="default" id="">default colors</option> <?php
                ?> <option value="color-inverted" id="">inverted colors</option> <?php
            }
            ?>
        </select>
    </div>
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
    $context['menu'] = \Timber\Timber::get_menu('primary');
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

    if ($hook === 'post.php' && $post && $post->post_type === 'page') {
        // ACTIVER TINYMCE POUR LE PAGE BUILDER
        wp_enqueue_editor();

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
        'block-accordion-js',
        get_template_directory_uri() . '/assets/js/block-accordion.js',
        [],
        filemtime(get_template_directory() . '/assets/js/block-accordion.js'),
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


// ENQUEUE BLOCKS' CSS & JS (FRONT)
function obwp_enqueue_blocks_assets(array $blocks) {
    foreach ($blocks as $block) {
        $block_type = $block['type'] ?? null;
        if (!$block_type) continue;

        $child_path = '';
        $child_uri  = '';
        $parent_path = get_template_directory() . "/templates/blocks/$block_type";
        $parent_uri  = get_template_directory_uri() . "/templates/blocks/$block_type";

        if (is_child_theme()) {
            $child_path = get_stylesheet_directory() . "/templates/blocks/$block_type";
            $child_uri  = get_stylesheet_directory_uri() . "/templates/blocks/$block_type";
        }

        // CSS parent
        $parent_css = $parent_path . "/assets/css/style.css";
        $parent_css_exists = file_exists($parent_css);

        if ($parent_css_exists) {
            wp_enqueue_style(
                "block-$block_type-parent",
                $parent_uri . "/assets/css/style.css",
                [],
                filemtime($parent_css)
            );
        }

        // CSS enfant
        if ($child_path && file_exists($child_path . "/assets/css/style.css")) {
            wp_enqueue_style(
                "block-$block_type-child",
                $child_uri . "/assets/css/style.css",
                $parent_css_exists ? ["block-$block_type-parent"] : [],
                filemtime($child_path . "/assets/css/style.css")
            );
        }

        // JS parent
        $parent_js = $parent_path . "/assets/js/script.js";
        $parent_js_exists = file_exists($parent_js);

        if ($parent_js_exists) {
            wp_enqueue_script(
                "block-$block_type-parent",
                $parent_uri . "/assets/js/script.js",
                [],
                filemtime($parent_js),
                true
            );
        }

        // JS enfant
        if ($child_path && file_exists($child_path . "/assets/js/script.js")) {
            wp_enqueue_script(
                "block-$block_type-child",
                $child_uri . "/assets/js/script.js",
                $parent_js_exists ? ["block-$block_type-parent"] : [],
                filemtime($child_path . "/assets/js/script.js"),
                true
            );
        }

        // Récursivité : traiter les enfants
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



function add_field_btn($type, $name, $placeholder, $text, $trad) {
    ?> <button class="add-field"
        data-field-type="<?= $type ?>"
        data-field-name="<?= $name ?>"
        data-field-placeholder="<?= $placeholder ?>"
        data-field-trad="<?= $trad ?>"
    ><?= $text ?></button> <?php
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


// Désactiver TinyMCE UNIQUEMENT côté front (pas dans l'admin)
/* add_action('wp_print_scripts', function() {
    if (!is_admin()) {
        wp_deregister_script('tinymce');
        wp_dequeue_script('editor');
        wp_dequeue_script('wp-editor');
    }
}); */




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


//=============================================================================================================================================================
//                                                               6. Updates WP
//=============================================================================================================================================================

// Charger le système de mise à jour
require_once get_template_directory() . '/includes/theme-updater.php';

/* require 'plugin-update-checker/plugin-update-checker.php';

use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

$updateChecker = PucFactory::buildUpdateChecker(
    'https://github.com/ReptoEternef/Page-Builder-Wordpress.git',
    get_stylesheet_directory() . '/style.css',
    'obwp-theme'
);

$updateChecker->setBranch('main'); */




/**
 * Theme Update Checker - Debug
 * À placer temporairement dans functions.php pour diagnostiquer les problèmes
 */

// Afficher les erreurs (à retirer en production)
add_action('admin_notices', function() {
    if (!current_user_can('manage_options')) {
        return;
    }
    
    // Vérifier que la bibliothèque est chargée
    $lib_path = get_template_directory() . '/lib/plugin-update-checker/plugin-update-checker.php';
    if (!file_exists($lib_path)) {
        echo '<div class="notice notice-error"><p>❌ Plugin Update Checker non trouvé à : ' . esc_html($lib_path) . '</p></div>';
        return;
    }
    
    // Vérifier la version du thème
    $theme = wp_get_theme();
    echo '<div class="notice notice-info"><p>';
    echo '📦 Thème : ' . esc_html($theme->get('Name')) . '<br>';
    echo '🔢 Version actuelle : ' . esc_html($theme->get('Version')) . '<br>';
    echo '📁 Slug : ' . esc_html($theme->get_stylesheet()) . '<br>';
    echo '📂 Dossier : ' . esc_html(get_template_directory());
    echo '</p></div>';
    
    // Tester la connexion GitHub
    if (isset($_GET['test_github'])) {
        $url = 'https://api.github.com/repos/ReptoEternef/Page-Builder-Wordpress/releases/latest';
        $response = wp_remote_get($url);
        
        if (is_wp_error($response)) {
            echo '<div class="notice notice-error"><p>❌ Erreur GitHub : ' . esc_html($response->get_error_message()) . '</p></div>';
        } else {
            $body = json_decode(wp_remote_retrieve_body($response), true);
            echo '<div class="notice notice-success"><p>';
            echo '✅ Connexion GitHub OK<br>';
            if (isset($body['tag_name'])) {
                echo '🏷️ Dernière release : ' . esc_html($body['tag_name']) . '<br>';
                echo '📅 Publiée le : ' . esc_html($body['published_at']);
            }
            echo '</p></div>';
        }
    }
});

// Ajouter un lien de test dans la barre d'admin
add_action('admin_bar_menu', function($wp_admin_bar) {
    if (!current_user_can('manage_options')) {
        return;
    }
    
    $wp_admin_bar->add_node([
        'id'    => 'test-theme-update',
        'title' => '🔍 Test Update GitHub',
        'href'  => admin_url('themes.php?test_github=1'),
    ]);
}, 100);

// Forcer la vérification des mises à jour
add_action('admin_init', function() {
    if (isset($_GET['force_update_check']) && current_user_can('manage_options')) {
        delete_site_transient('update_themes');
        wp_redirect(admin_url('themes.php'));
        exit;
    }
});