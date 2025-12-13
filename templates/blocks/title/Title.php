<?php

/* 
SNIPPETS & TIPS

à remplir :
    > nom du dossier
    > nom du fichier
*/

use Timber\Timber;

require_once get_template_directory() . '/templates/blocks/Block.php';

class Title extends Block {

    public function __construct()
    {
        $json_directory = __DIR__ . DIRECTORY_SEPARATOR . 'config.json';
        $json_config = json_decode(file_get_contents($json_directory), true);

        parent::__construct($json_config['block_type'], $json_config['display_name'], $json_config['fields'], $json_config['layouts']);
    }

    public function renderFrontend($values = [])
    {
        $data = $this->normalizeData();
        $data['values'] = $values['values'] ?? $values;
        $data['block'] = $this->type;
        $data['layouts'] = $this->layouts;

        $template_path = 'blocks/' . $this->type . '/view.twig';
        Timber::render($template_path, $data);
    }
}