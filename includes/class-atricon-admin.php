<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Admin panel functionality
 */
class ATRICON_Admin {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'admin_init'));
    }
    
    public function add_admin_menu() {
        add_options_page(
            __('ATRICON Sidebar', 'atricon-sidebar-menu'),
            __('ATRICON Sidebar', 'atricon-sidebar-menu'),
            'manage_options',
            'atricon-sidebar-menu',
            array($this, 'admin_page')
        );
    }
    
    public function admin_init() {
        // Register settings
        register_setting('atricon_sidebar_settings', 'atricon_sidebar_position');
        register_setting('atricon_sidebar_settings', 'atricon_sidebar_behavior');
    }
    
    public function admin_page() {
        // Handle form submissions
        if (isset($_POST['submit'])) {
            $this->handle_form_submission();
        }
        
        $position = ATRICON_Core::get_option('position', 'left');
        $behavior = ATRICON_Core::get_option('behavior', 'icon_only');
        $menu_instance = ATRICON_Menu::get_instance();
        ?>
        <div class="wrap">
            <h1><?php _e('ATRICON Sidebar - Configurações', 'atricon-sidebar-menu'); ?></h1>
            
            <?php if (!$menu_instance->is_menu_icons_active()): ?>
            <div class="notice notice-warning">
                <p>
                    <strong><?php _e('Plugin Menu Icons necessário:', 'atricon-sidebar-menu'); ?></strong>
                    <?php _e('Para o funcionamento completo do ATRICON Sidebar, instale e ative o plugin', 'atricon-sidebar-menu'); ?>
                    <a href="<?php echo admin_url('plugin-install.php?s=menu+icons&tab=search&type=term'); ?>" target="_blank">Menu Icons</a>.
                </p>
            </div>
            <?php endif; ?>
            
            <form method="post" action="">
                <?php wp_nonce_field('atricon_sidebar_save'); ?>
                
                <table class="form-table">
                    <tr>
                        <th scope="row"><?php _e('Posição do menu lateral', 'atricon-sidebar-menu'); ?></th>
                        <td>
                            <label>
                                <input type="radio" name="position" value="left" <?php checked($position, 'left'); ?>>
                                <?php _e('Esquerda', 'atricon-sidebar-menu'); ?>
                            </label><br>
                            <label>
                                <input type="radio" name="position" value="right" <?php checked($position, 'right'); ?>>
                                <?php _e('Direita', 'atricon-sidebar-menu'); ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('Comportamento do menu', 'atricon-sidebar-menu'); ?></th>
                        <td>
                            <label>
                                <input type="radio" name="behavior" value="icon_only" <?php checked($behavior, 'icon_only'); ?>>
                                <?php _e('Apenas ícones (expandir para ver títulos)', 'atricon-sidebar-menu'); ?>
                            </label><br>
                            <label>
                                <input type="radio" name="behavior" value="always_show" <?php checked($behavior, 'always_show'); ?>>
                                <?php _e('Sempre mostrar ícones e títulos', 'atricon-sidebar-menu'); ?>
                            </label>
                            <p class="description">
                                <?php _e('No modo "apenas ícones", os títulos aparecerão apenas quando você passar o mouse sobre o menu.', 'atricon-sidebar-menu'); ?>
                            </p>
                        </td>
                    </tr>
                </table>
                
                <?php if ($menu_instance->is_menu_icons_active()): ?>
                <h3><?php _e('Gerenciar Menu', 'atricon-sidebar-menu'); ?></h3>
                <table class="form-table">
                    <tr>
                        <th scope="row"><?php _e('Ações do Menu', 'atricon-sidebar-menu'); ?></th>
                        <td>
                            <button type="submit" name="action" value="update_icons" class="button-secondary">
                                <?php _e('Atualizar Ícones', 'atricon-sidebar-menu'); ?>
                            </button>
                            <button type="submit" name="action" value="reset_menu" class="button-secondary">
                                <?php _e('Resetar Menu', 'atricon-sidebar-menu'); ?>
                            </button>
                        </td>
                    </tr>
                </table>
                <?php endif; ?>
                
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }
    
    private function handle_form_submission() {
        if (!wp_verify_nonce($_POST['_wpnonce'], 'atricon_sidebar_save')) {
            return;
        }
        
        if (isset($_POST['position'])) {
            $position = sanitize_text_field($_POST['position']);
            ATRICON_Core::update_option('position', $position);
            add_settings_error('atricon_sidebar', 'position_updated', __('Posição atualizada!', 'atricon-sidebar-menu'), 'updated');
        }
        
        if (isset($_POST['behavior'])) {
            $behavior = sanitize_text_field($_POST['behavior']);
            ATRICON_Core::update_option('behavior', $behavior);
            add_settings_error('atricon_sidebar', 'behavior_updated', __('Comportamento atualizado!', 'atricon-sidebar-menu'), 'updated');
        }
        
        if (isset($_POST['action'])) {
            $menu_instance = ATRICON_Menu::get_instance();
            
            switch ($_POST['action']) {
                case 'update_icons':
                    $menu_instance->update_menu_icons();
                    add_settings_error('atricon_sidebar', 'icons_updated', __('Ícones atualizados!', 'atricon-sidebar-menu'), 'updated');
                    break;
                    
                case 'reset_menu':
                    $menu_instance->reset_menu();
                    add_settings_error('atricon_sidebar', 'menu_reset', __('Menu resetado!', 'atricon-sidebar-menu'), 'updated');
                    break;
            }
        }
        
        settings_errors('atricon_sidebar');
    }
}
