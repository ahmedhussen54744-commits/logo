<?php
if (!defined('ABSPATH')) exit;

/**
 * Logo Manager Class
 * Manages site header logo, favicon, and category logos from WP Admin
 */
class DPDT_Logo_Manager {

    private $option_key = 'dpdt_logos';

    public function __construct() {
        add_action('init', array($this, 'init'));
    }

    public function init() {
        // Register custom image sizes for logos
        add_image_size('dpdt-header-logo', 300, 80, false);
        add_image_size('dpdt-category-logo', 64, 64, true);
        add_image_size('dpdt-favicon', 32, 32, true);
    }

    /**
     * Get all logos
     */
    public function get_logos() {
        $defaults = array(
            'header_logo' => '',
            'header_logo_id' => 0,
            'header_logo_alt' => 'DPDT Logo',
            'favicon' => '',
            'favicon_id' => 0,
            'footer_logo' => '',
            'footer_logo_id' => 0,
            'category_logos' => array(),
        );

        $logos = get_option($this->option_key, array());
        return wp_parse_args($logos, $defaults);
    }

    /**
     * Get header logo URL
     */
    public function get_header_logo() {
        $logos = $this->get_logos();
        if (!empty($logos['header_logo_id'])) {
            $url = wp_get_attachment_image_url($logos['header_logo_id'], 'dpdt-header-logo');
            return $url ? $url : $logos['header_logo'];
        }
        return $logos['header_logo'];
    }

    /**
     * Get header logo HTML
     */
    public function get_header_logo_html() {
        $logos = $this->get_logos();
        $logo_url = $this->get_header_logo();

        if (empty($logo_url)) {
            return '<span class="dpdt-site-title">' . esc_html(get_option('dpdt_site_name', get_bloginfo('name'))) . '</span>';
        }

        return sprintf(
            '<img src="%s" alt="%s" class="dpdt-header-logo-img" />',
            esc_url($logo_url),
            esc_attr($logos['header_logo_alt'])
        );
    }

    /**
     * Get favicon URL
     */
    public function get_favicon() {
        $logos = $this->get_logos();
        if (!empty($logos['favicon_id'])) {
            $url = wp_get_attachment_image_url($logos['favicon_id'], 'dpdt-favicon');
            return $url ? $url : $logos['favicon'];
        }
        return $logos['favicon'];
    }

    /**
     * Get footer logo URL
     */
    public function get_footer_logo() {
        $logos = $this->get_logos();
        if (!empty($logos['footer_logo_id'])) {
            $url = wp_get_attachment_image_url($logos['footer_logo_id'], 'medium');
            return $url ? $url : $logos['footer_logo'];
        }
        return $logos['footer_logo'];
    }

    /**
     * Get category logo
     */
    public function get_category_logo($category_slug) {
        $logos = $this->get_logos();
        if (isset($logos['category_logos'][$category_slug])) {
            $cat_logo = $logos['category_logos'][$category_slug];
            if (!empty($cat_logo['id'])) {
                $url = wp_get_attachment_image_url($cat_logo['id'], 'dpdt-category-logo');
                return $url ? $url : $cat_logo['url'];
            }
            return isset($cat_logo['url']) ? $cat_logo['url'] : '';
        }
        return '';
    }

    /**
     * Save logos
     */
    public function save_logos($data) {
        $logos = $this->get_logos();

        if (isset($data['header_logo'])) {
            $logos['header_logo'] = esc_url_raw($data['header_logo']);
        }
        if (isset($data['header_logo_id'])) {
            $logos['header_logo_id'] = intval($data['header_logo_id']);
        }
        if (isset($data['header_logo_alt'])) {
            $logos['header_logo_alt'] = sanitize_text_field($data['header_logo_alt']);
        }
        if (isset($data['favicon'])) {
            $logos['favicon'] = esc_url_raw($data['favicon']);
        }
        if (isset($data['favicon_id'])) {
            $logos['favicon_id'] = intval($data['favicon_id']);
        }
        if (isset($data['footer_logo'])) {
            $logos['footer_logo'] = esc_url_raw($data['footer_logo']);
        }
        if (isset($data['footer_logo_id'])) {
            $logos['footer_logo_id'] = intval($data['footer_logo_id']);
        }
        if (isset($data['category_logos'])) {
            $logos['category_logos'] = $data['category_logos'];
        }

        return update_option($this->option_key, $logos);
    }

