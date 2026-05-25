<?php
if (!defined('ABSPATH')) exit;

/**
 * Application Handler Class
 * Manages trademark certificate applications
 */
class DPDT_Application {

    private $db;
    private $security;

    public function __construct() {
        $this->db = new DPDT_Database();
        $this->security = new DPDT_Security();
    }

    /**
     * Render application form shortcode
     */
    public function render_form($atts = array()) {
        $atts = shortcode_atts(array(
            'title' => __('ট্রেডমার্ক সার্টিফিকেট আবেদন', 'dpdt-trademark'),
            'show_title' => 'yes',
        ), $atts);

        ob_start();
        include DPDT_PLUGIN_DIR . 'templates/shortcodes/shortcode-apply-form.php';
        return ob_get_clean();
    }

    /**
     * Handle AJAX form submission
     */
    public function handle_submission() {
        global $wpdb;
        $table = $wpdb->prefix . 'dpdt_applications';

        // Make sure table exists
        $table_exists = $wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $table));
        if ($table_exists !== $table) {
            // Try to create it
            if (class_exists('DPDT_Database')) {
                $db = new DPDT_Database();
                $db->create_tables();
            }
            // Check again
            $table_exists = $wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $table));
            if ($table_exists !== $table) {
                wp_send_json_error(array('message' => 'ডাটাবেস টেবিল তৈরি করা যায়নি। প্লাগিন Deactivate করে আবার Activate করুন।'));
                wp_die();
            }
        }

        // Get form data
        $applicant_name = isset($_POST['applicant_name']) ? sanitize_text_field($_POST['applicant_name']) : '';
        $applicant_email = isset($_POST['applicant_email']) ? sanitize_email($_POST['applicant_email']) : '';
        $applicant_phone = isset($_POST['applicant_phone']) ? sanitize_text_field($_POST['applicant_phone']) : '';
        $brand_name = isset($_POST['brand_name']) ? sanitize_text_field($_POST['brand_name']) : '';
        $trademark_class = isset($_POST['trademark_class']) ? sanitize_text_field($_POST['trademark_class']) : '';

        // Validate required
        if (empty($applicant_name) || empty($applicant_email) || empty($applicant_phone) || empty($brand_name) || empty($trademark_class)) {
            wp_send_json_error(array('message' => 'সকল প্রয়োজনীয় (*) ঘর পূরণ করুন।'));
            wp_die();
        }

        // Generate unique ID
        $application_id = 'DPDT-' . date('Y') . '-' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 8));

        // Get table columns to only insert what exists
        $columns = $wpdb->get_col("SHOW COLUMNS FROM {$table}", 0);

        // Build insert data based on what columns actually exist
        $insert_data = array();
        $format = array();

        $field_map = array(
            'application_id' => array($application_id, '%s'),
            'app_code' => array($application_id, '%s'),
            'applicant_name' => array($applicant_name, '%s'),
            'applicant_name_bn' => array(sanitize_text_field($_POST['applicant_name_bn'] ?? ''), '%s'),
            'applicant_email' => array($applicant_email, '%s'),
            'applicant_phone' => array($applicant_phone, '%s'),
            'applicant_address' => array(sanitize_textarea_field($_POST['applicant_address'] ?? ''), '%s'),
            'brand_name' => array($brand_name, '%s'),
            'brand_name_bn' => array(sanitize_text_field($_POST['brand_name_bn'] ?? ''), '%s'),
            'trademark_class' => array($trademark_class, '%s'),
            'trademark_type' => array(sanitize_text_field($_POST['trademark_type'] ?? 'word'), '%s'),
            'description' => array(sanitize_textarea_field($_POST['description'] ?? ''), '%s'),
            'owner_name' => array(sanitize_text_field($_POST['owner_name'] ?? ''), '%s'),
            'owner_name_bn' => array(sanitize_text_field($_POST['owner_name_bn'] ?? ''), '%s'),
            'company_name' => array(sanitize_text_field($_POST['company_name'] ?? ''), '%s'),
            'application_date' => array(current_time('mysql'), '%s'),
            'status' => array('pending', '%s'),
            'ip_address' => array(isset($_SERVER['REMOTE_ADDR']) ? sanitize_text_field($_SERVER['REMOTE_ADDR']) : '', '%s'),
        );

        foreach ($field_map as $col => $val) {
            if (in_array($col, $columns)) {
                $insert_data[$col] = $val[0];
                $format[] = $val[1];
            }
        }

        // Handle logo upload
        if (!empty($_FILES['brand_logo']) && $_FILES['brand_logo']['error'] === UPLOAD_ERR_OK && in_array('brand_logo_url', $columns)) {
            require_once(ABSPATH . 'wp-admin/includes/file.php');
            $upload = wp_handle_upload($_FILES['brand_logo'], array('test_form' => false));
            if (!isset($upload['error']) && isset($upload['url'])) {
                $insert_data['brand_logo_url'] = $upload['url'];
                $format[] = '%s';
            }
        }

        // Do the insert
        $result = $wpdb->insert($table, $insert_data, $format);

        if ($result !== false) {
            // Send notification email to admin
            $this->send_admin_notification(array(
                'application_id' => $application_id,
                'applicant_name' => $applicant_name,
                'brand_name' => $brand_name,
                'applicant_email' => $applicant_email,
                'applicant_phone' => $applicant_phone,
            ));

            // Send confirmation email to applicant
            $this->send_applicant_confirmation(array(
                'application_id' => $application_id,
                'applicant_name' => $applicant_name,
                'brand_name' => $brand_name,
                'applicant_email' => $applicant_email,
            ));

            wp_send_json_success(array(
                'message' => 'আপনার আবেদন সফলভাবে জমা হয়েছে!',
                'application_id' => $application_id,
                'details' => 'আবেদন নম্বর: ' . $application_id . '। আপনার ইমেইলে নিশ্চিতকরণ পাঠানো হবে।',
            ));
        } else {
            // Log the error for debugging
            $error = $wpdb->last_error;
            error_log('DPDT Application Insert Failed: ' . $error);
            error_log('DPDT Insert Data: ' . print_r($insert_data, true));
            wp_send_json_error(array('message' => 'আবেদন জমা দিতে সমস্যা হয়েছে। Error: ' . $error));
        }
        wp_die();
    }

    /**
     * Send notification to admin
     */
    private function send_admin_notification($app_data) {
        $admin_email = get_option('admin_email');
        $site_name = get_option('dpdt_site_name', get_bloginfo('name'));

        $subject = sprintf('[%s] নতুন ট্রেডমার্ক আবেদন: %s', $site_name, $app_data['application_id']);

        $message = sprintf(
            "নতুন ট্রেডমার্ক সার্টিফিকেট আবেদন পাওয়া গেছে।\n\n" .
            "আবেদন নম্বর: %s\n" .
            "আবেদনকারী: %s\n" .
            "ব্র্যান্ড: %s\n" .
            "ইমেইল: %s\n" .
            "ফোন: %s\n" .
            "তারিখ: %s\n\n" .
            "ড্যাশবোর্ডে দেখুন: %s",
            $app_data['application_id'],
            $app_data['applicant_name'],
            $app_data['brand_name'],
            $app_data['applicant_email'],
            $app_data['applicant_phone'],
            current_time('d/m/Y h:i A'),
            admin_url('admin.php?page=dpdt-applications')
        );

        wp_mail($admin_email, $subject, $message);
    }

    /**
     * Send confirmation to applicant
     */
    private function send_applicant_confirmation($app_data) {
        $site_name = get_option('dpdt_site_name', get_bloginfo('name'));

        $subject = sprintf('[%s] আবেদন নিশ্চিতকরণ - %s', $site_name, $app_data['application_id']);

        $message = sprintf(
            "প্রিয় %s,\n\n" .
            "আপনার ট্রেডমার্ক সার্টিফিকেট আবেদন সফলভাবে গ্রহণ করা হয়েছে।\n\n" .
            "আবেদন নম্বর: %s\n" .
            "ব্র্যান্ড নাম: %s\n" .
            "তারিখ: %s\n\n" .
            "আপনার আবেদন পর্যালোচনা করা হচ্ছে। অনুমোদন হলে আপনাকে জানানো হবে।\n\n" .
            "ধন্যবাদ,\n%s",
            $app_data['applicant_name'],
            $app_data['application_id'],
            $app_data['brand_name'],
            current_time('d/m/Y'),
            $site_name
        );

        wp_mail($app_data['applicant_email'], $subject, $message);
    }

    /**
     * Get trademark classes (Nice Classification 1-45, dpdt.gov.bd style)
     */
    public static function get_trademark_classes() {
        return array(
            '1'  => 'শ্রেণী ০১ - রাসায়নিক দ্রব্য (Chemicals used in industry, science, photography, agriculture, horticulture and forestry)',
            '2'  => 'শ্রেণী ০২ - রং, বার্নিশ, লেকার (Paints, varnishes, lacquers; preservatives against rust and against deterioration of wood)',
            '3'  => 'শ্রেণী ০৩ - প্রসাধনী ও পরিষ্কারক দ্রব্য (Bleaching preparations, cleaning, polishing; soaps; perfumery, cosmetics)',
            '4'  => 'শ্রেণী ০৪ - শিল্প তেল ও জ্বালানি (Industrial oils and greases; lubricants; fuels and illuminants; candles and wicks)',
            '5'  => 'শ্রেণী ০৫ - ঔষধ ও ফার্মাসিউটিক্যাল (Pharmaceutical and veterinary preparations; sanitary preparations for medical purposes; dietetic food)',
            '6'  => 'শ্রেণী ০৬ - সাধারণ ধাতু ও তার সামগ্রী (Common metals and their alloys; metal building materials; transportable buildings of metal)',
            '7'  => 'শ্রেণী ০৭ - যন্ত্রপাতি ও মেশিন (Machines and machine tools; motors and engines; agriculture implements)',
            '8'  => 'শ্রেণী ০৮ - হাত সরঞ্জাম ও যন্ত্র (Hand tools and implements; cutlery; side arms; razors)',
            '9'  => 'শ্রেণী ০৯ - বৈজ্ঞানিক ও ইলেকট্রনিক যন্ত্রপাতি (Scientific, nautical, electric apparatus; computers; software; mobile phones)',
            '10' => 'শ্রেণী ১০ - চিকিৎসা ও শল্যচিকিৎসা যন্ত্রপাতি (Surgical, medical, dental and veterinary apparatus and instruments)',
            '11' => 'শ্রেণী ১১ - আলো, তাপ ও স্যানিটারি যন্ত্র (Apparatus for lighting, heating, steam generating, cooking, refrigerating, ventilating)',
            '12' => 'শ্রেণী ১২ - যানবাহন (Vehicles; apparatus for locomotion by land, air or water)',
            '13' => 'শ্রেণী ১৩ - আগ্নেয়াস্ত্র ও বিস্ফোরক (Firearms; ammunition and projectiles; explosives; fireworks)',
            '14' => 'শ্রেণী ১৪ - মূল্যবান ধাতু ও গহনা (Precious metals and their alloys; jewellery, precious stones; horological instruments)',
            '15' => 'শ্রেণী ১৫ - বাদ্যযন্ত্র (Musical instruments)',
            '16' => 'শ্রেণী ১৬ - কাগজ, মুদ্রণ ও অফিস সামগ্রী (Paper, cardboard; printed matter; bookbinding material; photographs; stationery)',
            '17' => 'শ্রেণী ১৭ - রাবার, গাটা-পার্চা ও প্লাস্টিক (Rubber, gutta-percha, gum; asbestos, mica; plastics in extruded form; packing materials)',
            '18' => 'শ্রেণী ১৮ - চামড়া ও চামড়াজাত পণ্য (Leather and imitations of leather; animal skins; trunks; bags; umbrellas)',
            '19' => 'শ্রেণী ১৯ - অধাতব নির্মাণ সামগ্রী (Building materials non-metallic; non-metallic rigid pipes; asphalt, pitch; monuments)',
            '20' => 'শ্রেণী ২০ - আসবাবপত্র ও গৃহসজ্জা (Furniture, mirrors, picture frames; goods of wood, cork, reed, cane, wicker)',
            '21' => 'শ্রেণী ২১ - গৃহস্থালী পাত্র ও বাসনকোসন (Household or kitchen utensils; combs and sponges; brushes; glassware, porcelain)',
            '22' => 'শ্রেণী ২২ - দড়ি, তাঁবু ও টেক্সটাইল কাঁচামাল (Ropes, string, nets, tents, awnings, tarpaulins, sails; padding and stuffing materials; raw fibrous textile materials)',
            '23' => 'শ্রেণী ২৩ - সুতা ও তন্তু (Yarns and threads for textile use)',
            '24' => 'শ্রেণী ২৪ - কাপড় ও বস্ত্র (Textiles and textile goods; bed covers; table covers)',
            '25' => 'শ্রেণী ২৫ - পোশাক, জুতা ও মাথার আচ্ছাদন (Clothing, footwear, headgear)',
            '26' => 'শ্রেণী ২৬ - সূচিকর্ম, ফিতা ও বোতাম (Lace and embroidery, ribbons and braid; buttons, hooks and eyes; artificial flowers)',
            '27' => 'শ্রেণী ২৭ - মেঝে ও দেয়াল আচ্ছাদন (Carpets, rugs, mats and matting; linoleum; wall hangings non-textile)',
            '28' => 'শ্রেণী ২৮ - খেলনা, খেলাধুলা ও ব্যায়াম সামগ্রী (Games and playthings; gymnastic and sporting articles; Christmas tree decorations)',
            '29' => 'শ্রেণী ২৯ - মাংস, মাছ, দুগ্ধ ও খাদ্য তেল (Meat, fish, poultry; preserved, frozen, dried fruits and vegetables; jellies; eggs; milk; edible oils)',
            '30' => 'শ্রেণী ৩০ - চা, কফি, মসলা ও শস্যজাত খাদ্য (Coffee, tea, cocoa, sugar, rice, flour; bread, pastry; spices; ice; honey; yeast)',
            '31' => 'শ্রেণী ৩১ - কৃষিজ পণ্য ও বীজ (Agricultural, horticultural products; fresh fruits and vegetables; seeds; live animals; foodstuffs for animals)',
            '32' => 'শ্রেণী ৩২ - অ্যালকোহলমুক্ত পানীয় (Beers; mineral waters; fruit beverages; syrups for making beverages)',
            '33' => 'শ্রেণী ৩৩ - অ্যালকোহলযুক্ত পানীয় (Alcoholic beverages except beers)',
            '34' => 'শ্রেণী ৩৪ - তামাক ও ধূমপান সামগ্রী (Tobacco; smokers articles; matches)',
            '35' => 'শ্রেণী ৩৫ - বিজ্ঞাপন ও ব্যবসা পরিচালনা (Advertising; business management; business administration; office functions)',
            '36' => 'শ্রেণী ৩৬ - বীমা ও আর্থিক সেবা (Insurance; financial affairs; monetary affairs; real estate affairs)',
            '37' => 'শ্রেণী ৩৭ - নির্মাণ ও মেরামত সেবা (Building construction; repair; installation services)',
            '38' => 'শ্রেণী ৩৮ - টেলিযোগাযোগ সেবা (Telecommunications)',
            '39' => 'শ্রেণী ৩৯ - পরিবহন ও সংরক্ষণ সেবা (Transport; packaging and storage of goods; travel arrangement)',
            '40' => 'শ্রেণী ৪০ - উপকরণ প্রক্রিয়াকরণ সেবা (Treatment of materials)',
            '41' => 'শ্রেণী ৪১ - শিক্ষা, প্রশিক্ষণ ও বিনোদন সেবা (Education; providing of training; entertainment; sporting and cultural activities)',
            '42' => 'শ্রেণী ৪২ - বৈজ্ঞানিক ও প্রযুক্তি সেবা (Scientific and technological services; industrial analysis; computer hardware and software design)',
            '43' => 'শ্রেণী ৪৩ - খাদ্য ও আবাসন সেবা (Services for providing food and drink; temporary accommodation)',
            '44' => 'শ্রেণী ৪৪ - চিকিৎসা ও কৃষি সেবা (Medical services; veterinary services; hygienic and beauty care; agriculture, horticulture, forestry services)',
            '45' => 'শ্রেণী ৪৫ - আইনি ও নিরাপত্তা সেবা (Legal services; security services; personal and social services)',
        );
    }
}
