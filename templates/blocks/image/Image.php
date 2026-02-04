<?php

use Timber\Timber;

require_once get_template_directory() . '/templates/blocks/Block.php';

class Image extends Block {

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
        $context = Timber::context();
        
        $context['values'] = $values['values'] ?? $values;
        $context['block'] = $this->type;
        $context['layouts'] = $this->layouts;

        $template_path = 'blocks/' . $this->type . '/view.twig';
        Timber::render($template_path, $context);
    }
}
