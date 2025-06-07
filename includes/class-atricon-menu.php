<?php
/**
 * ATRICON Menu Class
 *
 * @package ATRICON_Sidebar_Menu
 */

if (!defined('ABSPATH')) {
    exit;
}

class ATRICON_Menu {
    /**
     * Constructor
     */
    public function __construct() {
        add_action('init', [$this, 'register_menu']);
    }

    /**
     * Register the menu location
     */
    public function register_menu() {
        register_nav_menu('atrcn-sidebar', 'ATRICON Sidebar Menu');
    }

    /**
     * Get menu items structure
     *
     * @return array Menu items array
     */
    public function get_menu_items() {
        return [
            ['t'=>'BUSCA_PLACEHOLDER','v'=>'busca-servico','icon'=>'search'], // Caixa de busca no topo
            ['t'=>'TRANSPARÊNCIA','v'=>'transparencia','icon'=>'visibility'],
            ['t'=>'Organização Administrativa','v'=>'organizacao','icon'=>'networking','c'=>[
                ['t'=>'Estrutura Organizacional','v'=>'estrutura-organizacional','code'=>'2.1 a 2.5','icon'=>'chart-pie'],
                ['t'=>'Recursos Humanos','v'=>'recursos-humanos','code'=>'6.1 a 6.6','icon'=>'admin-users'],
                ['t'=>'Convênios e Transferências','v'=>'convenios-transferencias','code'=>'5.1 a 5.3','icon'=>'admin-links'],
            ]],
            ['t'=>'Normas e Leis','v'=>'leis','icon'=>'admin-page','c'=>[
                ['t'=>'Legislações e Atos','v'=>'legislacoes','code'=>'2.6','icon'=>'media-document'],
                ['t'=>'LAI','v'=>'lai','code'=>'Lei 12.527/2011','icon'=>'admin-network'],
                ['t'=>'LRF','v'=>'lrf','code'=>'LC 101/2000','icon'=>'businessman'],
                ['t'=>'LGPD e Governo Digital','v'=>'lgpd-governo','code'=>'15.1 a 15.6','icon'=>'shield-alt'],
            ]],
            ['t'=>'Contabilidade Pública','v'=>'contabilidade','icon'=>'money-alt','c'=>[
                ['t'=>'Receitas','v'=>'receitas','code'=>'3.1 a 3.3','icon'=>'cart'],
                ['t'=>'Despesas','v'=>'despesas','code'=>'4.1 a 4.2','icon'=>'money'],
                ['t'=>'Renúncias de Receitas','v'=>'renuncias','code'=>'16.1 a 16.4','icon'=>'percent'],
                ['t'=>'Dívida Ativa','v'=>'divida-ativa','code'=>'3.3','icon'=>'warning'],
            ]],
            ['t'=>'Gestão de Recursos','v'=>'recursos','icon'=>'chart-bar','c'=>[
                ['t'=>'Planejamento e Contas','v'=>'planejamento','code'=>'11.1 a 11.10','icon'=>'clipboard'],
                ['t'=>'Emendas Parlamentares','v'=>'emendas','code'=>'17.1 a 17.2','icon'=>'edit'],
            ]],
            ['t'=>'Contratos e Licitações','v'=>'contratos','icon'=>'media-text','c'=>[
                ['t'=>'Licitações e Contratos','v'=>'licitacoes','code'=>'8.1 a 9.4','icon'=>'media-document'],
                ['t'=>'Ordem Cronológica','v'=>'ordem-cronologica','code'=>'9.4','icon'=>'calendar-alt'],
            ]],
            ['t'=>'Despesas com Pessoal','v'=>'pessoal','icon'=>'admin-users','c'=>[
                ['t'=>'Diárias e Passagens','v'=>'diarias','code'=>'7.1 a 7.2','icon'=>'airplane'],
                ['t'=>'Valores das Diárias','v'=>'valores-diarias','code'=>'7.2','icon'=>'calculator'],
            ]],
            ['t'=>'Cidadania e Acesso','v'=>'cidadania','icon'=>'admin-users','c'=>[
                ['t'=>'SIC','v'=>'sic','code'=>'12.1 a 12.9','icon'=>'info'],
                ['t'=>'Ouvidorias','v'=>'ouvidorias','code'=>'14.1 a 14.3','icon'=>'microphone'],
                ['t'=>'Perguntas Frequentes','v'=>'faq','code'=>'2.7','icon'=>'editor-help'],
                ['t'=>'Carta de Serviços ao Cidadão','v'=>'carta-servicos','code'=>'','icon'=>'email-alt'],
            ]],
            ['t'=>'Publicações Oficiais','v'=>'publicacoes','icon'=>'media-text','c'=>[
                ['t'=>'Diário Oficial','v'=>'diario-oficial','code'=>'Lei 4.965/1966','icon'=>'book-alt'],
                ['t'=>'Transparência COVID-19','v'=>'transparencia-covid','code'=>'PRSE/MPF 12/2022','icon'=>'admin-site-alt3'],
            ]],
            ['t'=>'Indicadores e Avaliação','v'=>'avaliacao','icon'=>'awards','c'=>[
                ['t'=>'Radar da Transparência Pública','v'=>'radar-transparencia','code'=>'2.9','icon'=>'visibility'],
                ['t'=>'Dados Abertos','v'=>'dados-abertos','code'=>'CGU','icon'=>'database-view'],
            ]],
            ['t'=>'Serviços Essenciais','v'=>'servicos','icon'=>'building','c'=>[
                ['t'=>'Obras','v'=>'obras','code'=>'10.1 a 10.4','icon'=>'hammer'],
                ['t'=>'Saúde','v'=>'saude','code'=>'18.1 a 18.3','icon'=>'heart'],
                ['t'=>'Educação','v'=>'educacao','code'=>'19.1 a 19.2','icon'=>'welcome-learn-more'],
            ]],
        ];
    }

