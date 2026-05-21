<?php
if (!defined('ABSPATH')) exit;

get_header();
?>
<main id="primary" class="site-main">
    <div class="container">
        <?php if (have_posts()) : ?>
            <div class="posts-grid">
                <?php while (have_posts()) : the_post(); ?>
                    <?php get_template_part('template-parts/content'); ?>
                <?php endwhile; ?>
            </div>

            <div class="pagination-wrapper">
                <?php
                the_posts_pagination(array(
                    'mid_size' => 2,
                    'prev_text' => '&laquo; ' . __('পূর্ববর্তী', 'dpdt-theme'),
                    'next_text' => __('পরবর্তী', 'dpdt-theme') . ' &raquo;',
                ));
                ?>
            </div>
        <?php else : ?>
            <div class="no-results">
                <h2><?php esc_html_e('কোনো পোস্ট পাওয়া যায়নি', 'dpdt-theme'); ?></h2>
                <p><?php esc_html_e('আপনার অনুসন্ধানে কোনো ফলাফল পাওয়া যায়নি।', 'dpdt-theme'); ?></p>
            </div>
        <?php endif; ?>
    </div>
</main>
<?php
get_footer();
