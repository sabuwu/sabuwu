<?php defined('ABSPATH') || exit; ?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php echo esc_attr(get_bloginfo('charset')); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link" href="#content"><?php esc_html_e('Ir para o conteúdo', 'sabuwu'); ?></a>
<header class="topbar">
    <div class="container topbar-inner">
        <?php if (has_custom_logo()) : ?>
            <div class="logo"><?php the_custom_logo(); ?></div>
        <?php else : ?>
            <a class="logo" href="<?php echo esc_url(sabuwu_section_url('inicio')); ?>"><?php echo esc_html(sabuwu_setting('brand')); ?></a>
        <?php endif; ?>
        <button class="nav-toggle" type="button" aria-controls="primary-navigation" aria-expanded="false" hidden><?php esc_html_e('Menu', 'sabuwu'); ?></button>
        <nav class="nav" id="primary-navigation" aria-label="<?php esc_attr_e('Navegação principal', 'sabuwu'); ?>">
            <?php wp_nav_menu(['theme_location' => 'primary', 'container' => false, 'menu_class' => 'nav-list', 'depth' => 1, 'fallback_cb' => 'sabuwu_default_navigation']); ?>
        </nav>
        <?php if (sabuwu_setting('availability_show') && sabuwu_setting('availability_text')) : ?>
            <div class="availability"><?php echo esc_html(sabuwu_setting('availability_text')); ?></div>
        <?php endif; ?>
    </div>
</header>
