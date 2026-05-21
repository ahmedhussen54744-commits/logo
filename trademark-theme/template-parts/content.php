<?php
if (!defined('ABSPATH')) exit;
?>
<article id="post-<?php the_ID(); ?>" <?php post_class('post-card'); ?>>
    <?php if (has_post_thumbnail()) : ?>
        <div class="post-card-thumbnail">
            <a href="<?php the_permalink(); ?>">
                <?php the_post_thumbnail('dpdt-card'); ?>
            </a>
        </div>
    <?php endif; ?>

    <div class="post-card-content">
        <header class="entry-header">
            <?php the_title(sprintf('<h2 class="entry-title"><a href="%s">', esc_url(get_permalink())), '</a></h2>'); ?>
            <div class="entry-meta">
                <span class="posted-on">
                    <span class="dashicons dashicons-calendar-alt"></span>
                    <?php echo get_the_date(); ?>
                </span>
            </div>
        </header>

        <div class="entry-excerpt">
            <?php the_excerpt(); ?>
        </div>

        <footer class="entry-footer">
            <a href="<?php the_permalink(); ?>" class="read-more-link">
                <?php esc_html_e('বিস্তারিত পড়ুন', 'dpdt-theme'); ?> &rarr;
            </a>
        </footer>
    </div>
</article>
