<?php
if (!defined('ABSPATH')) exit;
?>
<form role="search" method="get" class="search-form" action="<?php echo esc_url(home_url('/')); ?>">
    <label class="screen-reader-text" for="search-field"><?php esc_html_e('অনুসন্ধান:', 'dpdt-theme'); ?></label>
    <div class="search-input-group">
        <input type="search" id="search-field" class="search-field" placeholder="<?php esc_attr_e('অনুসন্ধান করুন...', 'dpdt-theme'); ?>" value="<?php echo get_search_query(); ?>" name="s" />
        <button type="submit" class="search-submit">
            <span class="dashicons dashicons-search"></span>
            <span class="screen-reader-text"><?php esc_html_e('অনুসন্ধান', 'dpdt-theme'); ?></span>
        </button>
    </div>
</form>
