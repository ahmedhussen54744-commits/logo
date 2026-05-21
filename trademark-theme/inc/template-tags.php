<?php
if (!defined('ABSPATH')) exit;

/**
 * Template Tags
 * Helper functions for use in theme templates
 */

/**
 * Fallback menu if no menu is assigned
 */
function dpdt_fallback_menu() {
    echo '<div class="menu-container">';
    echo '<ul class="primary-menu-list">';
    echo '<li class="menu-item"><a href="' . esc_url(home_url('/')) . '">' . esc_html__('হোম', 'dpdt-theme') . '</a></li>';

    $pages = get_pages(array(
        'parent' => 0,
        'sort_column' => 'menu_order',
        'number' => 10,
    ));

    foreach ($pages as $page) {
        $children = get_pages(array('parent' => $page->ID, 'number' => 5));
        $has_children = !empty($children) ? ' class="menu-item-has-children"' : '';

        echo '<li' . $has_children . '>';
        echo '<a href="' . get_permalink($page->ID) . '">' . esc_html($page->post_title) . '</a>';

        if (!empty($children)) {
            echo '<ul class="sub-menu">';
            foreach ($children as $child) {
                echo '<li><a href="' . get_permalink($child->ID) . '">' . esc_html($child->post_title) . '</a></li>';
            }
            echo '</ul>';
        }

        echo '</li>';
    }

    echo '</ul>';
    echo '</div>';
}

/**
 * Get site breadcrumb
 */
function dpdt_breadcrumb() {
    if (is_front_page()) return;

    echo '<nav class="breadcrumb" aria-label="Breadcrumb">';
    echo '<a href="' . esc_url(home_url('/')) . '">' . esc_html__('হোম', 'dpdt-theme') . '</a>';

    if (is_page()) {
        $ancestors = get_post_ancestors(get_the_ID());
        $ancestors = array_reverse($ancestors);
        foreach ($ancestors as $ancestor) {
            echo ' <span class="separator">/</span> ';
            echo '<a href="' . get_permalink($ancestor) . '">' . get_the_title($ancestor) . '</a>';
        }
        echo ' <span class="separator">/</span> ';
        echo '<span class="current">' . get_the_title() . '</span>';
    } elseif (is_single()) {
        $categories = get_the_category();
        if (!empty($categories)) {
            echo ' <span class="separator">/</span> ';
            echo '<a href="' . esc_url(get_category_link($categories[0]->term_id)) . '">' . esc_html($categories[0]->name) . '</a>';
        }
        echo ' <span class="separator">/</span> ';
        echo '<span class="current">' . get_the_title() . '</span>';
    } elseif (is_archive()) {
        echo ' <span class="separator">/</span> ';
        echo '<span class="current">' . get_the_archive_title() . '</span>';
    } elseif (is_search()) {
        echo ' <span class="separator">/</span> ';
        echo '<span class="current">' . esc_html__('অনুসন্ধান ফলাফল', 'dpdt-theme') . '</span>';
    }

    echo '</nav>';
}

/**
 * Posted on date
 */
function dpdt_posted_on() {
    $time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time>';
    printf($time_string, esc_attr(get_the_date(DATE_W3C)), esc_html(get_the_date()));
}

/**
 * Posted by author
 */
function dpdt_posted_by() {
    printf(
        '<span class="author vcard"><a href="%1$s">%2$s</a></span>',
        esc_url(get_author_posts_url(get_the_author_meta('ID'))),
        esc_html(get_the_author())
    );
}

/**
 * Get reading time estimate
 */
function dpdt_reading_time($post_id = null) {
    if (!$post_id) $post_id = get_the_ID();
    $content = get_post_field('post_content', $post_id);
    $word_count = str_word_count(strip_tags($content));
    $reading_time = max(1, ceil($word_count / 200));
    return sprintf(__('%d মিনিট পড়া', 'dpdt-theme'), $reading_time);
}

/**
 * Social share buttons
 */
function dpdt_social_share() {
    $url = urlencode(get_permalink());
    $title = urlencode(get_the_title());

    echo '<div class="social-share">';
    echo '<span class="share-label">' . esc_html__('শেয়ার:', 'dpdt-theme') . '</span>';
    echo '<a href="https://www.facebook.com/sharer/sharer.php?u=' . $url . '" target="_blank" rel="noopener" class="share-btn share-facebook" aria-label="Share on Facebook"><span class="dashicons dashicons-facebook-alt"></span></a>';
    echo '<a href="https://twitter.com/intent/tweet?url=' . $url . '&text=' . $title . '" target="_blank" rel="noopener" class="share-btn share-twitter" aria-label="Share on Twitter"><span class="dashicons dashicons-twitter"></span></a>';
    echo '</div>';
}
