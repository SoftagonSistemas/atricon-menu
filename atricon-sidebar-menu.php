<?php
/**
 * Plugin Name: ATRICON Sidebar Menu
 * Description: Exibe a sidebar fixa em todas as páginas públicas, com busca, submenus e Material Icons via atributo title.
 * Version:     1.7
 * Author:      Hermes Alves
 * Requires at least: 6.8
 * Text Domain: atricon-sidebar-menu
 */

if ( ! defined( 'ABSPATH' ) ) exit;

require_once plugin_dir_path( __FILE__ ) . 'includes/class-atricon-menu.php';

/**
 * Walker customizado para injetar ícones antes do texto - Otimizado
 */
class ATRICON_Icon_Walker extends Walker_Nav_Menu {
    
    private $current_depth = 0;
    public $has_children = false;
    
    public function start_lvl( &$output, $depth = 0, $args = [] ) {
        $this->current_depth = $depth;
        $indent = str_repeat( "\t", $depth );
        $output .= "\n$indent<ul class=\"sub-menu\">\n";
    }
    
    public function end_lvl( &$output, $depth = 0, $args = [] ) {
        $indent = str_repeat( "\t", $depth );
        $output .= "$indent</ul>\n";
        $this->current_depth = $depth - 1;
    }
    
    public function start_el( &$output, $item, $depth = 0, $args = [], $id = 0 ) {
        $indent = str_repeat( "\t", $depth );
        
        // Processa ícone de forma otimizada
        $icon_html = '';
        if ( ! empty( $item->attr_title ) ) {
            $icon_name = str_replace( '-', '_', sanitize_text_field( $item->attr_title ) );
            $icon_html = '<i class="material-icons menu-icon" aria-hidden="true">' . esc_html( $icon_name ) . '</i>';
        }
        
        $title = apply_filters( 'nav_menu_item_title', $item->title, $item, $args );
        
        // Classes otimizadas
        $classes = ['menu-item', 'menu-item-' . $item->ID];
        if ( $depth === 0 ) {
            $classes[] = 'menu-item-top';
            // Verifica se tem filhos para adicionar classe
            $this->has_children = ! empty( $item->classes ) && in_array( 'menu-item-has-children', $item->classes );
            if ( $this->has_children ) {
                $classes[] = 'menu-item-has-children';
            }
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
    private $menu_cache = null;

    public function __construct() {
        $this->menu = new ATRICON_Menu();
        add_filter( 'elementor/frontend/print_google_fonts', '__return_false' );
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_assets' ] );
        add_action( 'wp_body_open',       [ $this, 'print_sidebar' ] );
        add_action( 'wp_footer',          [ $this, 'print_sidebar_fallback' ] );
        add_action( 'wp_update_nav_menu', [ $this, 'clear_cache' ] );
        add_action( 'wp_delete_nav_menu', [ $this, 'clear_cache' ] );
        
        // Limpa cache na inicialização para garantir que as mudanças sejam aplicadas
        $this->clear_cache();
    }

    /**
     * Limpa cache quando há alterações no menu
     */
    public function clear_cache() {
        $this->menu_cache = null;
        delete_transient( 'atricon_sidebar_menu_cache' );
    }

    public function enqueue_assets() {
        $url = plugin_dir_url( __FILE__ );
        wp_enqueue_style( 'atricon-roboto', 'https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap', [], null );
        wp_enqueue_style( 'atricon-material-icons', 'https://fonts.googleapis.com/icon?family=Material+Icons', [], null );
        wp_enqueue_style( 'atricon-sidebar', $url . 'assets/css/sidebar.css', [], '1.7.1' );
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

    /**
     * Obtém o markup da sidebar com cache
     */
    private function get_sidebar_markup() {
        // Verifica cache
        if ( $this->menu_cache ) {
            return $this->menu_cache;
        }

        $cached = get_transient( 'atricon_sidebar_menu_cache' );
        if ( $cached !== false ) {
            $this->menu_cache = $cached;
            return $cached;
        }

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
                <div id="search-results-count"></div>
              </div>
            </div>
            <?php
            // Busca o menu de forma otimizada
            $menu_location = 'atrcn-sidebar';
            $locations = get_nav_menu_locations();
            $menu_id = isset( $locations[ $menu_location ] ) ? $locations[ $menu_location ] : null;
            
            if ( $menu_id ) {
                $menu_items = wp_get_nav_menu_items( $menu_id );
                if ( ! empty( $menu_items ) ) {
                    wp_nav_menu([
                        'theme_location' => $menu_location,
                        'container'      => false,
                        'menu_class'     => 'menu',
                        'items_wrap'     => '<ul id="menu-list" class="menu">%3$s</ul>',
                        'depth'          => 2, // Permite submenus
                        'walker'         => new ATRICON_Icon_Walker(),
                        'fallback_cb'    => false,
                    ]);
                } else {
                    // Fallback se o menu estiver vazio
                    echo '<ul id="menu-list" class="menu"><li class="menu-item menu-item-top"><a href="#" class="menu-link"><i class="material-icons menu-icon" aria-hidden="true">menu</i> <span class="label">Menu não configurado</span></a></li></ul>';
                }
            } else {
                // Fallback se não houver menu configurado
                echo '<ul id="menu-list" class="menu"><li class="menu-item menu-item-top"><a href="#" class="menu-link"><i class="material-icons menu-icon" aria-hidden="true">menu</i> <span class="label">Menu não configurado</span></a></li></ul>';
            }
            ?>
            <div class="sidebar-footer">
              <div class="footer-logo-container">
                <img src="<?php echo plugin_dir_url( __FILE__ ); ?>logo.png" alt="ATRICON Logo" class="footer-logo" />
              </div>
              <span class="footer-label">ATRICON</span>
            </div>
          </aside>
          <aside id="submenu" class="submenu-sidebar">
            <h2 id="submenu-title"></h2>
            <div id="submenu-content" class="submenu"></div>
          </aside>
        </div>
        <?php
        $markup = ob_get_clean();
        
        // Salva no cache por 5 minutos
        $this->menu_cache = $markup;
        set_transient( 'atricon_sidebar_menu_cache', $markup, 300 );
        
        return $markup;
    }
}

// Inicializa o plugin
function atricon_sidebar_menu_init() {
    new ATRICON_Sidebar_Menu();
}
add_action( 'plugins_loaded', 'atricon_sidebar_menu_init' );

// Ativação do plugin
register_activation_hook( __FILE__, function() {
    $menu = new ATRICON_Menu();
    $menu->on_activation();
} );

// Desativação do plugin
register_deactivation_hook( __FILE__, 'atricon_sidebar_menu_deactivate' );
function atricon_sidebar_menu_deactivate() {
    // Remove o menu ATRICON
    $existing_menu = wp_get_nav_menu_object('ATRICON');
    if ($existing_menu) {
        wp_delete_nav_menu($existing_menu->term_id);
    }
    
    // Limpa cache
    delete_transient( 'atricon_sidebar_menu_cache' );
    delete_transient( 'atricon_menu_cache' );
    delete_transient( 'atricon_csv_content' );
}
