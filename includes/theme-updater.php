<?php
/**
 * Theme Update Checker
 * Gère les mises à jour automatiques du thème depuis GitHub
 */

if (!defined('ABSPATH')) {
    exit;
}

// Charger la bibliothèque
require_once get_template_directory() . '/lib/plugin-update-checker/plugin-update-checker.php';

use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

// IMPORTANT : Pour un thème, utilisez buildUpdateChecker avec le bon type
$themeUpdateChecker = PucFactory::buildUpdateChecker(
    'https://github.com/ReptoEternef/Page-Builder-Wordpress',
    get_template_directory() . '/style.css', // Chemin vers style.css
    'openbuilderWP' // Slug du thème (doit correspondre au nom du dossier)
);

// Définir que c'est un thème et non un plugin


// Branche surveillée (par défaut 'master' ou 'main')
$themeUpdateChecker->setBranch('master');

// Optionnel : Authentification GitHub pour éviter les limites de l'API
// $themeUpdateChecker->setAuthentication('votre-github-token-ici');

// Optionnel : Forcer la vérification des mises à jour (utile pour le debug)
// add_filter('puc_request_info_result-openbuilderWP', function($result) {
//     error_log('Update check result: ' . print_r($result, true));
//     return $result;
// });