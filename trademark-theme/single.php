<?php
if (!defined('ABSPATH')) exit;

get_header();
?>
<main id="primary" class="site-main">
    <div class="container">
        <div class="single-content-wrapper">
            <article class="single-article">
                <?php
                while (have_posts()) :
                    the_post();
                ?>
                    <header class="entry-header">
                        <?php the_title('<h1 class="entry-title">', '</h1>'); ?>
                        <div class="entry-meta">
                            <span class="posted-on">
                                <span class="dashicons dashicons-calendar"></span>
                                <?php echo get_the_date(); ?>
                            </span>
                            <?php if (get_the_category()) : ?>
                                <span class="posted-in">
                                    <span class="dashicons dashicons-category"></span>
                                    <?php the_category(', '); ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    </header>

                    <?php if (has_post_thumbnail()) : ?>
                        <div class="entry-thumbnail">
                            <?php the_post_thumbnail('large'); ?>
                        </div>
                    <?php endif; ?>

                    <div class="entry-content">
                        <?php the_content(); ?>
                    </div>

                    <footer class="entry-footer">
                        <?php
                        $tags = get_the_tags();
                        if ($tags) : ?>
                            <div class="entry-tags">
                                <span class="dashicons dashicons-tag"></span>
                                <?php the_tags('', ', ', ''); ?>
                            </div>
                        <?php endif; ?>
                    </footer>

                    <nav class="post-navigation">
                        <?php
                        the_post_navigation(array(
                            'prev_text' => '<span class="nav-subtitle">' . esc_html__('পূর্ববর্তী', 'dpdt-theme') . '</span> <span class="nav-title">%title</span>',
                            'next_text' => '<span class="nav-subtitle">' . esc_html__('পরবর্তী', 'dpdt-theme') . '</span> <span class="nav-title">%title</span>',
                        ));
                        ?>
                    </nav>
                <?php endwhile; ?>
            </article>

            <?php get_sidebar(); ?>
        </div>
    </div>
</main>
<?php
get_footer();
