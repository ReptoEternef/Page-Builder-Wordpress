<?php
/* Fallback if no template is selected */

$context = \Timber\Timber::context();
$context['posts'] = \Timber\Timber::get_posts();
$context['menu'] = \Timber\Timber::get_menu();

\Timber\Timber::render('/pages/base.twig', $context);

