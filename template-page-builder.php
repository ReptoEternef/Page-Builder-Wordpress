<?php
/**
 * Template Name: Page Builder
 */

/* use Timber\Timber;

$context = Timber::context();
$post_id = get_the_ID();

// Récupération des blocks depuis la meta
$json_blocks = get_post_meta($post_id, 'blocks_data', true);
$blocks = $json_blocks ? json_decode($json_blocks, true) : [];

// Préparation pour Twig
$rendered_blocks = [];
foreach ($blocks as $block) {
    $layout = $block['type'] ?? 'default'; // je pense que tu veux `type` plutôt que `layout`
    $rendered_blocks[] = array_merge($block, [
        'layout' => $layout,
    ]);
}

$context['rendered_blocks'] = $rendered_blocks;

// Render Twig
Timber::render('page-builder.twig', $context);
 */