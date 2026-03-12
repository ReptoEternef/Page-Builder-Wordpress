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

//var_dump($blocks);


function render_block_context($block, $availableBlocks) {
    $type = $block['type'] ?? 'default';

    $block['values'] = $block['values'] ?? [];
    $block['values']['layout'] = $block['values']['layout'] ?? 'default';
    $block['children'] = $block['children'] ?? [];
    $block['id'] = $block['id'] ?? [];

    // Rendu des enfants
    $renderedChildren = [];
    foreach ($block['children'] as $child) {
        $renderedChildren[] = render_block_context($child, $availableBlocks);
    }

    if (!isset($availableBlocks[$type])) {
        return [
            'type' => $type,
            'html' => "<div style='color:red;'>⚠️ Bloc inconnu : $type</div>",
            'values' => $block,
            'children' => $renderedChildren,
            'options'  => [],
        ];
    }

    $instance = $availableBlocks[$type];

    ob_start();
    $instance->renderFrontend($block);
    $html = ob_get_clean();

    return [
        'type' => $type,
        'html' => $html,
        'values' => $block['values'],
        'id' => $block['id'] ?? null,
        'children' => $renderedChildren,
        'options'  => $instance->options ?? [],
    ];
}

add_action('wp_head', function() use ($blocks) {
    $all_css = '';
    foreach ($blocks as $block) {
        if (!empty($block['values']['custom_css'])) {
            $id = $block['id'];
            $css = str_replace('##', '#' . $id, $block['values']['custom_css']);
            $all_css .= $css . "\n";
        }
    }
    if ($all_css) {
        echo '<style>' . $all_css . '</style>';
    }
});


$rendered_blocks = [];
foreach ($blocks as $block) {
    $rendered_blocks[] = render_block_context($block, $availableBlocks);
}
/* var_dump($rendered_blocks);
exit(); */

$context['rendered_blocks'] = $rendered_blocks;

// Rendu du builder
Timber::render('page-builder.twig', $context);