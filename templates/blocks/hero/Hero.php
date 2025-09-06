<?php

use Timber\Timber;

require_once get_template_directory() . '/templates/blocks/Block.php';

class Hero extends Block {
    public function __construct($type = 'hero', $fields = ['title', 'slogan', 'image'])
    {
        parent::__construct($type, $fields);
    }


    public function renderAdmin()
    {
        include __DIR__ . '/admin.php';
    }

    public function renderFrontend($values)
    {
        /* Timber::render('blocks/hero/view.twig', [
            'title' => $block['title'] ?? '',
        ]); */
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