<?php
/**
 * Plugin Name: Floating Ad Manager
 * Plugin URI: https://github.com/itgoyo
 * Description: 悬浮广告管理插件 - 用户可配置悬浮广告位置、间距和自定义前端代码
 * Version: 1.0.3
 * Author: itgoyo
 * License: GPL v2 or later
 * Text Domain: floating-ad-manager
 */

if (!defined('ABSPATH')) {
    exit;
}

define('FAM_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('FAM_PLUGIN_URL', plugin_dir_url(__FILE__));
define('FAM_VERSION', '1.0.3');

class Floating_Ad_Manager {

    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('admin_init', [$this, 'register_settings']);
        add_action('admin_enqueue_scripts', [$this, 'admin_enqueue_scripts']);
        add_action('wp_footer', [$this, 'render_floating_ad']);
    }

    /**
     * 添加管理菜单
     */
    public function add_admin_menu() {
        add_menu_page(
            __('悬浮广告管理', 'floating-ad-manager'),
            __('悬浮广告', 'floating-ad-manager'),
            'manage_options',
            'floating-ad-manager',
            [$this, 'render_admin_page'],
            'dashicons-megaphone',
            80
        );
    }

    /**
     * 注册设置
     */
    public function register_settings() {
        register_setting('fam_settings_group', 'fam_ads', [
            'type' => 'array',
            'sanitize_callback' => [$this, 'sanitize_ads'],
            'default' => [],
        ]);
    }

    /**
     * 数据清理
     */
    public function sanitize_ads($input) {
        if (!is_array($input)) {
            return [];
        }

        $sanitized = [];
        foreach ($input as $ad) {
            if (empty($ad['code'])) {
                continue;
            }

            $sanitized[] = [
                'enabled'  => !empty($ad['enabled']) ? 1 : 0,
                'name'     => sanitize_text_field($ad['name'] ?? ''),
                'position' => in_array($ad['position'] ?? '', ['top-left', 'top-center', 'top-right', 'bottom-left', 'bottom-center', 'bottom-right', 'center-left', 'center-right'], true)
                    ? $ad['position'] : 'bottom-right',
                'margin_top'    => intval($ad['margin_top'] ?? 20),
                'margin_right'  => intval($ad['margin_right'] ?? 20),
                'margin_bottom' => intval($ad['margin_bottom'] ?? 20),
                'margin_left'   => intval($ad['margin_left'] ?? 20),
                'z_index'       => intval($ad['z_index'] ?? 9999),
                'show_close'    => !empty($ad['show_close']) ? 1 : 0,
                'show_on'       => in_array($ad['show_on'] ?? '', ['all', 'home', 'post', 'page'], true)
                    ? $ad['show_on'] : 'all',
                'code'     => $ad['code'] ?? '',
            ];
        }

        return $sanitized;
    }

    /**
     * 加载后台资源
     */
    public function admin_enqueue_scripts($hook) {
        if ('toplevel_page_floating-ad-manager' !== $hook) {
            return;
        }

        wp_enqueue_style('fam-admin-css', FAM_PLUGIN_URL . 'assets/admin.css', [], FAM_VERSION);
        wp_enqueue_script('fam-admin-js', FAM_PLUGIN_URL . 'assets/admin.js', ['jquery'], FAM_VERSION, true);
    }

    /**
     * 渲染管理页面
     */
    public function render_admin_page() {
        if (!current_user_can('manage_options')) {
            return;
        }

        $ads = get_option('fam_ads', []);
        if (!is_array($ads)) {
            $ads = [];
        }

        include FAM_PLUGIN_DIR . 'templates/admin-page.php';
    }

    /**
     * 前端渲染悬浮广告
     */
    public function render_floating_ad() {
        $ads = get_option('fam_ads', []);
        if (!is_array($ads) || empty($ads)) {
            return;
        }

        foreach ($ads as $index => $ad) {
            if (empty($ad['enabled'])) {
                continue;
            }

            // 页面显示条件
            $show_on = $ad['show_on'] ?? 'all';
            if ($show_on === 'home' && !is_front_page()) continue;
            if ($show_on === 'post' && !is_single()) continue;
            if ($show_on === 'page' && !is_page()) continue;

            $position = $ad['position'] ?? 'bottom-right';
            $margin_top = intval($ad['margin_top'] ?? 20);
            $margin_right = intval($ad['margin_right'] ?? 20);
            $margin_bottom = intval($ad['margin_bottom'] ?? 20);
            $margin_left = intval($ad['margin_left'] ?? 20);
            $z_index = intval($ad['z_index'] ?? 9999);
            $show_close = !empty($ad['show_close']);
            $code = $ad['code'] ?? '';

            // 根据位置计算CSS
            $style_parts = [
                'position: fixed',
                "z-index: {$z_index}",
            ];

            switch ($position) {
                case 'top-left':
                    $style_parts[] = "top: {$margin_top}px";
                    $style_parts[] = "left: {$margin_left}px";
                    break;
                case 'top-center':
                    $style_parts[] = "top: {$margin_top}px";
                    $style_parts[] = "left: 50%";
                    $style_parts[] = "transform: translateX(-50%)";
                    $style_parts[] = "margin-left: " . ($margin_left - $margin_right) . "px";
                    break;
                case 'top-right':
                    $style_parts[] = "top: {$margin_top}px";
                    $style_parts[] = "right: {$margin_right}px";
                    break;
                case 'bottom-left':
                    $style_parts[] = "bottom: {$margin_bottom}px";
                    $style_parts[] = "left: {$margin_left}px";
                    break;
                case 'bottom-center':
                    $style_parts[] = "bottom: {$margin_bottom}px";
                    $style_parts[] = "left: 50%";
                    $style_parts[] = "transform: translateX(-50%)";
                    $style_parts[] = "margin-left: " . ($margin_left - $margin_right) . "px";
                    break;
                case 'bottom-right':
                    $style_parts[] = "bottom: {$margin_bottom}px";
                    $style_parts[] = "right: {$margin_right}px";
                    break;
                case 'center-left':
                    $style_parts[] = "top: 50%";
                    $style_parts[] = "transform: translateY(-50%)";
                    $style_parts[] = "left: {$margin_left}px";
                    break;
                case 'center-right':
                    $style_parts[] = "top: 50%";
                    $style_parts[] = "transform: translateY(-50%)";
                    $style_parts[] = "right: {$margin_right}px";
                    break;
            }

            $style = implode('; ', $style_parts);
            $ad_id = 'fam-ad-' . intval($index);

            echo '<div id="' . esc_attr($ad_id) . '" class="fam-floating-ad" style="' . esc_attr($style) . '">';

            if ($show_close) {
                echo '<span class="fam-close-btn" onclick="this.parentElement.style.display=\'none\'" style="position:absolute;top:-10px;right:-10px;width:22px;height:22px;background:#ff4444;color:#fff;border-radius:50%;text-align:center;line-height:22px;cursor:pointer;font-size:14px;font-weight:bold;box-shadow:0 1px 3px rgba(0,0,0,0.3);">&times;</span>';
            }

            // 输出用户自定义代码（仅管理员可设置，允许HTML/JS）
            echo $code;
            echo '</div>';
        }
    }
}

// 初始化插件
Floating_Ad_Manager::get_instance();

// 激活钩子
register_activation_hook(__FILE__, function () {
    if (!get_option('fam_ads')) {
        add_option('fam_ads', []);
    }
});

// 卸载钩子
register_uninstall_hook(__FILE__, 'fam_uninstall');
function fam_uninstall() {
    delete_option('fam_ads');
}
