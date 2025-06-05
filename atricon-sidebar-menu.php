<?php
/**
 * Plugin Name: ATRICON Sidebar Menu
 * Description: Menu lateral moderno com Material Design 3 Expressive, acessibilidade aprimorada e sistema de cores dinâmico
 * Version:     1.0
 * Author:      Hermes
 * Text Domain: atricon-sidebar-menu
 */

// Previne acesso direto
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('ATRICON_SIDEBAR_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('ATRICON_SIDEBAR_PLUGIN_URL', plugin_dir_url(__FILE__));
define('ATRICON_SIDEBAR_VERSION', '1.0');

// Include required files
require_once ATRICON_SIDEBAR_PLUGIN_DIR . 'includes/class-atricon-core.php';
require_once ATRICON_SIDEBAR_PLUGIN_DIR . 'includes/class-atricon-admin.php';
require_once ATRICON_SIDEBAR_PLUGIN_DIR . 'includes/class-atricon-frontend.php';
require_once ATRICON_SIDEBAR_PLUGIN_DIR . 'includes/class-atricon-menu.php';

/**
 * Main plugin bootstrap class
 */
class ATRICON_Sidebar_Menu {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('plugins_loaded', array($this, 'init'));
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
    }
    
    public function init() {
        // Initialize core functionality
        if (class_exists('ATRICON_Core')) {
            ATRICON_Core::get_instance();
        }
    }
    
    public function activate() {
        // Create menu on activation
        if (class_exists('ATRICON_Menu')) {
            ATRICON_Menu::get_instance()->create_atricon_menu();
        }
    }
    
    public function deactivate() {
        // Cleanup if needed
    }
}

// Initialize the plugin
ATRICON_Sidebar_Menu::get_instance();
