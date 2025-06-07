<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Frontend functionality
 */
class ATRICON_Frontend {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'), 5);
        add_action('wp_footer', array($this, 'render_sidebar'), 999);
    }
    
    public function enqueue_scripts() {
        // Enqueue external CSS
        wp_enqueue_style(
            'atricon-sidebar-css',
            ATRICON_SIDEBAR_PLUGIN_URL . 'assets/atricon-sidebar.css',
            array(),
            ATRICON_SIDEBAR_VERSION
        );

        // Enqueue jQuery
        wp_enqueue_script('jquery');

        // Add inline styles and scripts
        add_action('wp_head', array($this, 'output_inline_css'), 999);
        add_action('wp_footer', array($this, 'output_inline_js'), 999);
    }
    
    public function output_inline_css() {
        $position = ATRICON_Core::get_option('position', 'left');
        $behavior = ATRICON_Core::get_option('behavior', 'icon_only');
        
        $css = $this->generate_dynamic_css($position, $behavior);
        echo '<style id="atricon-sidebar-dynamic-css">' . $css . '</style>';
    }
    
    public function output_inline_js() {
        $js = $this->generate_javascript();
        echo '<script id="atricon-sidebar-js">' . $js . '</script>';
    }
    
    public function render_sidebar() {
        $position = ATRICON_Core::get_option('position', 'left');
        $behavior = ATRICON_Core::get_option('behavior', 'icon_only');
        
        // Add position and behavior classes
        $sidebar_classes = array('atricon-sidebar');
        if ($position === 'right') {
            $sidebar_classes[] = 'position-right';
        }
        if ($behavior === 'always_show') {
            $sidebar_classes[] = 'always-show';
        }
        
        echo '<div class="atricon-overlay"></div>';
        echo '<button class="atricon-mobile-toggle' . ($position === 'right' ? ' position-right' : '') . '" aria-label="' . esc_attr__('Toggle Menu', 'atricon-sidebar-menu') . '">☰</button>';
        echo '<nav id="atricon-sidebar" class="' . implode(' ', $sidebar_classes) . '" role="navigation" aria-label="' . esc_attr__('ATRICON Menu', 'atricon-sidebar-menu') . '">';
        
        $this->render_search_field();
        $this->render_menu_container();
        $this->render_footer();
        
        echo '</nav>';
    }
    
    private function render_search_field() {
        echo '<div class="atricon-search">';
        echo '<input type="text" id="atricon-search-input" placeholder="' . esc_attr__('Buscar no menu...', 'atricon-sidebar-menu') . '">';
        echo '</div>';
    }
    
    private function render_menu_container() {
        echo '<div class="atricon-menu-container" role="menu">';
        
        $menu_instance = ATRICON_Menu::get_instance();
        $menu_instance->render_menu_items();
        
        echo '</div>';
    }
    
    private function render_footer() {
        $logo_url = ATRICON_SIDEBAR_PLUGIN_URL . 'logo.png';
        echo '<div class="atricon-footer-logo">';
        echo '<img src="' . esc_url($logo_url) . '" alt="ATRICON">';
        echo '<span class="atricon-footer-text">ATRICON</span>';
        echo '</div>';
    }
    
    private function generate_dynamic_css($position, $behavior) {
        return "
        #atricon-sidebar {
            " . ($position === 'right' ? 'right: 0; left: auto;' : 'left: 0; right: auto;') . "
            width: " . ($behavior === 'always_show' ? '320px' : '80px') . ";
        }
        
        .atricon-menu-text,
        .atricon-footer-text,
        .atricon-submenu-indicator {
            opacity: " . ($behavior === 'always_show' ? '1' : '0') . ";
            transform: translateX(" . ($behavior === 'always_show' ? '0' : '-16px') . ");
        }
        
        .atricon-mobile-toggle {
            " . ($position === 'right' ? 'right: 24px; left: auto;' : 'left: 24px; right: auto;') . "
        }
        ";
    }
    
    private function generate_javascript() {
        return "
        jQuery(document).ready(function($) {
            // Enhanced sidebar interactions
            const sidebar = $('#atricon-sidebar');
            const overlay = $('.atricon-overlay');
            const toggle = $('.atricon-mobile-toggle');
            const searchInput = $('#atricon-search-input');
            
            // Mobile toggle functionality
            toggle.on('click', function(e) {
                e.preventDefault();
                const isOpen = sidebar.hasClass('mobile-open');
                
                if (isOpen) {
                    closeMobileMenu();
                } else {
                    openMobileMenu();
                }
            });
            
            function openMobileMenu() {
                sidebar.addClass('mobile-open');
                overlay.addClass('active');
                toggle.attr('aria-expanded', 'true');
                $('body').css('overflow', 'hidden');
                setTimeout(() => searchInput.focus(), 300);
            }
            
            function closeMobileMenu() {
                sidebar.removeClass('mobile-open');
                overlay.removeClass('active');
                toggle.attr('aria-expanded', 'false');
                $('body').css('overflow', '');
            }
            
            // Search functionality
            let searchTimeout;
            searchInput.on('input', function() {
                clearTimeout(searchTimeout);
                const query = $(this).val().toLowerCase().trim();
                
                searchTimeout = setTimeout(() => {
                    performSearch(query);
                }, 200);
            });
            
            function performSearch(query) {
                const menuItems = $('.atricon-menu-item');
                let foundItems = 0;
                
                $('.atricon-search-results').remove();
                
                if (query === '') {
                    menuItems.removeClass('search-hidden');
                    return;
                }
                
                menuItems.each(function() {
                    const item = $(this);
                    const text = item.find('.atricon-menu-text').text().toLowerCase();
                    const hasMatch = text.includes(query);
                    
                    if (hasMatch) {
                        item.removeClass('search-hidden');
                        foundItems++;
                    } else {
                        item.addClass('search-hidden');
                    }
                });
                
                const resultsText = foundItems === 0 
                    ? '" . __('Nenhum resultado encontrado', 'atricon-sidebar-menu') . "' 
                    : foundItems + ' resultado' + (foundItems > 1 ? 's' : '') + ' encontrado' + (foundItems > 1 ? 's' : '');
                
                $('.atricon-menu-container').prepend('<div class=\"atricon-search-results\">' + resultsText + '</div>');
            }
            
            // Close overlay click
            overlay.on('click', closeMobileMenu);
            
            // Escape key handler
            $(document).on('keydown', function(e) {
                if (e.key === 'Escape' && sidebar.hasClass('mobile-open')) {
                    closeMobileMenu();
                }
            });
        });
        ";
    }
}

add_action('wp_head', function() {
    echo '<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0" />';
}, 1);
