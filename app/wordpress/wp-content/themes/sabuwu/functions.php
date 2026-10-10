<?php
defined('ABSPATH') || exit;
add_action('after_setup_theme', static function (): void {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('responsive-embeds');
    add_theme_support('html5', ['search-form', 'gallery', 'caption', 'style', 'script']);
    register_nav_menus(['primary' => __('Navegação principal', 'sabuwu')]);
});
add_action('wp_enqueue_scripts', static function (): void {
    wp_enqueue_style('sabuwu', get_stylesheet_uri(), [], wp_get_theme()->get('Version'));
});

