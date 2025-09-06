<?php
$context = \Timber\Timber::context();
$context['posts'] = \Timber\Timber::get_posts();
$context['menu'] = \Timber\Timber::get_menu('primary');

\Timber\Timber::render('/pages/base.twig', $context);

