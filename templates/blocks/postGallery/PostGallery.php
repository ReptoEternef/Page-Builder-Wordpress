<?php

use Timber\Timber;

require_once get_template_directory() . '/templates/blocks/Block.php';

class PostGallery extends Block {

    public function __construct()
    {
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

        
        if (!empty($data['values']['custom_post_type']) && post_type_exists($data['values']['custom_post_type'])) {
            $cpt_name = $data['values']['custom_post_type'];
            $data['cpt'] = Timber::get_posts([
                'post_type' => $cpt_name,
                'posts_per_page' => -1,
                'post_status' => 'publish',
                'orderby' => 'date',
                'order' => 'DESC',
            ]);

            $post_type_object = get_post_type_object($cpt_name);
            $data['cpt_label'] = $post_type_object ? $post_type_object->label : '';
        } else {
            $data['cpt'] = [];
            $data['cpt_label'] = '';
            error_log('⚠️ Movie CPT non trouvé au moment du rendu.');
        }
        
        $template_path = 'blocks/' . $this->type . '/view.twig';
        Timber::render($template_path, $data);
    }
}