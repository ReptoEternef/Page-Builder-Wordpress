<?php

use Timber\Timber;

require_once get_template_directory() . '/templates/blocks/Block.php';

class Text extends Block {

    public function __construct()
    {
        // Déterminer d'abord où se trouve le bloc
        $block_path = self::resolveBlockPathStatic('text');
        
        // Charger le config depuis le bon endroit
        $json_directory = $block_path . DIRECTORY_SEPARATOR . 'config.json';
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
        $data['block'] = $this->type;
        $data['layouts'] = $this->layouts;
        $data = array_merge(Timber::context(), $data);

        $template_path = 'blocks/' . $this->type . '/view.twig';
        Timber::render($template_path, $data);
    }
}