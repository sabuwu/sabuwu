<?php defined('ABSPATH') || exit; get_header(); ?>
<main id="content" class="container document-shell">
    <?php while (have_posts()) : the_post(); ?>
        <article <?php post_class('entry project-detail'); ?>>
            <div class="kicker"><?php esc_html_e('Projeto / Case study', 'sabuwu'); ?></div>
            <h1 class="document-title"><?php echo esc_html(get_the_title()); ?></h1>
            <?php if (post_password_required()) : ?>
                <div class="entry-content"><?php the_content(); ?></div>
            <?php else : ?>
            <?php if (has_post_thumbnail()) : ?><div class="entry-image"><?php the_post_thumbnail('large'); ?></div><?php endif; ?>
            <?php $tags = array_filter(array_map('trim', explode(',', (string) get_post_meta(get_the_ID(), '_sabuwu_tags', true))), static fn(string $tag): bool => $tag !== ''); ?>
            <?php if ($tags) : ?><div class="tags"><?php foreach ($tags as $tag) : ?><span class="tag"><?php echo esc_html($tag); ?></span><?php endforeach; ?></div><?php endif; ?>
            <?php if (has_excerpt()) : ?><p class="hero-note"><?php echo esc_html(get_the_excerpt()); ?></p><?php endif; ?>
            <div class="project-detail-metrics">
                <?php for ($i = 1; $i <= 3; $i++) : ?>
                    <?php $value = get_post_meta(get_the_ID(), '_sabuwu_metric_' . $i . '_value', true); $label = get_post_meta(get_the_ID(), '_sabuwu_metric_' . $i . '_label', true); ?>
                    <?php if ($value !== '' || $label !== '') : ?><div class="metric"><strong><?php echo esc_html($value); ?></strong><span><?php echo esc_html($label); ?></span></div><?php endif; ?>
                <?php endfor; ?>
            </div>
            <div class="entry-content"><?php the_content(); wp_link_pages(); ?></div>
            <?php sabuwu_project_links(['demo_url' => get_post_meta(get_the_ID(), '_sabuwu_demo_url', true), 'repository_url' => get_post_meta(get_the_ID(), '_sabuwu_repository_url', true), 'case_url' => '']); ?>
            <?php endif; ?>
            <p class="back-link"><a class="btn btn-ghost" href="<?php echo esc_url(home_url('/#projetos')); ?>"><?php esc_html_e('← Voltar aos projetos', 'sabuwu'); ?></a></p>
        </article>
    <?php endwhile; ?>
</main>
<?php get_footer(); ?>
