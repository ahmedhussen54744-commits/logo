<?php
if (!defined('ABSPATH')) exit;

/**
 * Category Manager Class
 * Creates and manages WordPress pages for site categories
 * Auto-creates all 10 category pages on plugin activation
 */
class DPDT_Category_Manager {

    private $categories = array();

    public function __construct() {
        $this->categories = $this->get_default_categories();
    }

    /**
     * Get default category pages
     */
    private function get_default_categories() {
        return array(
            array(
                'title' => 'আমাদের সম্পর্কে',
                'slug' => 'about-us',
                'content' => 'পেটেন্ট, ডিজাইন ও ট্রেডমার্কস অধিদপ্তর (DPDT) বাংলাদেশ সরকারের শিল্প মন্ত্রণালয়ের অধীন একটি অধিদপ্তর। এই অধিদপ্তর পেটেন্ট, শিল্পনকশা ও ট্রেডমার্কস নিবন্ধন ও সুরক্ষা প্রদান করে।',
                'icon' => 'dashicons-info',
                'order' => 1,
                'children' => array(
                    array('title' => 'মিশন ও ভিশন', 'slug' => 'mission-vision'),
                    array('title' => 'ইতিহাস', 'slug' => 'history'),
                    array('title' => 'সাংগঠনিক কাঠামো', 'slug' => 'organizational-structure'),
                    array('title' => 'কর্মকর্তাবৃন্দ', 'slug' => 'officers'),
                ),
            ),
            array(
                'title' => 'সেবাসমূহ',
                'slug' => 'services',
                'content' => 'আমরা ট্রেডমার্ক, পেটেন্ট ও ডিজাইন নিবন্ধনসহ বিভিন্ন সেবা প্রদান করে থাকি।',
                'icon' => 'dashicons-admin-tools',
                'order' => 2,
                'children' => array(
                    array('title' => 'ট্রেডমার্ক নিবন্ধন', 'slug' => 'trademark-registration'),
                    array('title' => 'পেটেন্ট নিবন্ধন', 'slug' => 'patent-registration'),
                    array('title' => 'ডিজাইন নিবন্ধন', 'slug' => 'design-registration'),
                    array('title' => 'সার্টিফিকেট যাচাই', 'slug' => 'certificate-verify'),
                ),
            ),
            array(
                'title' => 'আইন ও বিধি',
                'slug' => 'laws-rules',
                'content' => 'ট্রেডমার্ক, পেটেন্ট ও ডিজাইন সম্পর্কিত প্রচলিত আইন ও বিধিমালা।',
                'icon' => 'dashicons-book',
                'order' => 3,
                'children' => array(
                    array('title' => 'ট্রেডমার্ক আইন ২০০৯', 'slug' => 'trademark-act-2009'),
                    array('title' => 'পেটেন্ট ও ডিজাইন আইন', 'slug' => 'patent-design-act'),
                    array('title' => 'বিধিমালা', 'slug' => 'regulations'),
                ),
            ),
            array(
                'title' => 'প্রকাশনা',
                'slug' => 'publications',
                'content' => 'অধিদপ্তর কর্তৃক প্রকাশিত বিভিন্ন প্রকাশনা ও জার্নাল।',
                'icon' => 'dashicons-media-document',
                'order' => 4,
                'children' => array(
                    array('title' => 'ট্রেডমার্ক জার্নাল', 'slug' => 'trademark-journal'),
                    array('title' => 'বার্ষিক প্রতিবেদন', 'slug' => 'annual-report'),
                ),
            ),
            array(
                'title' => 'ফরম ও ডাউনলোড',
                'slug' => 'forms-download',
                'content' => 'প্রয়োজনীয় আবেদন ফরম ও অন্যান্য ডাউনলোডযোগ্য নথি।',
                'icon' => 'dashicons-download',
                'order' => 5,
                'children' => array(
                    array('title' => 'ট্রেডমার্ক ফরম', 'slug' => 'trademark-forms'),
                    array('title' => 'পেটেন্ট ফরম', 'slug' => 'patent-forms'),
                    array('title' => 'ডিজাইন ফরম', 'slug' => 'design-forms'),
                ),
            ),
            array(
                'title' => 'তথ্য ভান্ডার',
                'slug' => 'database',
                'content' => 'ট্রেডমার্ক, পেটেন্ট ও ডিজাইন তথ্য ভান্ডার অনুসন্ধান।',
                'icon' => 'dashicons-database',
                'order' => 6,
                'children' => array(
                    array('title' => 'ট্রেডমার্ক অনুসন্ধান', 'slug' => 'trademark-search'),
                    array('title' => 'পেটেন্ট অনুসন্ধান', 'slug' => 'patent-search'),
                ),
            ),
            array(
                'title' => 'ই-সেবা',
                'slug' => 'e-services',
                'content' => 'অনলাইনে আবেদন ও সেবা গ্রহণ করুন।',
                'icon' => 'dashicons-laptop',
                'order' => 7,
                'children' => array(
                    array('title' => 'অনলাইন আবেদন', 'slug' => 'online-application'),
                    array('title' => 'ফি পরিশোধ', 'slug' => 'fee-payment'),
                    array('title' => 'স্ট্যাটাস চেক', 'slug' => 'status-check'),
                ),
            ),
            array(
                'title' => 'গ্যালারি',
                'slug' => 'gallery',
                'content' => 'ছবি ও ভিডিও গ্যালারি।',
                'icon' => 'dashicons-format-gallery',
                'order' => 8,
                'children' => array(
                    array('title' => 'ফটো গ্যালারি', 'slug' => 'photo-gallery'),
                    array('title' => 'ভিডিও গ্যালারি', 'slug' => 'video-gallery'),
                ),
            ),
            array(
                'title' => 'নোটিশ বোর্ড',
                'slug' => 'notice-board',
                'content' => 'সর্বশেষ নোটিশ ও বিজ্ঞপ্তি।',
                'icon' => 'dashicons-megaphone',
                'order' => 9,
                'children' => array(),
            ),
            array(
                'title' => 'যোগাযোগ',
                'slug' => 'contact',
                'content' => 'পেটেন্ট, ডিজাইন ও ট্রেডমার্কস অধিদপ্তরের সাথে যোগাযোগ করুন।\n\nঠিকানা: ৯১, মতিঝিল বা/এ, ঢাকা-১০০০\nফোন: +৮৮০-২-৯৫৭৩৩৯৮\nইমেইল: info@dpdt.gov.bd',
                'icon' => 'dashicons-phone',
                'order' => 10,
                'children' => array(),
            ),
        );
    }