    /**
     * Get content from CSV file
     *
     * @return array Array of menu items with their content
     */
    private function get_menu_content_from_csv() {
        $csv_file = plugin_dir_path(dirname(__FILE__)) . 'assets/atricon_menu_conteudo_modelo.csv';
        $content = [];
        
        if (($handle = fopen($csv_file, "r")) !== FALSE) {
            // Skip header row
            fgetcsv($handle);
            
            while (($data = fgetcsv($handle)) !== FALSE) {
                if (count($data) >= 4) {
                    $menu = trim($data[0]);
                    $submenu = trim($data[1]);
                    $code = trim($data[2]);
                    $conteudo = trim($data[3]);
                    
                    if (!empty($submenu)) {
                        $content[$submenu] = [
                            'menu' => $menu,
                            'code' => $code,
                            'content' => $conteudo
                        ];
                    }
                }
            }
            fclose($handle);
        }
        
        return $content;
    }

    /**
     * Cria todas as páginas do menu e retorna um mapa de caminho => ID
     */
    public function create_menu_pages_and_get_map() {
        $menu_items = $this->get_menu_items();
        $menu_content = $this->get_menu_content_from_csv();
        $map = [];
        foreach ($menu_items as $item) {
            $this->create_page_for_item_and_map($item, '', $menu_content, $map);
        }
        return $map;
    }

    /**
     * Cria página para o item e preenche o mapa de caminho => ID
     */
    private function create_page_for_item_and_map($item, $parent_slug = '', $menu_content = [], &$map = []) {
        if ($item['v'] === 'busca-servico') return;

        $slug = sanitize_title($item['v']);
        $path = $parent_slug ? 'atricon/' . sanitize_title($parent_slug) . '/' . $slug : 'atricon/' . $slug;

        // Busca página pelo slug e pai
        $page_args = [
            'name'        => $slug,
            'post_type'   => 'page',
            'post_status' => 'publish',
            'numberposts' => 1,
        ];
        $parent_id = 0;
        if ($parent_slug) {
            $parent_page = get_page_by_path('atricon/' . sanitize_title($parent_slug), OBJECT, 'page');
            if ($parent_page) {
                $page_args['post_parent'] = $parent_page->ID;
                $parent_id = $parent_page->ID;
            }
        }
        $existing_pages = get_posts($page_args);
        if (!empty($existing_pages)) {
            $page_id = $existing_pages[0]->ID;
        } else {
            $content = isset($menu_content[$item['t']]) ? $menu_content[$item['t']]['content'] : '<h2>' . esc_html($item['t']) . '</h2>' . (!empty($item['code']) ? '<p>Código de referência: ' . esc_html($item['code']) . '</p>' : '') . '<p>Esta página foi criada automaticamente pelo plugin ATRICON.</p>';
            $page_args_insert = [
                'post_title'    => $item['t'],
                'post_name'     => $slug,
                'post_content'  => $content,
                'post_status'   => 'publish',
                'post_type'     => 'page',
                'post_parent'   => $parent_id,
            ];
            $page_id = wp_insert_post($page_args_insert);
        }
        $map[$path] = $page_id;

        if (!empty($item['c'])) {
            foreach ($item['c'] as $child_item) {
                $this->create_page_for_item_and_map($child_item, $slug, $menu_content, $map);
            }
        }
    }

    /**
     * Cria o menu ATRICON usando o mapa de páginas criadas
     */
    public function create_menu_with_pages($map) {
        // Remove o menu existente se houver
        $existing_menu = wp_get_nav_menu_object('ATRICON');
        if ($existing_menu) {
            wp_delete_nav_menu($existing_menu->term_id);
        }
        $menu_id = wp_create_nav_menu('ATRICON');
        $items = $this->get_menu_items();

        foreach ($items as $i) {
            $parent_path = 'atricon/' . sanitize_title($i['v']);
            $url = isset($map[$parent_path]) ? get_permalink($map[$parent_path]) : home_url('/' . $parent_path);
            $pid = wp_update_nav_menu_item($menu_id, 0, [
                'menu-item-title'  => $i['t'],
                'menu-item-url'    => $url,
                'menu-item-status' => 'publish',
            ]);
            if (!empty($i['c'])) {
                foreach ($i['c'] as $c) {
                    $child_path = 'atricon/' . sanitize_title($i['v']) . '/' . sanitize_title($c['v']);
                    $child_url = isset($map[$child_path]) ? get_permalink($map[$child_path]) : home_url('/' . $child_path);
                    wp_update_nav_menu_item($menu_id, 0, [
                        'menu-item-title'     => $c['t'] . ($c['code'] ? " ({$c['code']})" : ''),
                        'menu-item-url'       => $child_url,
                        'menu-item-parent-id' => $pid,
                        'menu-item-status'    => 'publish',
                    ]);
                }
            }
        }
        set_theme_mod('nav_menu_locations', array_merge(
            (array)get_theme_mod('nav_menu_locations'), ['atrcn-sidebar' => $menu_id]
        ));
    }
}
