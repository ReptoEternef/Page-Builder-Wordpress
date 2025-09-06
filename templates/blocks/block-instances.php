<?php

// List of blocks directory
require_once get_template_directory() . '/templates/blocks/Block.php';
require_once get_template_directory() . '/templates/blocks/text/TextBlock.php';

$availableBlocks = [
    'text' => new Text(),
];

$GLOBALS['availableBlocks'] = $availableBlocks;