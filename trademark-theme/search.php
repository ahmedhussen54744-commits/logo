<?php
if (!defined('ABSPATH')) exit;

get_header();
?>
<main id="primary" class="site-main">
    <div class="container">
        <header class="page-header">
            <h1 class="page-title">
                <?php printf(esc_html__('অনুসন্ধান ফলাফল: "%s"', 'dpdt-theme'), get_search_query()); ?>
            </h1>
        </header>

        <?php if (have_posts()) : ?>
            <div class="search-results">
                <?php while (have_posts()) : the_post(); ?>
                    <article class="search-result-item">
                        <h2 class="entry-title">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h2>
                        <div class="entry-meta">
                            <span class="posted-on"><?php echo get_the_date(); ?></span>
                            <span class="post-type"><?php echo get_post_type_object(get_post_type())->labels->singular_name; ?></span>
                        </div>
                        <div class="entry-excerpt">
                            <?php the_excerpt(); ?>
                        </div>
                    </article>
                <?php endwhile; ?>
            </div>

            <?php the_posts_pagination(); ?>
        <?php else : ?>
            <div class="no-results">
                <p><?php esc_html_e('আপনার অনুসন্ধানে কোনো ফলাফল পাওয়া যায়নি। অন্য শব্দ দিয়ে চেষ্টা করুন।', 'dpdt-theme'); ?></p>
                <?php get_search_form(); ?>
            </div>
        <?php endif; ?>
    </div>
</main>
<?php
get_footer();
