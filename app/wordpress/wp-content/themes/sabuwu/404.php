<?php defined('ABSPATH') || exit; get_header(); ?>
<main id="content" class="container document-shell">
    <div class="kicker">404</div>
    <h1 class="document-title"><?php esc_html_e('Página não encontrada.', 'sabuwu'); ?></h1>
    <p><?php esc_html_e('Use a busca ou volte ao portfólio.', 'sabuwu'); ?></p>
    <?php get_search_form(); ?>
    <a class="btn btn-primary" href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Voltar ao início', 'sabuwu'); ?></a>
</main>
<?php get_footer(); ?>
