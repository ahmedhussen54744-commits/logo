<?php
if (!defined('ABSPATH')) exit;

/**
 * Certificate Template Class
 * Manages certificate layout and template rendering
 */
class DPDT_Certificate_Template {

    /**
     * Render certificate preview
     */
    public function render_preview($data) {
        ob_start();
        include DPDT_PLUGIN_DIR . 'certificates/templates/certificate-layout.php';
        return ob_get_clean();
    }

    /**
     * Get certificate HTML for printing
     */
    public function get_print_html($data) {
        $html = '<!DOCTYPE html><html><head>';
        $html .= '<meta charset="UTF-8">';
        $html .= '<title>Certificate - ' . esc_html($data['certificate_number']) . '</title>';
        $html .= '<style>' . $this->get_print_styles() . '</style>';
        $html .= '</head><body>';
        $html .= $this->render_preview($data);
        $html .= '</body></html>';
        return $html;
    }

    /**
     * Get print styles for certificate
     */
    private function get_print_styles() {
        return '
        @page { size: A4 landscape; margin: 0; }
        body { margin: 0; padding: 0; font-family: "SolaimanLipi", "Noto Sans Bengali", sans-serif; }
        .dpdt-certificate-wrapper {
            width: 297mm; height: 210mm;
            padding: 15mm; box-sizing: border-box;
            border: 3px solid #1a5276;
            position: relative;
            background: #fff;
        }
        .dpdt-cert-border {
            border: 2px solid #d4af37;
            padding: 10mm;
            height: calc(100% - 20mm);
            box-sizing: border-box;
        }
        .dpdt-cert-header {
            text-align: center;
            margin-bottom: 8mm;
            border-bottom: 2px solid #1a5276;
            padding-bottom: 5mm;
        }
        .dpdt-cert-header h1 {
            font-size: 24pt; color: #1a5276;
            margin: 0 0 3mm 0;
        }
        .dpdt-cert-header h2 {
            font-size: 14pt; color: #333;
            margin: 0; font-weight: normal;
        }
        .dpdt-cert-body { padding: 5mm 0; }
        .dpdt-cert-body table {
            width: 100%; border-collapse: collapse;
            font-size: 11pt;
        }
        .dpdt-cert-body td {
            padding: 2mm 3mm;
            vertical-align: top;
        }
        .dpdt-cert-body .label { font-weight: bold; width: 35%; color: #1a5276; }
        .dpdt-cert-logo {
            text-align: center; margin: 5mm 0;
        }
        .dpdt-cert-logo img {
            max-width: 60mm; max-height: 40mm;
            border: 1px solid #ddd; padding: 2mm;
        }
        .dpdt-cert-footer {
            position: absolute; bottom: 25mm;
            left: 25mm; right: 25mm;
            display: flex; justify-content: space-between;
            align-items: flex-end;
        }
        .dpdt-cert-qr { text-align: center; }
        .dpdt-cert-qr img { width: 25mm; height: 25mm; }
        .dpdt-cert-seal { text-align: center; }
        .dpdt-cert-number {
            position: absolute; top: 25mm; right: 30mm;
            font-size: 10pt; color: #666;
        }
        ';
    }

    /**
     * Get available certificate templates
     */
    public static function get_templates() {
        return array(
            'default' => array(
                'name' => __('ডিফল্ট টেমপ্লেট', 'dpdt-trademark'),
                'description' => __('সরকারি শৈলীর সার্টিফিকেট', 'dpdt-trademark'),
            ),
            'modern' => array(
                'name' => __('আধুনিক টেমপ্লেট', 'dpdt-trademark'),
                'description' => __('আধুনিক ডিজাইনের সার্টিফিকেট', 'dpdt-trademark'),
            ),
            'minimal' => array(
                'name' => __('সরল টেমপ্লেট', 'dpdt-trademark'),
                'description' => __('সরল ও পরিচ্ছন্ন ডিজাইন', 'dpdt-trademark'),
            ),
        );
    }
}
