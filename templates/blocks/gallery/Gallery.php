<?php

use Timber\Timber;

require_once get_template_directory() . '/templates/blocks/Block.php';

class Gallery extends Block {

    public function __construct()
    {
        // Charger le config depuis le bon endroit
        $json_directory = __DIR__ . DIRECTORY_SEPARATOR . 'config.json';
        $json_config = json_decode(file_get_contents($json_directory), true);

        parent::__construct(
            $json_config['block_type'], 
            $json_config['display_name'], 
            $json_config['fields'], 
            $json_config['layouts']
        );
    }

    public function renderFrontend($values = [])
    {
        $data = $this->normalizeData();
        $data['values'] = $values['values'] ?? $values;

        // Utiliser le type de bloc pour choisir le bon view.twig
        $template_path = 'blocks/' . $this->type . '/view.twig';

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
}