    /**
     * Create default pages on activation
     */
    public function create_default_pages() {
        foreach ($this->categories as $category) {
            $parent_id = $this->create_page_if_not_exists($category['title'], $category['slug'], $category['content'], 0, $category['order']);

            // Create child pages
            if (!empty($category['children']) && $parent_id) {
                $child_order = 1;
                foreach ($category['children'] as $child) {
                    $content = isset($child['content']) ? $child['content'] : '';
                    $this->create_page_if_not_exists($child['title'], $child['slug'], $content, $parent_id, $child_order);
                    $child_order++;
                }
            }
        }

        // Create Apply page
        $this->create_page_if_not_exists(
            'আবেদন করুন',
            'apply',
            '[dpdt_apply_form]',
            0,
            11
        );

        // Create Verify page
        $this->create_page_if_not_exists(
            'সার্টিফিকেট যাচাই',
            'verify',
            '[dpdt_verify]',
            0,
            12
        );

        // Store page IDs
        $this->store_page_ids();
    }

    /**
     * Create a page if it doesn't already exist
     */
    private function create_page_if_not_exists($title, $slug, $content, $parent_id = 0, $order = 0) {
        $existing = get_page_by_path($slug);
        if ($existing) {
            return $existing->ID;
        }

        $page_data = array(
            'post_title' => $title,
            'post_name' => $slug,
            'post_content' => $content,
            'post_status' => 'publish',
            'post_type' => 'page',
            'post_parent' => $parent_id,
            'menu_order' => $order,
            'comment_status' => 'closed',
        );

        $page_id = wp_insert_post($page_data);
        return $page_id;
    }

    /**
     * Store page IDs for reference
     */
    private function store_page_ids() {
        $page_ids = array();
        foreach ($this->categories as $category) {
            $page = get_page_by_path($category['slug']);
            if ($page) {
                $page_ids[$category['slug']] = $page->ID;
            }
        }
        update_option('dpdt_category_page_ids', $page_ids);
    }

    /**
     * Get all category pages
     */
    public function get_category_pages() {
        $pages = array();
        foreach ($this->categories as $category) {
            $page = get_page_by_path($category['slug']);
            if ($page) {
                $category['page_id'] = $page->ID;
                $category['url'] = get_permalink($page->ID);
                $pages[] = $category;
            }
        }
        return $pages;
    }

    /**
     * Render admin page for category management
     */
    public function render_admin_page() {
        // Handle form submission
        if (isset($_POST['dpdt_category_action']) && isset($_POST['_dpdt_nonce'])) {
            if (wp_verify_nonce($_POST['_dpdt_nonce'], DPDT_NONCE_ACTION)) {
                $this->handle_admin_action();
            }
        }

        include DPDT_PLUGIN_DIR . 'admin/views/category-settings.php';
    }

    /**
     * Handle admin category actions
     */
    private function handle_admin_action() {
        $action = sanitize_text_field($_POST['dpdt_category_action']);

        switch ($action) {
            case 'recreate_pages':
                $this->create_default_pages();
                add_settings_error('dpdt_categories', 'pages_created', __('সকল পেজ পুনরায় তৈরি করা হয়েছে।', 'dpdt-trademark'), 'success');
                break;

            case 'recreate_menu':
                $menu_manager = new DPDT_Menu_Manager();
                $menu_manager->create_default_menu();
                add_settings_error('dpdt_categories', 'menu_created', __('মেনু পুনরায় তৈরি করা হয়েছে।', 'dpdt-trademark'), 'success');
                break;

            case 'add_page':
                $title = sanitize_text_field($_POST['page_title']);
                $slug = sanitize_title($_POST['page_slug']);
                $parent = intval($_POST['page_parent']);
                if (!empty($title) && !empty($slug)) {
                    $this->create_page_if_not_exists($title, $slug, '', $parent);
                    add_settings_error('dpdt_categories', 'page_added', __('নতুন পেজ যোগ করা হয়েছে।', 'dpdt-trademark'), 'success');
                }
                break;
        }
    }

    /**
     * Get categories for display
     */
    public function get_categories_list() {
        return $this->categories;
    }
}
