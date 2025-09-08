<?php
use Timber\Timber;

$context = Timber::context();
$post_id = get_the_ID();

// Récupération des blocks JSON
$json_blocks = get_post_meta($post_id, '_page_blocks', true);
$blocks = $json_blocks ? json_decode($json_blocks, true) : [];

// Préparer les blocks pour Twig
$rendered_blocks = [];
foreach ($blocks as $block) {
    $layout = $block['type'] ?? 'default'; // clé "type" du JSON
    $rendered_blocks[] = array_merge($block, [
        'layout' => $layout,
    ]);
}

$context['rendered_blocks'] = $rendered_blocks;

// Rendu du builder
Timber::render('page-builder.twig', $context);