<?php

use Timber\Timber;

require_once get_template_directory() . '/templates/blocks/Block.php';

class Text extends Block {
    public $html;
    public $block_type = 'text';
    public $display_name = 'Texte';
    public $layouts = ['default'];

    public function __construct()
    {
        parent::__construct('text', ['custom_css', 'title','content']);
    }

    public function renderAdmin($values = [])
    {
        $data = [
            'custom_css' => $values['custom_css'] ?? '',
            'title'      => $values['title'] ?? '',
            'content'    => $values['content'] ?? '',
        ];
        
        include __DIR__ . '/admin.php';
    }

    public function renderFrontend($values = [])
    {
        Timber::render('blocks/text/view.twig', [
            'custom_css' => $values['custom_css'] ?? '',
            'title' => $values['title'] ?? '',
            'content' => $values['content'] ?? '',
        ]);
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