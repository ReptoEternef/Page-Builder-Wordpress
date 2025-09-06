<?php

use Timber\Timber;

require_once get_template_directory() . '/templates/blocks/Block.php';

class GalleryBlock extends Block {
    public $block_type = 'gallery';

    public function __construct()
    {
        parent::__construct('gallery', ['title','image']);
    }

    public function renderAdmin()
    {
        include __DIR__ . '/admin.php';
    }

    public function renderFrontend($values)
    {
        /* Timber::render('blocks/text/view.twig', [
            'content' => $values['content'] ?? '',
        ]); */
    }

    public function getType() {
        return $this->block_type;
    }

    public function enqueueAssets()
    {
        $css = __DIR__ . '/assets/css/style.css';
        $js = __DIR__ . '/assets/js/script.js';

        if (file_exists($css)) {
            wp_enqueue_style(
                'block-hero',
                get_template_directory_uri() . '/templates/blocks/' . $this->block_type . '/assets/css/style.css',
                [],
                filemtime($css)
            );
        }

        if (file_exists($js)) {
            wp_enqueue_script(
                'block-hero',
                get_template_directory_uri() . '/templates/blocks/' . $this->block_type . '/assets/js/script.js',
                ['jquery'],
                filemtime($js),
                true
            );
        }
    }
}