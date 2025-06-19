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
     * Cache para itens do menu
     */
    private $menu_cache = null;
    private $menu_cache_time = 0;
    private $cache_duration = 300; // 5 minutos

    /**
     * Constructor
     */
    public function __construct() {
        add_action('init', [$this, 'register_menu']);
        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('wp_update_nav_menu', [$this, 'clear_menu_cache']);
        add_action('wp_delete_nav_menu', [$this, 'clear_menu_cache']);
        
        // Adiciona ação de ativação
        register_activation_hook(plugin_dir_path(dirname(__FILE__)) . 'atricon-sidebar-menu.php', [$this, 'on_activation']);
    }

    /**
     * Método executado na ativação do plugin
     */
    public function on_activation() {
        // Cria as páginas e obtém o mapa
        $map = $this->create_menu_pages_and_get_map();
        
        // Cria o menu usando as páginas
        $this->create_menu_with_pages($map);
    }

    /**
     * Register the menu location
     */
    public function register_menu() {
        register_nav_menu('atrcn-sidebar', 'ATRICON Sidebar Menu');
    }

    /**
     * Limpa o cache do menu quando há alterações
     */
    public function clear_menu_cache() {
        $this->menu_cache = null;
        $this->menu_cache_time = 0;
        delete_transient('atricon_menu_cache');
    }

    /**
     * Get menu items structure with caching
     *
     * @return array Menu items array
     */
    public function get_menu_items() {
        // Verifica cache primeiro
        if ($this->menu_cache && (time() - $this->menu_cache_time) < $this->cache_duration) {
            return $this->menu_cache;
        }

        // Tenta buscar do cache transiente
        $cached = get_transient('atricon_menu_cache');
        if ($cached !== false) {
            $this->menu_cache = $cached;
            $this->menu_cache_time = time();
            return $cached;
        }

        // Estrutura base do menu
        $menu_structure = [
            ['t'=>'BUSCA_PLACEHOLDER','v'=>'busca-servico','icon'=>'search'],
            ['t'=>'TRANSPARÊNCIA','v'=>'transparencia','icon'=>'visibility'],
            ['t'=>'Organização Administrativa','v'=>'organizacao','icon'=>'account_balance','c'=>[
                ['t'=>'Estrutura Organizacional','v'=>'estrutura-organizacional','code'=>'2.1 a 2.5','icon'=>'pie_chart'],
                ['t'=>'Recursos Humanos','v'=>'recursos-humanos','code'=>'6.1 a 6.6','icon'=>'people'],
                ['t'=>'Convênios e Transferências','v'=>'convenios-transferencias','code'=>'5.1 a 5.3','icon'=>'link'],
            ]],
            ['t'=>'Normas e Leis','v'=>'leis','icon'=>'description','c'=>[
                ['t'=>'Legislações e Atos','v'=>'legislacoes','code'=>'2.6','icon'=>'description'],
                ['t'=>'LAI','v'=>'lai','code'=>'Lei 12.527/2011','icon'=>'public'],
                ['t'=>'LRF','v'=>'lrf','code'=>'LC 101/2000','icon'=>'person'],
                ['t'=>'LGPD e Governo Digital','v'=>'lgpd-governo','code'=>'15.1 a 15.6','icon'=>'shield'],
            ]],
            ['t'=>'Contabilidade Pública','v'=>'contabilidade','icon'=>'payments','c'=>[
                ['t'=>'Receitas','v'=>'receitas','code'=>'3.1 a 3.3','icon'=>'shopping_cart'],
                ['t'=>'Despesas','v'=>'despesas','code'=>'4.1 a 4.2','icon'=>'payments'],
                ['t'=>'Renúncias de Receitas','v'=>'renuncias','code'=>'16.1 a 16.4','icon'=>'percent'],
                ['t'=>'Dívida Ativa','v'=>'divida-ativa','code'=>'3.3','icon'=>'warning'],
            ]],
            ['t'=>'Gestão de Recursos','v'=>'recursos','icon'=>'bar_chart','c'=>[
                ['t'=>'Planejamento e Contas','v'=>'planejamento','code'=>'11.1 a 11.10','icon'=>'assignment'],
                ['t'=>'Emendas Parlamentares','v'=>'emendas','code'=>'17.1 a 17.2','icon'=>'edit'],
            ]],
            ['t'=>'Contratos e Licitações','v'=>'contratos','icon'=>'article','c'=>[
                ['t'=>'Licitações e Contratos','v'=>'licitacoes','code'=>'8.1 a 9.4','icon'=>'description'],
                ['t'=>'Ordem Cronológica','v'=>'ordem-cronologica','code'=>'9.4','icon'=>'calendar_today'],
            ]],
            ['t'=>'Despesas com Pessoal','v'=>'pessoal','icon'=>'people','c'=>[
                ['t'=>'Diárias e Passagens','v'=>'diarias','code'=>'7.1 a 7.2','icon'=>'flight'],
                ['t'=>'Valores das Diárias','v'=>'valores-diarias','code'=>'7.2','icon'=>'calculate'],
            ]],
            ['t'=>'Cidadania e Acesso','v'=>'cidadania','icon'=>'people','c'=>[
                ['t'=>'SIC','v'=>'sic','code'=>'12.1 a 12.9','icon'=>'info'],
                ['t'=>'Ouvidorias','v'=>'ouvidorias','code'=>'14.1 a 14.3','icon'=>'mic'],
                ['t'=>'Perguntas Frequentes','v'=>'faq','code'=>'2.7','icon'=>'help'],
                ['t'=>'Carta de Serviços ao Cidadão','v'=>'carta-servicos','code'=>'','icon'=>'email'],
            ]],
            ['t'=>'Publicações Oficiais','v'=>'publicacoes','icon'=>'article','c'=>[
                ['t'=>'Diário Oficial','v'=>'diario-oficial','code'=>'Lei 4.965/1966','icon'=>'book'],
                ['t'=>'Transparência COVID-19','v'=>'transparencia-covid','code'=>'PRSE/MPF 12/2022','icon'=>'web'],
            ]],
            ['t'=>'Indicadores e Avaliação','v'=>'avaliacao','icon'=>'emoji_events','c'=>[
                ['t'=>'Radar da Transparência Pública','v'=>'radar-transparencia','code'=>'2.9','icon'=>'visibility'],
                ['t'=>'Dados Abertos','v'=>'dados-abertos','code'=>'CGU','icon'=>'storage'],
            ]],
            ['t'=>'Serviços Essenciais','v'=>'servicos','icon'=>'business','c'=>[
                ['t'=>'Obras','v'=>'obras','code'=>'10.1 a 10.4','icon'=>'construction'],
                ['t'=>'Saúde','v'=>'saude','code'=>'18.1 a 18.3','icon'=>'favorite'],
                ['t'=>'Educação','v'=>'educacao','code'=>'19.1 a 19.2','icon'=>'school'],
            ]],
        ];

        // Salva no cache
        $this->menu_cache = $menu_structure;
        $this->menu_cache_time = time();
        set_transient('atricon_menu_cache', $menu_structure, $this->cache_duration);

        return $menu_structure;
    }

    /**
     * Get content from CSV file with caching
     *
     * @return array Array of menu items with their content
     */
    private function get_menu_content_from_csv() {
        // Cache para conteúdo CSV
        $csv_cache = get_transient('atricon_csv_content');
        if ($csv_cache !== false) {
            return $csv_cache;
        }

        $csv_file = plugin_dir_path(dirname(__FILE__)) . 'assets/atricon_menu_conteudo_modelo.csv';
        $content = [];
        
        if (file_exists($csv_file) && ($handle = fopen($csv_file, "r")) !== FALSE) {
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
        
        // Cache por 1 hora
        set_transient('atricon_csv_content', $content, 3600);
        
        return $content;
    }

    /**
     * Cria todas as páginas do menu e retorna um mapa de caminho => ID
     */
    public function create_menu_pages_and_get_map() {
        $menu_items = $this->get_menu_items();
        $menu_content = $this->get_menu_content_from_csv();
        $map = [];
        
        // Primeiro, cria as páginas principais
        foreach ($menu_items as $item) {
            if ($item['v'] === 'busca-servico') continue;
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

        // Primeiro, garante que a página ATRICON existe
        $atricon_page = get_page_by_path('atricon');
        if (!$atricon_page) {
            $atricon_id = wp_insert_post([
                'post_title'    => 'ATRICON',
                'post_name'     => 'atricon',
                'post_content'  => '<h1>ATRICON</h1><p>Página principal do portal ATRICON.</p>',
                'post_status'   => 'publish',
                'post_type'     => 'page',
            ]);
            $atricon_page = get_post($atricon_id);
        }
        
        // Busca página pelo slug
        $page_args = [
            'name'        => $slug,
            'post_type'   => 'page',
            'post_status' => 'publish',
            'numberposts' => 1,
            'fields'      => 'ids',
        ];
        
        // Define o parent_id como o ID da página ATRICON
        $parent_id = $atricon_page->ID;
        
        $existing_pages = get_posts($page_args);
        if (!empty($existing_pages)) {
            $page_id = $existing_pages[0];
            // Atualiza o parent da página existente para ATRICON se necessário
            $existing_page = get_post($page_id);
            if ($existing_page->post_parent != $parent_id) {
                wp_update_post([
                    'ID' => $page_id,
                    'post_parent' => $parent_id
                ]);
            }
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

        foreach ($items as $item) {
            if ($item['v'] === 'busca-servico') continue;
            
            $parent_path = 'atricon/' . sanitize_title($item['v']);
            $page_id = isset($map[$parent_path]) ? $map[$parent_path] : 0;
            
            if ($page_id) {
                $menu_item_args = [
                    'menu-item-title'  => $item['t'],
                    'menu-item-object' => 'page',
                    'menu-item-object-id' => $page_id,
                    'menu-item-status' => 'publish',
                    'menu-item-type'   => 'post_type',
                    'menu-item-attr-title' => $item['icon'],
                ];
                
                $pid = wp_update_nav_menu_item($menu_id, 0, $menu_item_args);
                
                if (!empty($item['c'])) {
                    foreach ($item['c'] as $child) {
                        $child_path = 'atricon/' . sanitize_title($item['v']) . '/' . sanitize_title($child['v']);
                        $child_page_id = isset($map[$child_path]) ? $map[$child_path] : 0;
                        
                        if ($child_page_id) {
                            $child_menu_item_args = [
                                'menu-item-title'     => $child['t'] . ($child['code'] ? " ({$child['code']})" : ''),
                                'menu-item-object'    => 'page',
                                'menu-item-object-id' => $child_page_id,
                                'menu-item-parent-id' => $pid,
                                'menu-item-status'    => 'publish',
                                'menu-item-type'      => 'post_type',
                                'menu-item-attr-title' => $child['icon'],
                            ];
                            
                            wp_update_nav_menu_item($menu_id, 0, $child_menu_item_args);
                        }
                    }
                }
            }
        }
        
        // Associa o menu à localização do tema
        $locations = get_theme_mod('nav_menu_locations');
        $locations['atrcn-sidebar'] = $menu_id;
        set_theme_mod('nav_menu_locations', $locations);
        
        // Limpa cache após criar menu
        $this->clear_menu_cache();
    }

    /**
     * Add admin menu page
     */
    public function add_admin_menu() {
        add_menu_page(
            'ATRICON Menu',
            'ATRICON Menu',
            'manage_options',
            'atricon-menu',
            [$this, 'render_admin_page'],
            'dashicons-menu',
            30
        );
    }

    /**
     * Create pages from CSV content
     */
    public function create_pages_from_csv() {
        // Get CSV content
        $csv_content = $this->get_menu_content_from_csv();
        
        // First, check if ATRICON parent page exists
        $parent_page = get_page_by_path('atricon');
        $parent_id = 0;
        
        if (!$parent_page) {
            // Create parent page if it doesn't exist
            $parent_id = wp_insert_post([
                'post_title'    => 'ATRICON',
                'post_name'     => 'atricon',
                'post_content'  => '<h1>ATRICON</h1><p>Página principal do portal ATRICON.</p>',
                'post_status'   => 'publish',
                'post_type'     => 'page',
            ]);
        } else {
            $parent_id = $parent_page->ID;
        }
        
        $created = 0;
        $skipped = 0;
        
        foreach ($csv_content as $slug => $item) {
            // Sanitize the slug
            $page_slug = sanitize_title($slug);
            
            // Check if page exists
            $existing_page = get_page_by_path('atricon/' . $page_slug);
            
            if (!$existing_page) {
                // Create page
                $page_id = wp_insert_post([
                    'post_title'    => $slug,
                    'post_name'     => $page_slug,
                    'post_content'  => $item['content'],
                    'post_status'   => 'publish',
                    'post_type'     => 'page',
                    'post_parent'   => $parent_id,
                ]);
                
                if ($page_id && !is_wp_error($page_id)) {
                    $created++;
                }
            } else {
                $skipped++;
            }
        }
        
        return [
            'created' => $created,
            'skipped' => $skipped,
            'total' => count($csv_content)
        ];
    }

    /**
     * Render admin page
     */
    public function render_admin_page() {
        if (isset($_POST['atricon_recreate_menu']) && check_admin_referer('atricon_recreate_menu')) {
            $map = $this->create_menu_pages_and_get_map();
            $this->create_menu_with_pages($map);
            echo '<div class="notice notice-success"><p>Menu recriado com sucesso!</p></div>';
        }
        
        if (isset($_POST['atricon_clear_cache']) && check_admin_referer('atricon_clear_cache')) {
            $this->clear_menu_cache();
            delete_transient('atricon_csv_content');
            echo '<div class="notice notice-success"><p>Cache limpo com sucesso!</p></div>';
        }

        if (isset($_POST['atricon_create_pages']) && check_admin_referer('atricon_create_pages')) {
            $result = $this->create_pages_from_csv();
            echo '<div class="notice notice-success"><p>Páginas processadas com sucesso! Criadas: ' . $result['created'] . ', Ignoradas (já existem): ' . $result['skipped'] . ', Total: ' . $result['total'] . '</p></div>';
        }
        ?>
        <div class="wrap">
            <h1>ATRICON Menu</h1>
            <form method="post" action="">
                <?php wp_nonce_field('atricon_recreate_menu'); ?>
                <p>Clique no botão abaixo para recriar o menu ATRICON com todas as páginas necessárias.</p>
                <p class="submit">
                    <input type="submit" name="atricon_recreate_menu" class="button button-primary" value="Recriar Menu">
                </p>
            </form>
            
            <hr>
            
            <form method="post" action="">
                <?php wp_nonce_field('atricon_create_pages'); ?>
                <p>Criar páginas do CSV (pula páginas que já existem).</p>
                <p class="submit">
                    <input type="submit" name="atricon_create_pages" class="button button-primary" value="Criar Páginas">
                </p>
            </form>
            
            <hr>
            
            <form method="post" action="">
                <?php wp_nonce_field('atricon_clear_cache'); ?>
                <p>Limpar cache do menu e conteúdo CSV.</p>
                <p class="submit">
                    <input type="submit" name="atricon_clear_cache" class="button button-secondary" value="Limpar Cache">
                </p>
            </form>
        </div>
        <?php
    }
}
