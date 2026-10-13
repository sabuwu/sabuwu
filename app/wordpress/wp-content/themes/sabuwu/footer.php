<?php defined('ABSPATH') || exit; ?>
<footer class="site-footer">
    <div class="container footer-inner">
        <span>© <?php echo esc_html(wp_date('Y')); ?> <?php echo esc_html(sabuwu_setting('copyright_name')); ?></span>
        <div class="footer-links">
            <a href="#<?php echo sabuwu_is_portfolio() ? 'inicio' : 'content'; ?>"><?php esc_html_e('Topo ↑', 'sabuwu'); ?></a>
            <?php foreach (['github_url' => 'GitHub', 'discord_url' => 'Discord'] as $key => $label) : ?>
                <?php if (sabuwu_setting($key)) : ?>
                    <a href="<?php echo esc_url(sabuwu_setting($key)); ?>"<?php echo sabuwu_link_attributes(sabuwu_setting($key)); ?>><?php echo esc_html($label); ?></a>
                <?php endif; ?>
            <?php endforeach; ?>
            <?php if (get_privacy_policy_url()) : ?>
                <a href="<?php echo esc_url(get_privacy_policy_url()); ?>"><?php esc_html_e('Privacidade', 'sabuwu'); ?></a>
            <?php endif; ?>
        </div>
    </div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
