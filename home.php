<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use Timber\Timber;

$context = Timber::context();
$context['menu'] = \Timber\Timber::get_menu('primary');
Timber::render('/pages/home.twig', $context);