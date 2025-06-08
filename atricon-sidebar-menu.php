<?php
/**
 * Plugin Name: ATRICON Sidebar Menu
 * Description: Exibe a sidebar fixa em todas as páginas públicas, com busca, submenus e Material Icons via atributo title.
 * Version:     1.4
 * Author:      Hermes Alves
 * Requires at least: 6.8
 * Text Domain: atricon-sidebar-menu
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Walker customizado para injetar ícones antes do texto.
 */
class ATRICON_Icon_Walker extends Walker_Nav_Menu {
    public function start_el( &$output, $item, $depth = 0, $args = [], $id = 0 ) {
        // Obtém nome do ícone do atributo title e ajusta hífens para underscore
        $icon_html = '';
        if ( ! empty( $item->attr_title ) ) {
            $icon_name = str_replace( '-', '_', sanitize_text_field( $item->attr_title ) );
            $icon_html = '<i class="material-icons menu-icon" aria-hidden="true">' . esc_html( $icon_name ) . '</i>';
        }
        $title = apply_filters( 'nav_menu_item_title', $item->title, $item, $args );

        $output .= '<li id="menu-item-' . $item->ID . '" class="menu-item menu-item-' . $item->ID . '">';
        $output .= '<a href="' . esc_url( $item->url ) . '" class="menu-link">';
        // Espaço separado para garantir não colidir
        $output .= $icon_html . ' <span class="label">' . esc_html( $title ) . '</span>';
        $output .= '</a>';
    }

    public function end_el( &$output, $item, $depth = 0, $args = [] ) {
        $output .= "</li>\n";
    }
}

class ATRICON_Sidebar_Menu {

    public function __construct() {
        add_filter( 'elementor/frontend/print_google_fonts', '__return_false' );
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_assets' ] );
        add_action( 'wp_body_open',       [ $this, 'print_sidebar' ] );
        add_action( 'wp_footer',          [ $this, 'print_sidebar_fallback' ] );
    }

    public function enqueue_assets() {
        $url = plugin_dir_url( __FILE__ );
        wp_enqueue_style( 'atricon-roboto', 'https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap', [], null );
        wp_enqueue_style( 'atricon-material-icons', 'https://fonts.googleapis.com/icon?family=Material+Icons', [], null );
        wp_enqueue_style( 'atricon-sidebar', $url . 'assets/css/sidebar.css', [], null );
        wp_enqueue_script( 'jquery' );
        wp_enqueue_script( 'atricon-sidebar-js', $url . 'assets/js/sidebar.js', [ 'jquery' ], null, true );
    }

    public function print_sidebar() {
        if ( is_admin() || is_preview() ) return;
        echo $this->get_sidebar_markup();
    }

    public function print_sidebar_fallback() {
        if ( did_action( 'wp_body_open' ) || is_admin() || is_preview() ) return;
        echo $this->get_sidebar_markup();
    }

    private function get_sidebar_markup() {
        ob_start(); ?>
        <div id="menu-container" class="container">
          <aside id="main-sidebar" class="sidebar">
            <div class="search-box">
              <div class="search-wrap">
                <input type="text" id="menu-search" placeholder="Buscar no menu...">
                <i class="material-icons clear-search" id="clear-search">close</i>
              </div>
            </div>
            <?php
            wp_nav_menu([
                'menu'       => 29,
                'container'  => false,
                'menu_class' => 'menu',
                'items_wrap' => '<ul id="menu-list" class="menu">%3$s</ul>',
                'depth'      => 2,
                'walker'     => new ATRICON_Icon_Walker(),
                'fallback_cb'=> false,
            ]);
            ?>
          </aside>
          <aside id="submenu" class="submenu-sidebar">
            <h2 id="submenu-title"></h2>
            <div id="submenu-content" class="submenu"></div>
          </aside>
        </div>
        <?php
        return ob_get_clean();
    }
}

new ATRICON_Sidebar_Menu();
