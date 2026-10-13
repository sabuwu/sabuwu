<?php
defined('ABSPATH') || exit;
if (is_home()) {
    get_template_part('index');
    return;
}
get_header();
get_template_part('template-parts/portfolio');
get_footer();
