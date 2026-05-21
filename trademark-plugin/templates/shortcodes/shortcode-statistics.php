<?php
if (!defined('ABSPATH')) exit;

$db = new DPDT_Database();
$stats = $db->get_statistics();
?>
<div class="dpdt-statistics-section">
    <div class="dpdt-stats-container">
        <div class="dpdt-stat-item">
            <div class="dpdt-stat-number" data-count="<?php echo intval($stats['total']); ?>">0</div>
            <div class="dpdt-stat-label"><?php esc_html_e('মোট আবেদন', 'dpdt-trademark'); ?></div>
        </div>
        <div class="dpdt-stat-item">
            <div class="dpdt-stat-number" data-count="<?php echo intval($stats['approved']); ?>">0</div>
            <div class="dpdt-stat-label"><?php esc_html_e('অনুমোদিত সার্টিফিকেট', 'dpdt-trademark'); ?></div>
        </div>
        <div class="dpdt-stat-item">
            <div class="dpdt-stat-number" data-count="<?php echo intval($stats['this_year']); ?>">0</div>
            <div class="dpdt-stat-label"><?php esc_html_e('এই বছরের আবেদন', 'dpdt-trademark'); ?></div>
        </div>
        <div class="dpdt-stat-item">
            <div class="dpdt-stat-number" data-count="45">45</div>
            <div class="dpdt-stat-label"><?php esc_html_e('ট্রেডমার্ক শ্রেণী', 'dpdt-trademark'); ?></div>
        </div>
    </div>
</div>
