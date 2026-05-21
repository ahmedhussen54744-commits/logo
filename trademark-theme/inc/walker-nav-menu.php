<?php
if (!defined('ABSPATH')) exit;

/**
 * Custom Walker for Navigation Menu
 * Adds dropdown support with proper classes and accessibility
 */
class DPDT_Walker_Nav_Menu extends Walker_Nav_Menu {

    /**
     * Start level (sub-menu)
     */
    public function start_lvl(&$output, $depth = 0, $args = null) {
        $indent = str_repeat("\t", $depth);
        $classes = array('sub-menu', 'dpdt-dropdown');
        if ($depth > 0) {
            $classes[] = 'dpdt-sub-dropdown';
        }
        $class_names = implode(' ', $classes);
        $output .= "\n{$indent}<ul class=\"{$class_names}\" role=\"menu\">\n";
    }

    /**
     * End level
     */
    public function end_lvl(&$output, $depth = 0, $args = null) {
        $indent = str_repeat("\t", $depth);
        $output .= "{$indent}</ul>\n";
    }

    /**
     * Start element
     */
    public function start_el(&$output, $item, $depth = 0, $args = null, $id = 0) {
        $indent = ($depth) ? str_repeat("\t", $depth) : '';

        $classes = empty($item->classes) ? array() : (array) $item->classes;
        $classes[] = 'menu-item-' . $item->ID;
        $classes[] = 'dpdt-menu-item';

        if ($depth === 0) {
            $classes[] = 'dpdt-menu-item-top';
        }

        // Check if item has children
        if (in_array('menu-item-has-children', $classes)) {
            $classes[] = 'dpdt-has-dropdown';
        }

        // Current page highlighting
        if (in_array('current-menu-item', $classes) || in_array('current-menu-parent', $classes)) {
            $classes[] = 'dpdt-active';
        }

        $class_names = implode(' ', apply_filters('nav_menu_css_class', array_filter($classes), $item, $args, $depth));
        $class_names = $class_names ? ' class="' . esc_attr($class_names) . '"' : '';

        $id_attr = apply_filters('nav_menu_item_id', 'menu-item-' . $item->ID, $item, $args, $depth);
        $id_attr = $id_attr ? ' id="' . esc_attr($id_attr) . '"' : '';

        $output .= $indent . '<li' . $id_attr . $class_names . ' role="none">';

        $atts = array();
        $atts['title'] = !empty($item->attr_title) ? $item->attr_title : '';
        $atts['target'] = !empty($item->target) ? $item->target : '';
        $atts['rel'] = !empty($item->xfn) ? $item->xfn : '';
        $atts['href'] = !empty($item->url) ? $item->url : '';
        $atts['class'] = 'dpdt-menu-link';
        $atts['role'] = 'menuitem';

        if ($depth === 0) {
            $atts['class'] .= ' dpdt-menu-link-top';
        }

        // Add aria attributes for dropdowns
        if (in_array('menu-item-has-children', (array) $item->classes)) {
            $atts['aria-haspopup'] = 'true';
            $atts['aria-expanded'] = 'false';
        }

        $atts = apply_filters('nav_menu_link_attributes', $atts, $item, $args, $depth);

        $attributes = '';
        foreach ($atts as $attr => $value) {
            if (!empty($value)) {
                $value = ('href' === $attr) ? esc_url($value) : esc_attr($value);
                $attributes .= ' ' . $attr . '="' . $value . '"';
            }
        }

        $title = apply_filters('the_title', $item->title, $item->ID);
        $title = apply_filters('nav_menu_item_title', $title, $item, $args, $depth);

        $item_output = isset($args->before) ? $args->before : '';
        $item_output .= '<a' . $attributes . '>';
        $item_output .= (isset($args->link_before) ? $args->link_before : '') . $title . (isset($args->link_after) ? $args->link_after : '');

        // Add dropdown arrow for items with children
        if (in_array('menu-item-has-children', (array) $item->classes)) {
            $item_output .= ' <span class="dpdt-dropdown-arrow" aria-hidden="true">▾</span>';
        }

        $item_output .= '</a>';

        // Add toggle button for mobile
        if (in_array('menu-item-has-children', (array) $item->classes)) {
            $item_output .= '<button class="dpdt-dropdown-toggle" aria-label="' . esc_attr__('সাবমেনু খুলুন', 'dpdt-theme') . '" aria-expanded="false"><span>+</span></button>';
        }

        $item_output .= isset($args->after) ? $args->after : '';

        $output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
    }

    /**
     * End element
     */
    public function end_el(&$output, $item, $depth = 0, $args = null) {
        $output .= "</li>\n";
    }
}
