<?php
defined('ABSPATH') || exit;

$latest_posts = new WP_Query([
    'post_type' => 'post',
    'post_status' => 'publish',
    'posts_per_page' => 3,
    'orderby' => 'date',
    'order' => 'DESC',
    'has_password' => false,
    'ignore_sticky_posts' => true,
    'no_found_rows' => true,
]);

if (!$latest_posts->have_posts()) {
    return;
}
?>
<section id="publicacoes" aria-labelledby="latest-posts-heading">
    <div class="container section-shell">
        <aside class="section-index">
            <small><?php esc_html_e('Blog', 'sabuwu'); ?></small>
            <h2 id="latest-posts-heading"><?php esc_html_e('Últimas publicações', 'sabuwu'); ?></h2>
        </aside>
        <div class="section-content">
            <div class="projects">
                <?php while ($latest_posts->have_posts()) : $latest_posts->the_post(); ?>
                    <article id="latest-post-<?php the_ID(); ?>" <?php post_class('project reveal'); ?>>
                        <div class="project-no"><?php echo esc_html(sprintf('%02d — %02d', $latest_posts->current_post + 1, $latest_posts->post_count)); ?></div>
                        <div>
                            <h3><a href="<?php echo esc_url(get_permalink()); ?>"><?php echo esc_html(get_the_title()); ?></a></h3>
                            <p><?php echo esc_html(wp_trim_words(wp_strip_all_tags(get_the_excerpt()), 36, '…')); ?></p>
                            <div class="link-row">
                                <a href="<?php echo esc_url(get_permalink()); ?>">
                                    <?php esc_html_e('Ler publicação →', 'sabuwu'); ?>
                                    <span class="screen-reader-text"><?php echo esc_html(get_the_title()); ?></span>
                                </a>
                            </div>
                        </div>
                        <div class="project-metrics">
                            <div class="metric">
                                <strong><time datetime="<?php echo esc_attr(get_the_date(DATE_W3C)); ?>"><?php echo esc_html(get_the_date('d/m/Y')); ?></time></strong>
                                <span><?php esc_html_e('Publicado em', 'sabuwu'); ?></span>
                            </div>
                        </div>
                    </article>
                <?php endwhile; ?>
            </div>
            <div class="hero-actions">
                <a class="btn btn-ghost" href="<?php echo esc_url(sabuwu_blog_url()); ?>"><?php esc_html_e('Ver todos os posts', 'sabuwu'); ?></a>
            </div>
        </div>
    </div>
</section>
<?php wp_reset_postdata(); ?>
