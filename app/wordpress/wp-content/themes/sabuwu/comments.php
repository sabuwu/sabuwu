<?php defined('ABSPATH') || exit; if (post_password_required()) { return; } ?>
<div id="comments" class="comments-area">
    <?php if (have_comments()) : ?>
        <h2><?php esc_html_e('Comentários', 'sabuwu'); ?></h2>
        <ol class="comment-list"><?php wp_list_comments(['style' => 'ol', 'short_ping' => true, 'avatar_size' => 40]); ?></ol>
        <?php the_comments_navigation(); ?>
        <?php if (!comments_open()) : ?><p><?php esc_html_e('Os comentários estão encerrados.', 'sabuwu'); ?></p><?php endif; ?>
    <?php endif; ?>
    <?php comment_form(); ?>
</div>
