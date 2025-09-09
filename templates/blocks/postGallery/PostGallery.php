<?php

/* 
SNIPPETS & TIPS

à remplir :
    > nom du dossier
    > nom du fichier
    
    > nom de la classe
    > block_type
    > display_name
    > layouts

    > construct('CLASS_NAME')
*/

use Timber\Timber;

require_once get_template_directory() . '/templates/blocks/Block.php';

class PostGallery extends Block {
    public $html;
    public $block_type = 'postGallery';
    public $display_name = 'Galerie de posts';
    public $layouts = ['default', 'version2'];

    public function __construct()
    {
        parent::__construct('postGallery', ['custom_css', 'layout', 'custom_post_type']);
    }

    public function renderAdmin($values = [])
    {
        $this->setValues($values ?: $this->values);
        $data = $this->normalizeData();

        ?>
        <div class="block-item">
            <?php include __DIR__ . '/admin.php'; ?>
        </div>
        <?php
    }

    public function renderFrontend($values = [])
    {
        $data = Timber::context();
        $data = array_merge($data, $this->normalizeData());
        $data['values'] = 'TEST';

        // Récupère les posts à ce moment précis, en s'assurant que le CPT existe
        error_log('OUIIIIIIIIIIIIIIIIIIIIIIIIIIIII');
        if ( post_type_exists('movie') ) {
            $data['posts'] = Timber::get_posts([
                'post_type' => 'movie',
                'posts_per_page' => -1,
                'post_status' => 'publish',
                'orderby' => 'date',
                'order' => 'DESC',
            ]);
        } else {
            $data['posts'] = [];
            error_log('⚠️ Movie CPT non trouvé au moment du rendu.');
        }

        Timber::render('blocks/text/view.twig', $data);
    }





    public function getHTML() {
        ob_start();
        include __DIR__ . '/admin.php';
        return ob_get_clean();
    }

    public function enqueueAssets()
    {
        $css = __DIR__ . '/assets/css/style.css';
        $js = __DIR__ . '/assets/js/script.js';

        if (file_exists($css)) {
            wp_enqueue_style(
                'block-hero',
                get_template_directory_uri() . '/templates/blocks/hero/assets/css/style.css',
                [],
                filemtime($css)
            );
        }

        if (file_exists($js)) {
            wp_enqueue_script(
                'block-hero',
                get_template_directory_uri() . '/templates/blocks/hero/assets/js/script.js',
                ['jquery'],
                filemtime($js),
                true
            );
        }
    }
}