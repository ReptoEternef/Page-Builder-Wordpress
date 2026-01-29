<?php
if (!defined('ABSPATH')) {
    exit;
}

require_once get_template_directory() . '/lib/plugin-update-checker/plugin-update-checker.php';

use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

$themeUpdateChecker = PucFactory::buildUpdateChecker(
    'https://github.com/ReptoEternef/Page-Builder-Wordpress.git',
    get_template_directory() . '/style.css',
    'openbuilderWP'
);

// Branche surveillée
$themeUpdateChecker->setBranch('main');

// Optionnel mais recommandé
//$themeUpdateChecker->getVcsApi()->enableReleaseAssets();
