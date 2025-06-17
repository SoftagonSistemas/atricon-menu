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

require_once plugin_dir_path( __FILE__ ) . 'includes/class-atricon-menu.php';

/**
 * Walker customizado para injetar ícones antes do texto.
 */
class ATRICON_Icon_Walker extends Walker_Nav_Menu {
    
    public function start_lvl( &$output, $depth = 0, $args = [] ) {
        $indent = str_repeat( "\t", $depth );
        $output .= "\n$indent<ul class=\"sub-menu\">\n";
    }
    
    public function end_lvl( &$output, $depth = 0, $args = [] ) {
        $indent = str_repeat( "\t", $depth );
        $output .= "$indent</ul>\n";
    }
    
    public function start_el( &$output, $item, $depth = 0, $args = [], $id = 0 ) {
        $indent = str_repeat( "\t", $depth );
        
        $icon_html = '';
        if ( ! empty( $item->attr_title ) ) {
            $icon_name = str_replace( '-', '_', sanitize_text_field( $item->attr_title ) );
            $icon_html = '<i class="material-icons menu-icon" aria-hidden="true">' . esc_html( $icon_name ) . '</i>';
        }
        
        $title = apply_filters( 'nav_menu_item_title', $item->title, $item, $args );
        
        $classes = ['menu-item', 'menu-item-' . $item->ID];
        if ( $depth === 0 ) {
            $classes[] = 'menu-item-top';
        } else {
            $classes[] = 'menu-item-sub';
        }
        
        $class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args ) );
        $class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';
        
        $id = apply_filters( 'nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args );
        $id = $id ? ' id="' . esc_attr( $id ) . '"' : '';
        
        $output .= $indent . '<li' . $id . $class_names .'>';
        
        $item_output = $args->before ?? '';
        $item_output .= '<a href="' . esc_url( $item->url ) . '" class="menu-link">';
        $item_output .= ( $args->link_before ?? '' ) . $icon_html . ' <span class="label">' . esc_html( $title ) . '</span>' . ( $args->link_after ?? '' );
        $item_output .= '</a>';
        $item_output .= $args->after ?? '';
        
        $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
    }
    
    public function end_el( &$output, $item, $depth = 0, $args = [] ) {
        $output .= "</li>\n";
    }
}

class ATRICON_Sidebar_Menu {
    private $menu;

    public function __construct() {
        $this->menu = new ATRICON_Menu();
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
        // Exporta a estrutura do menu para o JS
        $menu_data = json_encode($this->menu->get_menu_items());
        ob_start(); ?>
        <script>window.ATRICON_MENU_DATA = <?php echo $menu_data; ?>;</script>
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
                'theme_location' => 'atrcn-sidebar',
                'container'      => false,
                'menu_class'     => 'menu',
                'items_wrap'     => '<ul id="menu-list" class="menu">%3$s</ul>',
                'depth'          => 2, // Permite submenus
                'walker'         => new ATRICON_Icon_Walker(),
                'fallback_cb'    => false,
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

// Inicializa o plugin
function atricon_sidebar_menu_init() {
    new ATRICON_Sidebar_Menu();
}
add_action( 'plugins_loaded', 'atricon_sidebar_menu_init' );

// Ativação do plugin
register_activation_hook( __FILE__, 'atricon_sidebar_menu_activate' );
function atricon_sidebar_menu_activate() {
    $menu = new ATRICON_Menu();
    $map = $menu->create_menu_pages_and_get_map();
    $menu->create_menu_with_pages($map);
}

// Desativação do plugin
register_deactivation_hook( __FILE__, 'atricon_sidebar_menu_deactivate' );
function atricon_sidebar_menu_deactivate() {
    // Remove o menu ATRICON
    $existing_menu = wp_get_nav_menu_object('ATRICON');
    if ($existing_menu) {
        wp_delete_nav_menu($existing_menu->term_id);
    }
}
