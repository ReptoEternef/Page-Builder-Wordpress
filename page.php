<?php
use Timber\Timber;

// Contexte global
$context = Timber::context();
$post_id = get_the_ID();

// Récupération des blocks JSON
$json_blocks = get_post_meta($post_id, '_page_blocks', true);
$blocks = $json_blocks ? json_decode($json_blocks, true) : [];

// Récupère tous les blocs disponibles (Text, Movie, etc.)
$availableBlocks = obwp_get_library();

$rendered_blocks = [];


function render_block_context($block, $availableBlocks) {
    $output = [];

    $type = $block['type'] ?? 'default';
    $block['values'] = $block['values'] ?? [];
    $block['values']['layout'] = $block['values']['layout'] ?? 'default';

/*     if (!empty($block['children'])) {
        foreach ($block['children'] as $child) {
            $output = array_merge($output, render_block_context($child, $availableBlocks));
        }
    } else {
    } */
    if (isset($availableBlocks[$type])) {
        $instance = $availableBlocks[$type];

        ob_start();
        $instance->renderFrontend($block);
        $html = ob_get_clean();

        $output[] = [
            'type' => $type,
            'html' => $html,
            'values' => $block,
        ];
    } else {
        $output[] = [
            'type' => $type,
            'html' => "<div style='color:red;'>⚠️ Bloc inconnu : $type</div>",
            'values' => $block,
        ];
    }

    return $output;
}



$rendered_blocks = [];
foreach ($blocks as $block) {
    $rendered_blocks = array_merge($rendered_blocks, render_block_context($block, $availableBlocks));
}

/* if (!empty($block['html'])) {
    echo nl2br(htmlspecialchars($block['html']));
} else {
    echo "Pas de HTML pour ce block.";
} */



$context['rendered_blocks'] = $rendered_blocks;

// Rendu du builder
Timber::render('page-builder.twig', $context);