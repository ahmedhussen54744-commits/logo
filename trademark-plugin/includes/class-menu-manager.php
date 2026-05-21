<?php
if (!defined('ABSPATH')) exit;

/**
 * Menu Manager Class
 * Auto-creates WordPress navigation menus and assigns pages
 */
class DPDT_Menu_Manager {

    private $menu_name = 'DPDT Primary Menu';
    private $menu_slug = 'dpdt-primary-menu';

    public function __construct() {
        add_action('after_switch_theme', array($this, 'assign_menu_to_location'));
    }

    /**
     * Create default menu with all category pages
     */
    public function create_default_menu() {
        // Delete existing menu if present
        $existing_menu = wp_get_nav_menu_object($this->menu_name);
        if ($existing_menu) {
            wp_delete_nav_menu($existing_menu->term_id);
        }

        // Create the menu
        $menu_id = wp_create_nav_menu($this->menu_name);

        if (is_wp_error($menu_id)) {
            return false;
        }

        // Get category manager for pages data
        $category_manager = new DPDT_Category_Manager();
        $categories = $category_manager->get_categories_list();

        // Add Home first
        wp_update_nav_menu_item($menu_id, 0, array(
            'menu-item-title' => 'হোম',
            'menu-item-url' => home_url('/'),
            'menu-item-status' => 'publish',
            'menu-item-type' => 'custom',
            'menu-item-position' => 0,
        ));

        // Add each category page to menu
        $position = 1;
        foreach ($categories as $category) {
            $page = get_page_by_path($category['slug']);
            if (!$page) continue;

            $parent_item_id = wp_update_nav_menu_item($menu_id, 0, array(
                'menu-item-title' => $category['title'],
                'menu-item-object' => 'page',
                'menu-item-object-id' => $page->ID,
                'menu-item-type' => 'post_type',
                'menu-item-status' => 'publish',
                'menu-item-position' => $position,
            ));

            $position++;

            // Add children
            if (!empty($category['children'])) {
                foreach ($category['children'] as $child) {
                    $child_page = get_page_by_path($child['slug']);
                    if (!$child_page) continue;

                    wp_update_nav_menu_item($menu_id, 0, array(
                        'menu-item-title' => $child['title'],
                        'menu-item-object' => 'page',
                        'menu-item-object-id' => $child_page->ID,
                        'menu-item-type' => 'post_type',
                        'menu-item-status' => 'publish',
                        'menu-item-parent-id' => $parent_item_id,
                        'menu-item-position' => $position,
                    ));
                    $position++;
                }
            }
        }

        // Assign to theme locations
        $this->assign_menu_to_location($menu_id);

        // Store menu ID
        update_option('dpdt_primary_menu_id', $menu_id);

        return $menu_id;
    }

    /**
     * Assign menu to theme menu locations
     */
    public function assign_menu_to_location($menu_id = null) {
        if (!$menu_id) {
            $menu_id = get_option('dpdt_primary_menu_id', 0);
        }

        if (!$menu_id) return;

        $locations = get_theme_mod('nav_menu_locations', array());
        $locations['primary'] = $menu_id;
        $locations['mobile'] = $menu_id;

        set_theme_mod('nav_menu_locations', $locations);
    }

    /**
     * Create footer menu
     */
    public function create_footer_menu() {
        $menu_name = 'DPDT Footer Menu';
        $existing = wp_get_nav_menu_object($menu_name);

        if ($existing) {
            return $existing->term_id;
        }

        $menu_id = wp_create_nav_menu($menu_name);
        if (is_wp_error($menu_id)) return false;

        // Add important footer links
        $footer_links = array(
            array('title' => 'গোপনীয়তা নীতি', 'url' => home_url('/privacy-policy/')),
            array('title' => 'ব্যবহারের শর্তাবলী', 'url' => home_url('/terms/')),
            array('title' => 'যোগাযোগ', 'slug' => 'contact'),
            array('title' => 'সাইটম্যাপ', 'url' => home_url('/sitemap/')),
        );

        $position = 0;
        foreach ($footer_links as $link) {
            if (isset($link['slug'])) {
                $page = get_page_by_path($link['slug']);
                if ($page) {
                    wp_update_nav_menu_item($menu_id, 0, array(
                        'menu-item-title' => $link['title'],
                        'menu-item-object' => 'page',
                        'menu-item-object-id' => $page->ID,
                        'menu-item-type' => 'post_type',
                        'menu-item-status' => 'publish',
                        'menu-item-position' => $position,
                    ));
                }
            } else {
                wp_update_nav_menu_item($menu_id, 0, array(
                    'menu-item-title' => $link['title'],
                    'menu-item-url' => $link['url'],
                    'menu-item-type' => 'custom',
                    'menu-item-status' => 'publish',
                    'menu-item-position' => $position,
                ));
            }
            $position++;
        }

        // Assign to footer location
        $locations = get_theme_mod('nav_menu_locations', array());
        $locations['footer'] = $menu_id;
        set_theme_mod('nav_menu_locations', $locations);

        return $menu_id;
    }

    /**
     * Get menu ID
     */
    public function get_menu_id() {
        return get_option('dpdt_primary_menu_id', 0);
    }

    /**
     * Check if menu exists
     */
    public function menu_exists() {
        $menu = wp_get_nav_menu_object($this->menu_name);
        return $menu !== false;
    }

    /**
     * Rebuild menu from current pages
     */
    public function rebuild_menu() {
        return $this->create_default_menu();
    }
}
