<?php

use Timber\Timber;

require_once get_template_directory() . '/templates/blocks/Block.php';

class Gallery extends Block {
    public $html;
    public $block_type = 'gallery';
    public $display_name = 'Galerie';
    public $layouts = ['default', 'grid'];

    public function __construct()
    {
        parent::__construct('gallery', ['custom_css', 'title','gallery', 'layout', 'display_desc']);
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
        $data['values'] = $values['values'] ?? $values;

        // Utiliser le type de bloc pour choisir le bon view.twig
        $template_path = 'blocks/' . $this->block_type . '/view.twig';

        if (!empty($data['values']['gallery']) && is_array($data['values']['gallery'])) {
            $gallery = [];

            foreach ($data['values']['gallery'] as $image_url) {
                $attachment_id = attachment_url_to_postid($image_url);

                if ($attachment_id) {
                    $attachment = get_post($attachment_id);

                    $gallery[] = [
                        'url'         => $image_url,
                        'alt'         => get_post_meta($attachment_id, '_wp_attachment_image_alt', true),
                        'title'       => $attachment->post_title,
                        'caption'     => $attachment->post_excerpt,
                        'description' => $attachment->post_content,
                    ];
                } else {
                    // fallback si jamais l'image n'est pas un media WP (cas rare)
                    $gallery[] = ['url' => $image_url];
                }
            }

            $data['values']['gallery'] = $gallery;
        }


        Timber::render($template_path, $data);
    }


    public function getHTML() {
        ob_start();
        include __DIR__ . '/admin.php';
        return ob_get_clean();
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