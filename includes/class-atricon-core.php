<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Core plugin functionality
 */
class ATRICON_Core {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('init', array($this, 'init'));
    }
    
    public function init() {
        // Initialize admin functionality
        if (is_admin()) {
            ATRICON_Admin::get_instance();
        }
        
        // Initialize frontend functionality
        if (!is_admin()) {
            ATRICON_Frontend::get_instance();
        }
        
        // Initialize menu functionality
        ATRICON_Menu::get_instance();
        
        // Load text domain
        load_plugin_textdomain('atricon-sidebar-menu', false, dirname(plugin_basename(__FILE__)) . '/languages/');
        
        // Enqueue Dashicons on frontend
        add_action('wp_enqueue_scripts', array($this, 'enqueue_dashicons'), 1);
        
        // AJAX handlers
        add_action('wp_ajax_atricon_search_menu', array($this, 'ajax_search_menu'));
        add_action('wp_ajax_nopriv_atricon_search_menu', array($this, 'ajax_search_menu'));
    }
    
    /**
     * Ensure Dashicons are loaded on frontend
     */
    public function enqueue_dashicons() {
        wp_enqueue_style('dashicons');
    }
    
    /**
     * AJAX handler for menu search
     */
    public function ajax_search_menu() {
        check_ajax_referer('atricon_search', 'nonce');
        
        $query = sanitize_text_field($_POST['query']);
        $menu_instance = ATRICON_Menu::get_instance();
        $results = $menu_instance->search_menu_items($query);
        
        wp_send_json_success($results);
    }
    
    /**
     * Get plugin option with default
     */
    public static function get_option($option_name, $default = '') {
        return get_option('atricon_sidebar_' . $option_name, $default);
    }
    
    /**
     * Update plugin option
     */
    public static function update_option($option_name, $value) {
        return update_option('atricon_sidebar_' . $option_name, $value);
    }
}
