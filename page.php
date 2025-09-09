<?php
use Timber\Timber;

// Contexte global
$context = Timber::context();
$post_id = get_the_ID();

// Récupération des blocks JSON
$json_blocks = get_post_meta($post_id, '_page_blocks', true);
$blocks = $json_blocks ? json_decode($json_blocks, true) : [];

// Récupère tous les blocs disponibles (Text, Movie, etc.)
$availableBlocks = get_library();

$rendered_blocks = [];


foreach ($blocks as $block) {
    $layout = $block['type'] ?? 'default';
    
    if (isset($availableBlocks[$layout])) {
        $block_instance = $availableBlocks[$layout];
        
        ob_start();
        $block_instance->renderFrontend($block); // injecte les values
        $html = ob_get_clean();
        
        $rendered_blocks[] = [
            'layout' => $layout,
            'html'   => $html,
            'values' => $block,
        ];
    } else {
        $rendered_blocks[] = [
            'layout' => $layout,
            'html'   => '<div style="color:red;">⚠️ Template ou classe manquant pour le bloc : ' . $layout . '</div>',
            'values' => $block,
        ];
    }
}

//var_dump($rendered_blocks[0]['values']);
$context['rendered_blocks'] = $rendered_blocks;

// Rendu du builder
Timber::render('page-builder.twig', $context);