    /**
     * Handle logo upload via AJAX
     */
    public function handle_upload() {
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => 'Unauthorized'));
        }

        check_ajax_referer(DPDT_NONCE_ACTION, 'nonce');

        $type = isset($_POST['logo_type']) ? sanitize_text_field($_POST['logo_type']) : '';
        $category_slug = isset($_POST['category_slug']) ? sanitize_text_field($_POST['category_slug']) : '';

        if (empty($_FILES['logo_file'])) {
            wp_send_json_error(array('message' => __('কোনো ফাইল নির্বাচন করা হয়নি।', 'dpdt-trademark')));
        }

        require_once(ABSPATH . 'wp-admin/includes/image.php');
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/media.php');

        $file = $_FILES['logo_file'];
        $allowed_types = array('image/jpeg', 'image/png', 'image/gif', 'image/svg+xml', 'image/x-icon');

        if (!in_array($file['type'], $allowed_types)) {
            wp_send_json_error(array('message' => __('অনুমোদিত ফাইল টাইপ: JPG, PNG, GIF, SVG, ICO', 'dpdt-trademark')));
        }

        $upload_dir = wp_upload_dir();
        $target_dir = $upload_dir['basedir'] . '/dpdt-logos/';

        if (!file_exists($target_dir)) {
            wp_mkdir_p($target_dir);
        }

        $filename = sanitize_file_name($file['name']);
        $filename = wp_unique_filename($target_dir, $filename);
        $target_path = $target_dir . $filename;

        if (move_uploaded_file($file['tmp_name'], $target_path)) {
            $attachment = array(
                'post_mime_type' => $file['type'],
                'post_title' => pathinfo($filename, PATHINFO_FILENAME),
                'post_content' => '',
                'post_status' => 'inherit',
            );

            $attach_id = wp_insert_attachment($attachment, $target_path);
            if (!is_wp_error($attach_id)) {
                $attach_data = wp_generate_attachment_metadata($attach_id, $target_path);
                wp_update_attachment_metadata($attach_id, $attach_data);

                $url = $upload_dir['baseurl'] . '/dpdt-logos/' . $filename;

                // Save to options
                $logos = $this->get_logos();
                switch ($type) {
                    case 'header':
                        $logos['header_logo'] = $url;
                        $logos['header_logo_id'] = $attach_id;
                        break;
                    case 'favicon':
                        $logos['favicon'] = $url;
                        $logos['favicon_id'] = $attach_id;
                        break;
                    case 'footer':
                        $logos['footer_logo'] = $url;
                        $logos['footer_logo_id'] = $attach_id;
                        break;
                    case 'category':
                        if (!empty($category_slug)) {
                            $logos['category_logos'][$category_slug] = array(
                                'url' => $url,
                                'id' => $attach_id,
                            );
                        }
                        break;
                }
                update_option($this->option_key, $logos);

                wp_send_json_success(array(
                    'message' => __('লোগো আপলোড সফল!', 'dpdt-trademark'),
                    'url' => $url,
                    'id' => $attach_id,
                ));
            }
        }

        wp_send_json_error(array('message' => __('আপলোড ব্যর্থ হয়েছে।', 'dpdt-trademark')));
    }

    /**
     * Handle logo delete via AJAX
     */
    public function handle_delete() {
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => 'Unauthorized'));
        }

        check_ajax_referer(DPDT_NONCE_ACTION, 'nonce');

        $type = isset($_POST['logo_type']) ? sanitize_text_field($_POST['logo_type']) : '';
        $category_slug = isset($_POST['category_slug']) ? sanitize_text_field($_POST['category_slug']) : '';

        $logos = $this->get_logos();

        switch ($type) {
            case 'header':
                if ($logos['header_logo_id']) wp_delete_attachment($logos['header_logo_id'], true);
                $logos['header_logo'] = '';
                $logos['header_logo_id'] = 0;
                break;
            case 'favicon':
                if ($logos['favicon_id']) wp_delete_attachment($logos['favicon_id'], true);
                $logos['favicon'] = '';
                $logos['favicon_id'] = 0;
                break;
            case 'footer':
                if ($logos['footer_logo_id']) wp_delete_attachment($logos['footer_logo_id'], true);
                $logos['footer_logo'] = '';
                $logos['footer_logo_id'] = 0;
                break;
            case 'category':
                if (!empty($category_slug) && isset($logos['category_logos'][$category_slug])) {
                    if ($logos['category_logos'][$category_slug]['id']) {
                        wp_delete_attachment($logos['category_logos'][$category_slug]['id'], true);
                    }
                    unset($logos['category_logos'][$category_slug]);
                }
                break;
        }

        update_option($this->option_key, $logos);
        wp_send_json_success(array('message' => __('লোগো মুছে ফেলা হয়েছে।', 'dpdt-trademark')));
    }
}
