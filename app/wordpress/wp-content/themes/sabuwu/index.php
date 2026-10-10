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
<header class="site-header">
    <a class="brand" href="<?php echo esc_url(home_url('/')); ?>"><?php echo esc_html(get_bloginfo('name')); ?></a>
    <nav aria-label="<?php esc_attr_e('Navegação principal', 'sabuwu'); ?>">
        <?php wp_nav_menu(['theme_location' => 'primary', 'container' => false, 'depth' => 1]); ?>
    </nav>
</header>
<main id="content">
    <p class="tagline"><?php echo esc_html(get_bloginfo('description')); ?></p>
    <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
        <article <?php post_class(); ?>>
            <h1><?php echo esc_html(get_the_title()); ?></h1>
            <?php the_content(); ?>
        </article>
    <?php endwhile; the_posts_pagination(); else : ?>
        <h1><?php esc_html_e('Conteúdo não encontrado', 'sabuwu'); ?></h1>
        <?php get_search_form(); ?>
    <?php endif; ?>
</main>
<footer class="site-footer"><?php echo esc_html(get_bloginfo('name')); ?> · WordPress &amp; PHP</footer>
<?php wp_footer(); ?>
</body>
</html>

