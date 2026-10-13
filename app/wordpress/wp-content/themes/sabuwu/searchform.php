<?php defined('ABSPATH') || exit; $search_id = wp_unique_id('sabuwu-search-'); ?>
<form role="search" method="get" class="search-form" action="<?php echo esc_url(home_url('/')); ?>">
    <label for="<?php echo esc_attr($search_id); ?>"><?php esc_html_e('Buscar no site', 'sabuwu'); ?></label>
    <div class="search-controls">
        <input type="search" id="<?php echo esc_attr($search_id); ?>" name="s" value="<?php echo esc_attr(get_search_query()); ?>" placeholder="<?php esc_attr_e('Digite sua busca…', 'sabuwu'); ?>" required>
        <button type="submit" class="btn btn-primary"><?php esc_html_e('Buscar', 'sabuwu'); ?></button>
    </div>
</form>
