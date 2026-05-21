<?php
if (!defined('ABSPATH')) exit;

/**
 * Template: Category Page
 * Used for category landing pages
 */
get_header();

$logo_manager = new DPDT_Logo_Manager();
$page_slug = get_post_field('post_name', get_the_ID());
$category_logo = $logo_manager->get_category_logo($page_slug);
?>
<div class="dpdt-page-wrapper dpdt-category-page">
    <div class="dpdt-container">
        <div class="dpdt-page-header">
            <?php if ($category_logo) : ?>
                <div class="dpdt-category-icon">
                    <img src="<?php echo esc_url($category_logo); ?>" alt="<?php the_title_attribute(); ?>" />
                </div>
            <?php endif; ?>
            <h1><?php the_title(); ?></h1>
        </div>

        <div class="dpdt-page-content">
            <?php
            while (have_posts()) :
                the_post();
                the_content();
            endwhile;
            ?>
        </div>

        <?php
        // Show child pages if any
        $child_pages = get_pages(array('parent' => get_the_ID(), 'sort_column' => 'menu_order'));
        if (!empty($child_pages)) :
        ?>
            <div class="dpdt-child-pages">
                <h2><?php esc_html_e('এই বিভাগে', 'dpdt-trademark'); ?></h2>
                <div class="dpdt-child-grid">
                    <?php foreach ($child_pages as $child) : ?>
                        <a href="<?php echo get_permalink($child->ID); ?>" class="dpdt-child-card">
                            <h3><?php echo esc_html($child->post_title); ?></h3>
                            <?php if ($child->post_excerpt) : ?>
                                <p><?php echo esc_html($child->post_excerpt); ?></p>
                            <?php endif; ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php get_footer(); ?>
