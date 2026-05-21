<?php
if (!defined('ABSPATH')) exit;

get_header();
?>
<main id="primary" class="site-main">
    <div class="container">
        <div class="page-content-wrapper">
            <?php
            while (have_posts()) :
                the_post();
                get_template_part('template-parts/content', 'page');
            endwhile;
            ?>
        </div>
    </div>
</main>
<?php
get_footer();
