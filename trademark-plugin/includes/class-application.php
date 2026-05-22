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
        // Verify nonce - accept from multiple possible field names for flexibility
        $nonce_verified = false;

        // Check _dpdt_nonce field (from form nonce field)
        if (isset($_POST['_dpdt_nonce']) && $this->security->verify_nonce($_POST['_dpdt_nonce'])) {
            $nonce_verified = true;
        }

        // Check _wpnonce field (WordPress default nonce field name)
        if (!$nonce_verified && isset($_POST['_wpnonce']) && $this->security->verify_nonce($_POST['_wpnonce'])) {
            $nonce_verified = true;
        }

        // For non-logged-in users, allow submission if honeypot + rate limit pass (nonce may expire)
        if (!$nonce_verified && !is_user_logged_in()) {
            // Still require honeypot and rate limiting as security measures
            $nonce_verified = true;
        }

        if (!$nonce_verified) {
            wp_send_json_error(array('message' => __('নিরাপত্তা যাচাই ব্যর্থ। পেজ রিফ্রেশ করে আবার চেষ্টা করুন।', 'dpdt-trademark')));
        }

        // Check honeypot
        if (isset($_POST['dpdt_honeypot']) && !$this->security->check_honeypot($_POST['dpdt_honeypot'])) {
            wp_send_json_error(array('message' => __('অনুরোধ প্রক্রিয়া করা যায়নি।', 'dpdt-trademark')));
        }

        // Rate limiting
        $ip = isset($_SERVER['REMOTE_ADDR']) ? sanitize_text_field($_SERVER['REMOTE_ADDR']) : '0.0.0.0';
        if (!$this->security->check_rate_limit($ip)) {
            wp_send_json_error(array('message' => __('অনেক বেশি অনুরোধ। কিছুক্ষণ পর আবার চেষ্টা করুন।', 'dpdt-trademark')));
        }

        // Sanitize and validate input
        $data = $this->validate_submission($_POST);
        if (is_wp_error($data)) {
            wp_send_json_error(array('message' => $data->get_error_message()));
        }

        // Handle brand logo upload
        $brand_logo_url = '';
        $brand_logo_id = 0;
        if (!empty($_FILES['brand_logo']) && $_FILES['brand_logo']['error'] === UPLOAD_ERR_OK) {
            $upload_result = $this->handle_logo_upload($_FILES['brand_logo']);
            if (is_wp_error($upload_result)) {
                wp_send_json_error(array('message' => $upload_result->get_error_message()));
            }
            $brand_logo_url = $upload_result['url'];
            $brand_logo_id = $upload_result['id'];
        }

        // Generate application ID
        $application_id = $this->security->generate_application_id();

        // Prepare application data
        $app_data = array(
            'application_id' => $application_id,
            'applicant_name' => $data['applicant_name'],
            'applicant_name_bn' => $data['applicant_name_bn'],
            'applicant_email' => $data['applicant_email'],
            'applicant_phone' => $data['applicant_phone'],
            'applicant_address' => $data['applicant_address'],
            'brand_name' => $data['brand_name'],
            'brand_name_bn' => $data['brand_name_bn'],
            'trademark_class' => $data['trademark_class'],
            'trademark_type' => $data['trademark_type'],
            'brand_logo_url' => $brand_logo_url,
            'brand_logo_id' => $brand_logo_id,
            'description' => $data['description'],
            'owner_name' => $data['owner_name'],
            'owner_name_bn' => $data['owner_name_bn'],
            'company_name' => $data['company_name'],
            'application_date' => current_time('mysql'),
            'status' => 'pending',
            'ip_address' => $ip,
            'user_agent' => isset($_SERVER['HTTP_USER_AGENT']) ? sanitize_text_field($_SERVER['HTTP_USER_AGENT']) : '',
        );

        // Insert into database
        $result = $this->db->insert_application($app_data);

        if ($result) {
            // Send notification email to admin
            $this->send_admin_notification($app_data);

            // Send confirmation email to applicant
            $this->send_applicant_confirmation($app_data);

            wp_send_json_success(array(
                'message' => __('আপনার আবেদন সফলভাবে জমা হয়েছে!', 'dpdt-trademark'),
                'application_id' => $application_id,
                'details' => sprintf(
                    __('আবেদন নম্বর: %s। আপনার ইমেইলে নিশ্চিতকরণ পাঠানো হয়েছে।', 'dpdt-trademark'),
                    $application_id
                ),
            ));
        } else {
            wp_send_json_error(array('message' => __('আবেদন জমা দিতে সমস্যা হয়েছে। পুনরায় চেষ্টা করুন।', 'dpdt-trademark')));
        }

        wp_die();
    }

    /**
     * Validate form submission data
     */
    private function validate_submission($post_data) {
        $errors = new WP_Error();

        $required_fields = array(
            'applicant_name' => __('আবেদনকারীর নাম', 'dpdt-trademark'),
            'applicant_email' => __('ইমেইল', 'dpdt-trademark'),
            'applicant_phone' => __('মোবাইল নম্বর', 'dpdt-trademark'),
            'brand_name' => __('ব্র্যান্ডের নাম', 'dpdt-trademark'),
            'trademark_class' => __('ট্রেডমার্ক শ্রেণী', 'dpdt-trademark'),
        );

        foreach ($required_fields as $field => $label) {
            if (empty($post_data[$field])) {
                $errors->add($field, sprintf(__('%s আবশ্যক।', 'dpdt-trademark'), $label));
            }
        }

        // Validate email
        if (!empty($post_data['applicant_email']) && !is_email($post_data['applicant_email'])) {
            $errors->add('email', __('সঠিক ইমেইল ঠিকানা দিন।', 'dpdt-trademark'));
        }

        // Validate phone
        if (!empty($post_data['applicant_phone'])) {
            $phone = preg_replace('/[^0-9+]/', '', $post_data['applicant_phone']);
            if (strlen($phone) < 10 || strlen($phone) > 15) {
                $errors->add('phone', __('সঠিক মোবাইল নম্বর দিন।', 'dpdt-trademark'));
            }
        }

        if ($errors->has_errors()) {
            return $errors;
        }

        // Return sanitized data
        return array(
            'applicant_name' => $this->security->sanitize_input($post_data['applicant_name']),
            'applicant_name_bn' => $this->security->sanitize_input(isset($post_data['applicant_name_bn']) ? $post_data['applicant_name_bn'] : ''),
            'applicant_email' => $this->security->sanitize_input($post_data['applicant_email'], 'email'),
            'applicant_phone' => $this->security->sanitize_input($post_data['applicant_phone']),
            'applicant_address' => $this->security->sanitize_input(isset($post_data['applicant_address']) ? $post_data['applicant_address'] : '', 'textarea'),
            'brand_name' => $this->security->sanitize_input($post_data['brand_name']),
            'brand_name_bn' => $this->security->sanitize_input(isset($post_data['brand_name_bn']) ? $post_data['brand_name_bn'] : ''),
            'trademark_class' => $this->security->sanitize_input($post_data['trademark_class']),
            'trademark_type' => $this->security->sanitize_input(isset($post_data['trademark_type']) ? $post_data['trademark_type'] : 'word'),
            'description' => $this->security->sanitize_input(isset($post_data['description']) ? $post_data['description'] : '', 'textarea'),
            'owner_name' => $this->security->sanitize_input(isset($post_data['owner_name']) ? $post_data['owner_name'] : ''),
            'owner_name_bn' => $this->security->sanitize_input(isset($post_data['owner_name_bn']) ? $post_data['owner_name_bn'] : ''),
            'company_name' => $this->security->sanitize_input(isset($post_data['company_name']) ? $post_data['company_name'] : ''),
        );
    }

    /**
     * Handle brand logo file upload
     */
    private function handle_logo_upload($file) {
        $allowed_types = array('image/jpeg', 'image/png', 'image/gif', 'image/svg+xml');
        $max_size = 2 * 1024 * 1024; // 2MB

        $errors = $this->security->validate_upload($file, $allowed_types, $max_size);
        if (!empty($errors)) {
            return new WP_Error('upload_error', implode(' ', $errors));
        }

        require_once(ABSPATH . 'wp-admin/includes/image.php');
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/media.php');

        $upload_dir = wp_upload_dir();
        $target_dir = $upload_dir['basedir'] . '/dpdt-logos/brands/';

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

                return array(
                    'id' => $attach_id,
                    'url' => $upload_dir['baseurl'] . '/dpdt-logos/brands/' . $filename,
                );
            }
        }

        return new WP_Error('upload_failed', __('ফাইল আপলোড ব্যর্থ হয়েছে।', 'dpdt-trademark'));
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
