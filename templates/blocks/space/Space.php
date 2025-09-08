<?php

/* 
SNIPPETS & TIPS

à remplir :
    > nom du dossier
    > nom du fichier
    
    > block_type
    > display_name
    > layouts

    > construct('CLASS_NAME')
*/

use Timber\Timber;

require_once get_template_directory() . '/templates/blocks/Block.php';

class Space extends Block {
    public $html;
    public $block_type = 'space';
    public $display_name = 'Espace';
    public $layouts = ['default'];

    public function __construct()
    {
        parent::__construct('space', ['custom_css', 'layout', 'height']);
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
        $data = $this->normalizeData();
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