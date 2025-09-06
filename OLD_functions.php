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
        'testytesty',
        'page',
        'normal',
        'high'
    );
});

function testytesty($post) {
    require_once(get_template_directory() . '/templates/blocks/block-instances.php');


    // Récupérer la valeur JSON actuelle stockée dans 'blocks_data'
    $blocks_data = get_post_meta($post->ID, 'blocks_data', true);
    $blocks_data = get_post_meta($post->ID, 'blocks_data', true);

    // Toujours un tableau, même si vide ou invalide
    $saved_blocks = !empty($blocks_data) ? json_decode($blocks_data, true) : [];
    if (!is_array($saved_blocks)) {
        $saved_blocks = [];
    }


    // Afficher pour debug dans l'admin
    ?>
    
    <div id="custom-blocks-editor">
        <!-- Conteneur des blocs gérés par JS -->
        <div id="page_blocks"></div>
        
        <!-- Bouton pour ajouter un bloc -->
        
        <select name="block-type-selector" id="block-type-selector">
            <?php
            foreach ($GLOBALS['availableBlocks'] as $key => $single_block) {
                echo '<option value="' . esc_attr($key) . '">' . esc_html($key) . '</option>';
            }
            ?>
        </select>
        
        <button type="button" id="add_block_btn">Ajouter un bloc</button>
        
        <?php
        $blocks_data = get_post_meta($post->ID, 'blocks_data', true);
        $blocks = !empty($blocks_data) ? json_decode($blocks_data, true) : [];
        if (!is_array($blocks)) {
            $blocks = [];
        }

        // DEBUG Array from DB
        /* echo '<pre>DATA : ';
        var_dump($blocks);
        echo '</pre>'; */
        ?>

    <!-- Input hidden pour stocker le JSON -->
    <input type="hidden"
    id="blocks_data"
    name="blocks_data"
    value='<?php echo esc_attr(json_encode($blocks ?: [])); ?>'>
    </div>


    <?php
    // Préparer les blocs pour JS
    $blocks_for_js = [];
    foreach ($GLOBALS['availableBlocks'] as $type => $block_class) {
        // Créer un tableau de champs avec valeurs vides par défaut
        $fields_with_values = [];
        foreach ($block_class->fields as $field_name) {
            $fields_with_values[$field_name] = '';
        }

        $blocks_for_js[$type] = [
            'type'   => $type,
            'fields' => $fields_with_values
        ];
    }

    // Préparer block_json : on remplit avec les valeurs déjà sauvegardées
    foreach ($saved_blocks as $b) {
        $type = $b['type'] ?? '';
        $block_json[] = [
            'type'  => $type,
            'order' => $b['order'] ?? 0,
            'fields'=> isset($blocks_for_js[$type]) ? array_merge($blocks_for_js[$type]['fields'], $b['fields'] ?? []) : ($b['fields'] ?? [])
        ];
    }



    // Localiser le script JS pour qu'il puisse récupérer le JSON initial
    wp_localize_script('page-blocks-js', 'genBlocks', [
        'ajaxurl'    => admin_url('admin-ajax.php'),
        'block_json' => $blocks,
        'availableBlocks' => $blocks_for_js
    ]);
}



// Sauvegarde générique
/* add_action('save_post', function($post_id) {
    update_post_meta($post_id, 'blocks_data', wp_json_encode($_POST['blocks']));
    if (isset($_POST['blocks'])) {
        update_post_meta($post_id, 'blocks_data', wp_json_encode($_POST['blocks']));
    }
}); */

add_action('save_post', function($post_id) {
    // security
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    if (isset($_POST['blocks_data'])) {
        $blocks_json = wp_unslash($_POST['blocks_data']);
        update_post_meta($post_id, 'blocks_data', $blocks_json);
    }
});

// ================================
// AJAX pour le Page Builder
// ================================

add_action('wp_ajax_generate_blocks', 'generate_blocks_callback');
function generate_blocks_callback($post) {
    // Récupère la variable envoyée depuis JS
    //$blocks = isset($_POST['block_array']) ? sanitize_text_field($_POST['block_array']) : '';
    //var_dump($blocks);
    echo 'oui';

    $blocks_data = get_post_meta($post->ID, 'blocks_data', true);


    // Exemple : renvoyer quelque chose à JS
    wp_send_json_success([
        'message' => 'PHP a reçu : ' . '$blocks',
    ]);

    wp_die();
}


add_action('wp_ajax_get_blocks_html', function () {
    require_once(get_template_directory() . '/templates/blocks/block-instances.php');

    $blocks_dir = get_template_directory() . '/templates/blocks';
    $availableBlocks = [];

    
    foreach (glob($blocks_dir . '/*', GLOB_ONLYDIR) as $block_folder) {
        $block_name = basename($block_folder); // ex: "text"
        
        $class_file = $block_folder . '/' . ucfirst($block_name) . 'Block.php';
        $class_name = ucfirst($block_name) . 'Block';
        
        if (file_exists($class_file)) {
            require_once $class_file;
            
            if (class_exists($class_name)) {
                $availableBlocks[$block_name] = new $class_name();
            }
        }
    }

    // Ici on génère le HTML pour chaque block de la librairie
    $blocks_html = [];
    foreach ($availableBlocks as $slug => $block) {
        ob_start();
        $block->renderAdmin();
        $blocks_html[$slug] = ob_get_clean();
    }
    
    wp_send_json_success([
        'message' => 'All blocks generated',
        'allBlocksHTML'  => $blocks_html,
        'availableBlocksDebug' => $blocks_html
    ]);
    
    wp_die();
});




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
    $blocks_data = get_post_meta($post->ID, 'blocks_data', true);
    $blocks = $blocks_data ? json_decode($blocks_data, true) : [];
    ?>
    <label for="blocks-json">JSON actuel :</label>
    <textarea id="blocks-json" rows="10" style="width:100%;"><?php echo htmlspecialchars(json_encode($blocks, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)); ?></textarea>
    <?php
}






// INCLUDE BLOCKS CSS AND JS (ONLY FROM BLOCKS ON PAGE)
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
