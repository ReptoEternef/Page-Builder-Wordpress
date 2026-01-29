<?php
/**
 * Functions and definitions pour le thème enfant Open Builder WP
 *
 * @package OpenBuilderWP_Child
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Charger les styles du parent et de l'enfant
 */
add_action('wp_enqueue_scripts', 'openbuilderWP_child_enqueue_styles');
function openbuilderWP_child_enqueue_styles() {
    // Style du thème parent
    wp_enqueue_style(
        'openbuilderWP-parent-style',
        get_template_directory_uri() . '/style.css',
        [],
        wp_get_theme()->parent()->get('Version')
    );
    
    // Style du thème enfant
    wp_enqueue_style(
        'openbuilderWP-child-style',
        get_stylesheet_uri(),
        ['openbuilderWP-parent-style'],
        wp_get_theme()->get('Version')
    );
    
    // Scripts personnalisés (optionnel)
    // wp_enqueue_script(
    //     'openbuilderWP-child-script',
    //     get_stylesheet_directory_uri() . '/assets/js/custom.js',
    //     ['jquery'],
    //     wp_get_theme()->get('Version'),
    //     true
    // );
}

/**
 * Configuration personnalisée du page builder
 */
add_action('openbuilder_init', 'openbuilderWP_child_config');
function openbuilderWP_child_config() {
    // Exemple : Configurer les options du builder
    // if (function_exists('openbuilder_set_config')) {
    //     openbuilder_set_config([
    //         'logo' => get_stylesheet_directory_uri() . '/assets/images/logo.png',
    //         'primary_color' => '#007bff',
    //         'enable_feature_x' => true,
    //     ]);
    // }
}

/**
 * Ajouter des blocs personnalisés
 */
add_filter('openbuilder_blocks', 'openbuilderWP_child_custom_blocks');
function openbuilderWP_child_custom_blocks($blocks) {
    // Exemple : Ajouter vos propres blocs
    // $blocks['mon-bloc'] = [
    //     'name' => 'Mon Bloc Custom',
    //     'icon' => 'dashicons-star-filled',
    //     'template' => get_stylesheet_directory() . '/blocks/mon-bloc.php',
    // ];
    
    return $blocks;
}

/**
 * Personnalisations supplémentaires
 */

// Modifier le nombre de posts par page
// add_action('pre_get_posts', function($query) {
//     if (!is_admin() && $query->is_main_query() && is_home()) {
//         $query->set('posts_per_page', 12);
//     }
// });

// Ajouter un custom post type
// add_action('init', function() {
//     register_post_type('mon_cpt', [
//         'labels' => [
//             'name' => 'Mes CPT',
//             'singular_name' => 'Mon CPT',
//         ],
//         'public' => true,
//         'has_archive' => true,
//         'supports' => ['title', 'editor', 'thumbnail'],
//     ]);
// });

// Modifier un filtre du parent
// add_filter('openbuilder_default_layout', function($layout) {
//     return 'mon-layout-custom';
// });

/**
 * Vos fonctions personnalisées ici
 */
