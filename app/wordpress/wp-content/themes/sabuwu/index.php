<?php defined('ABSPATH') || exit; get_header(); ?>
<main id="content" class="container document-shell">
    <?php if (is_search()) : ?>
        <h1 class="document-title"><?php printf(esc_html__('Resultados para: %s', 'sabuwu'), esc_html(get_search_query())); ?></h1>
    <?php elseif (is_archive()) : ?>
        <h1 class="document-title"><?php the_archive_title(); ?></h1>
        <?php the_archive_description('<div class="entry-content">', '</div>'); ?>
    <?php elseif (is_home()) : ?>
        <h1 class="document-title"><?php echo esc_html(get_option('page_for_posts') ? get_the_title(get_option('page_for_posts')) : __('Publicações', 'sabuwu')); ?></h1>
    <?php endif; ?>
    <?php if (have_posts()) : ?>
        <?php while (have_posts()) : the_post(); ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class('entry'); ?>>
                <?php if (is_singular()) : ?>
                    <h1 class="document-title"><?php echo esc_html(get_the_title()); ?></h1>
                    <?php if (!post_password_required() && has_post_thumbnail()) : ?><div class="entry-image"><?php the_post_thumbnail('large'); ?></div><?php endif; ?>
                    <div class="entry-content"><?php the_content(); wp_link_pages(['before' => '<nav class="page-links" aria-label="' . esc_attr__('Páginas do conteúdo', 'sabuwu') . '">', 'after' => '</nav>']); ?></div>
                    <?php if (comments_open() || get_comments_number()) { comments_template(); } ?>
                <?php else : ?>
                    <h2><a href="<?php the_permalink(); ?>"><?php echo esc_html(get_the_title()); ?></a></h2>
                    <?php if (has_post_thumbnail()) : ?><a class="entry-image" href="<?php the_permalink(); ?>"><?php the_post_thumbnail('large'); ?></a><?php endif; ?>
                    <div class="entry-content"><?php the_excerpt(); ?></div>
                    <a class="btn btn-ghost" href="<?php the_permalink(); ?>"><?php esc_html_e('Ler mais →', 'sabuwu'); ?></a>
                <?php endif; ?>
            </article>
        <?php endwhile; ?>
        <?php if (!is_singular()) { the_posts_pagination(); } ?>
    <?php else : ?>
        <h2><?php esc_html_e('Nenhum conteúdo encontrado.', 'sabuwu'); ?></h2>
        <p><?php esc_html_e('Tente pesquisar outro termo.', 'sabuwu'); ?></p>
        <?php get_search_form(); ?>
    <?php endif; ?>
</main>
<?php get_footer(); ?>

