    <?php
    /** * Plugin Name: ATRICON Sidebar Menu
     * Description: Menu lateral fixo para ATRICON com ícones, submenu, busca em tempo real dos itens do WordPress e comportamento configurável (apenas ícones ou sempre visível). Versão melhorada com design responsivo, UX otimizada e prevenção de cortes.
     * Version:     2.3
     * Author:      Hermes
     * Text Domain: atricon-sidebar-menu
     */

    // Função auxiliar para verificar se Menu Icons está ativo
    function atricon_menu_icons_active() {
        if (!function_exists('is_plugin_active')) {
            include_once(ABSPATH . 'wp-admin/includes/plugin.php');
        }
        return is_plugin_active('menu-icons/menu-icons.php');
    }

    // 0) Painel de Configurações: posição do menu
    add_action('admin_menu', function() {
        add_options_page(
            'ATRICON Sidebar',
            'ATRICON Sidebar',
            'manage_options',
            'atricon-sidebar-menu',
            function() {
                if (isset($_POST['atricon_sidebar_position'])) {
                    check_admin_referer('atricon_sidebar_save');
                    update_option('atricon_sidebar_position', $_POST['atricon_sidebar_position'] === 'right' ? 'right' : 'left');
                    echo '<div class="updated"><p>Configuração salva!</p></div>';
                }
                
                if (isset($_POST['atricon_sidebar_behavior'])) {
                    check_admin_referer('atricon_sidebar_save');
                    update_option('atricon_sidebar_behavior', $_POST['atricon_sidebar_behavior'] === 'always_show' ? 'always_show' : 'icon_only');
                    echo '<div class="updated"><p>Comportamento do menu atualizado!</p></div>';
                }
                
                if (isset($_POST['atricon_update_icons']) && atricon_menu_icons_active()) {
                    check_admin_referer('atricon_sidebar_save');
                    atricon_update_menu_icons();
                    echo '<div class="updated"><p>Ícones atualizados com sucesso!</p></div>';
                }
                if (isset($_POST['atricon_reset_menu'])) {
                    check_admin_referer('atricon_sidebar_save');
                    atricon_reset_menu();
                    echo '<div class="updated"><p>Menu ATRICON resetado com sucesso!</p></div>';
                }
                
                if (isset($_POST['atricon_force_update_icons']) && atricon_menu_icons_active()) {
                    check_admin_referer('atricon_sidebar_save');
                    atricon_force_update_all_icons();
                    echo '<div class="updated"><p>Ícones forçados a atualizar com sucesso!</p></div>';
                }
                
                $pos = get_option('atricon_sidebar_position', 'left');
                $behavior = get_option('atricon_sidebar_behavior', 'icon_only');
                $behavior = get_option('atricon_sidebar_behavior', 'icon_only');
                ?>
                <div class="wrap">
                    <h1>ATRICON Sidebar - Configurações</h1>
                    <?php if (!atricon_menu_icons_active()): ?>
                    <div class="notice notice-warning">
                        <p><strong>Plugin Menu Icons necessário:</strong> Para o funcionamento completo do ATRICON Sidebar, instale e ative o plugin <a href="<?php echo admin_url('plugin-install.php?s=menu+icons&tab=search&type=term'); ?>" target="_blank">Menu Icons</a>.</p>
                        <p><em>Com o Menu Icons ativo, o menu ATRICON será criado automaticamente com ícones Dashicons já configurados para cada item!</em></p>
                    </div>
                    <?php endif; ?>
                    
                    <form method="post">
                        <?php wp_nonce_field('atricon_sidebar_save'); ?>
                        <table class="form-table">
                            <tr valign="top">
                                <th scope="row">Posição do menu lateral</th>
                                <td>
                                    <label><input type="radio" name="atricon_sidebar_position" value="left" <?php checked($pos, 'left'); ?>> Esquerda</label><br>
                                    <label><input type="radio" name="atricon_sidebar_position" value="right" <?php checked($pos, 'right'); ?>> Direita</label>
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row">Comportamento do menu</th>
                                <td>
                                    <label><input type="radio" name="atricon_sidebar_behavior" value="icon_only" <?php checked($behavior, 'icon_only'); ?>> Apenas ícones (expandir para ver títulos)</label><br>
                                    <label><input type="radio" name="atricon_sidebar_behavior" value="always_show" <?php checked($behavior, 'always_show'); ?>> Sempre mostrar ícones e títulos</label>
                                    <p class="description">No modo "apenas ícones", os títulos aparecerão apenas quando você passar o mouse sobre o menu.</p>
                                </td>
                            </tr>
                        </table>
                        <?php if (atricon_menu_icons_active()): ?>
                        <h3>Gerenciar Ícones</h3>
                        <p>O plugin Menu Icons está ativo. Você pode reconfigurar os ícones Dashicons do menu ATRICON automaticamente.</p>
                        <table class="form-table">                        <tr valign="top">
                                <th scope="row">Reconfigurar Ícones</th>
                                <td>
                                    <input type="submit" name="atricon_update_icons" class="button-secondary" value="Aplicar Ícones Dashicons Padrão">
                                    <p class="description">Clique para aplicar os ícones Dashicons padrão ao menu ATRICON. Isso não substituirá ícones personalizados já configurados.</p>
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row">Forçar Atualização</th>
                                <td>
                                    <input type="submit" name="atricon_force_update_icons" class="button-secondary" value="Forçar Atualização de Todos os Ícones">
                                    <p class="description">Use esta opção se os ícones não estão aparecendo corretamente no site. Isso irá sobrescrever TODOS os ícones com os padrões.</p>
                                </td>
                            </tr>
                        </table>
                        <?php endif; ?>
                        <h3>Gerenciar Menu</h3>
                        <p>Use as opções abaixo para gerenciar o menu ATRICON.</p>
                        <table class="form-table">
                            <tr valign="top">
                                <th scope="row">Resetar Menu</th>
                                <td>
                                    <input type="submit" name="atricon_reset_menu" class="button-secondary" value="Resetar Menu ATRICON" onclick="return confirm('Tem certeza que deseja resetar o menu ATRICON? Esta ação irá recriar o menu com as configurações padrão e não pode ser desfeita.');">
                                    <p class="description">Remove o menu ATRICON atual e recria com todas as configurações padrão, incluindo ícones se o Menu Icons estiver ativo.</p>
                                </td>
                            </tr>
                        </table>
                        <p class="submit"><input type="submit" class="button-primary" value="Salvar alterações"></p>
                    </form>
                </div>
                <?php
            }
        );
    });

    // 1) Registra localização
    add_action('init', function(){
        register_nav_menu('atrcn-sidebar','ATRICON Sidebar Menu');
    });

    // Função centralizada para definir os itens do menu
    function atricon_get_menu_items() {
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

    // Adiciona estilos CSS para o menu lateral
    add_action('wp_enqueue_scripts', function() {
        $behavior = get_option('atricon_sidebar_behavior', 'icon_only');
        $sidebar_pos = get_option('atricon_sidebar_position', 'left');

        $sidebar_width_normal = '250px';
        $sidebar_width_icon_only = '60px';
        $current_sidebar_width = ($behavior === 'icon_only') ? $sidebar_width_icon_only : $sidebar_width_normal;

        $css_main = <<<CSS
            body.admin-bar #wpcontent, 
            body.admin-bar #wpfooter {
                margin-left: 0 !important; /* Reseta margem padrão do WordPress se a barra de admin estiver visível */
                margin-right: 0 !important;
            }
    CSS;

        if ($sidebar_pos === 'left') {
            $css_main .= <<<CSS

            body:not(.atricon-sidebar-icon-only) #page, 
            body:not(.atricon-sidebar-icon-only) #wpcontent,
            body:not(.atricon-sidebar-icon-only) > *:not(#atricon-sidebar) {
                margin-left: {$sidebar_width_normal};
                margin-right: 0;
            }

            body.atricon-sidebar-icon-only #page,
            body.atricon-sidebar-icon-only #wpcontent,
            body.atricon-sidebar-icon-only > *:not(#atricon-sidebar) {
                margin-left: {$sidebar_width_icon_only};
                margin-right: 0;
            }
    CSS;
        } else { // right
            $css_main .= <<<CSS

            body:not(.atricon-sidebar-icon-only) #page, 
            body:not(.atricon-sidebar-icon-only) #wpcontent,
            body:not(.atricon-sidebar-icon-only) > *:not(#atricon-sidebar) {
                margin-right: {$sidebar_width_normal};
                margin-left: 0;
            }

            body.atricon-sidebar-icon-only #page,
            body.atricon-sidebar-icon-only #wpcontent,
            body.atricon-sidebar-icon-only > *:not(#atricon-sidebar) {
                margin-right: {$sidebar_width_icon_only};
                margin-left: 0;
            }
    CSS;
        }

        // Os estilos diretos do #atricon-sidebar foram removidos daqui
        // pois são gerenciados de forma mais completa no hook wp_head.
        // Este bloco foca principalmente nos ajustes de margem do corpo da página.

        wp_add_inline_style('dashicons', $css_main);
    });

    // Adiciona classes ao body para controlar o comportamento e posição do menu
    add_filter('body_class', function($classes) {
        $behavior = get_option('atricon_sidebar_behavior', 'icon_only');
        if ($behavior === 'icon_only') {
            $classes[] = 'atricon-sidebar-icon-only';
        }

        $pos = get_option('atricon_sidebar_position', 'left');
        if ($pos === 'right') {
            $classes[] = 'atricon-sidebar-right';
        }
        return $classes;
    });



    // 2) Na ativação, cria o menu e popula os itens
    register_activation_hook(__FILE__, function(){
        $name = 'ATRICON';
        if ( wp_get_nav_menu_object($name) ) return;
        $menu_id = wp_create_nav_menu($name);

        $items = atricon_get_menu_items();

        foreach($items as $i){
            $pid = wp_update_nav_menu_item($menu_id, 0, [
                'menu-item-title'  => $i['t'],
                'menu-item-url'    => '#'.$i['v'],
                'menu-item-status' => 'publish',
            ]);
            
            // Configura o ícone do Menu Icons se estiver ativo
            if (atricon_menu_icons_active() && !empty($i['icon'])) {
                update_post_meta($pid, 'menu-icons', [
                    'type' => 'dashicons',
                    'icon' => 'dashicons-' . $i['icon'],
                    'hide_label' => '',
                    'position' => 'before',
                    'vertical_align' => 'middle',
                    'font_size' => '1',
                    'svg_width' => '1',
                    'image_size' => 'thumbnail'
                ]);
            }
            
            if(!empty($i['c'])){
                foreach($i['c'] as $c){
                    $cid = wp_update_nav_menu_item($menu_id, 0, [
                        'menu-item-title'     => $c['t'].' '.($c['code']? "({$c['code']})":''),
                        'menu-item-url'       => '#'.$c['v'],
                        'menu-item-parent-id' => $pid,
                        'menu-item-status'    => 'publish',
                    ]);
                    
                    // Configura o ícone do submenu se estiver ativo
                    if (atricon_menu_icons_active() && !empty($c['icon'])) {
                        update_post_meta($cid, 'menu-icons', [
                            'type' => 'dashicons',
                            'icon' => 'dashicons-' . $c['icon'],
                            'hide_label' => '',
                            'position' => 'before',
                            'vertical_align' => 'middle',
                            'font_size' => '0.9',
                            'svg_width' => '1',
                            'image_size' => 'thumbnail'
                        ]);
                    }
                }
            }
        }

        // atribui à localização
        set_theme_mod('nav_menu_locations', array_merge(
            (array)get_theme_mod('nav_menu_locations'), ['atrcn-sidebar'=>$menu_id]
        ));
    });

    // 3) Walker customizado compatível com Menu Icons
    class ATRICON_Walker_Main extends Walker_Nav_Menu {
        function start_lvl(&$o,$d=0,$a=[]){ 
            $o .= "<ul class=\"atricon-submenu\">\n"; 
        }
        
        function end_lvl(&$o,$d=0,$a=[]){ 
            $o .= "</ul>\n"; 
        }      
        
        function start_el(&$o,$item,$d=0,$a=[],$id=0){
            // Pula o item de busca pois já foi renderizado no topo
            if ($item->url === '#busca-servico') {
                return;
            }
            
            $indent = ($d) ? str_repeat("\t", $d) : '';

            $classes = empty($item->classes) ? array() : (array) $item->classes;
            $classes[] = 'menu-item-' . $item->ID;
            $classes[] = 'atricon-item';
            
            // Adiciona classes específicas para submenu
            if ($item->menu_item_parent > 0) {
                $classes[] = 'atricon-subitem';
            } else {
                $classes[] = 'atricon-parent-item';
            }

            // Adiciona classe para itens que têm submenu
            if (in_array('menu-item-has-children', $classes)) {
                $classes[] = 'atricon-has-submenu';
            }

            $class_names = join(' ', apply_filters('nav_menu_css_class', array_filter($classes), $item, $a));
            $class_names = $class_names ? ' class="' . esc_attr($class_names) . '"' : '';

            $id = apply_filters('nav_menu_item_id', 'menu-item-'. $item->ID, $item, $a);
            $id = $id ? ' id="' . esc_attr($id) . '"' : '';

            $o .= $indent . '<li' . $id . $class_names .'>';

            $attributes  = ! empty($item->attr_title) ? ' title="'  . esc_attr($item->attr_title) .'"' : '';
            $attributes .= ! empty($item->target)     ? ' target="' . esc_attr($item->target     ) .'"' : '';
            $attributes .= ! empty($item->xfn)        ? ' rel="'    . esc_attr($item->xfn        ) .'"' : '';
            $attributes .= ! empty($item->url)        ? ' href="'   . esc_attr($item->url        ) .'"' : '';
            
            $attributes .= ' class="atricon-link"';

            $item_output = isset($a->before) ? $a->before : '';
            $item_output .= '<a' . $attributes . '>';
            
            // Deixa que o Menu Icons adicione o ícone se estiver ativo
            $title = apply_filters('the_title', $item->title, $item->ID);
            $item_output .= (isset($a->link_before) ? $a->link_before : '') . $title . (isset($a->link_after) ? $a->link_after : '');
            
            $item_output .= '</a>';
            $item_output .= isset($a->after) ? $a->after : '';

            $o .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $d, $a);
        }
        
        function end_el(&$o,$item,$d=0,$a=[]){ 
            // Só adiciona </li> se não for o item de busca
            if ($item->url !== '#busca-servico') {
                $o .= "</li>\n"; 
            }
        }
    }

    // 4) Injeta CSS inline compatível com Menu Icons
    add_action('wp_head', function(){
        $pos = get_option('atricon_sidebar_position', 'left');
        $behavior = get_option('atricon_sidebar_behavior', 'icon_only');
        $side = $pos === 'right' ? 'right' : 'left';
        $border = $side === 'right' ? 'border-left:1px solid #eee;border-right:none;' : 'border-right:1px solid #eee;border-left:none;';
        $side_css = $side === 'right' ? 'right:0;left:auto;' : 'left:0;right:auto;';
        
        // Largura baseada no comportamento
        $collapsed_width_val = ($behavior === 'always_show') ? 280 : 60;
        $expanded_width_val = ($behavior === 'always_show') ? 280 : 320;
        $collapsed_width = $collapsed_width_val . 'px';
        $expanded_width = $expanded_width_val . 'px';

        // Tablet widths
        $tablet_collapsed_width_val = ($behavior === 'always_show') ? 240 : 50;
        $tablet_expanded_width_val = 280;
        $tablet_collapsed_width = $tablet_collapsed_width_val . 'px';
        $tablet_expanded_width = $tablet_expanded_width_val . 'px';

        // Mobile width (when active)
        $mobile_width_val = 280;
        $mobile_width = $mobile_width_val . 'px';
        ?>
        <style>
        /* === SIDEBAR PRINCIPAL === */
        #atricon-sidebar {
            position: fixed;
            top: 0;
            <?php echo $side_css; ?>
            width: <?php echo $collapsed_width; ?>;
            height: 100vh;
            background: linear-gradient(180deg, #fff 0%, #fafbfc 100%);
            overflow-y: auto;
            overflow-x: visible; /* Permite submenus laterais */
            transition: all .3s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 1000;
            <?php echo $border; ?>
            box-shadow: <?php echo $side === 'right' ? '-4px 0 20px rgba(0,0,0,0.08)' : '4px 0 20px rgba(0,0,0,0.08)'; ?>;
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            scrollbar-width: thin;
            scrollbar-color: rgba(0,0,0,0.2) transparent;
            display: flex;
            flex-direction: column;
            box-sizing: border-box;
            border-top: 3px solid #2271b1;
        }
        
        /* Expansão no hover (apenas no modo icon_only e telas >= 1025px) */
        <?php if ($behavior === 'icon_only'): ?>
        @media (min-width: 1025px) {
            #atricon-sidebar:hover { width: <?php echo $expanded_width; ?>; }
        }
        <?php endif; ?>
        
        /* Scrollbar */
        #atricon-sidebar::-webkit-scrollbar,
        .atricon-search-results::-webkit-scrollbar {
            width: 4px;
        }
        #atricon-sidebar::-webkit-scrollbar-thumb,
        .atricon-search-results::-webkit-scrollbar-thumb {
            background-color: rgba(0,0,0,0.2);
            border-radius: 2px;
            transition: background-color 0.3s;
        }
        #atricon-sidebar::-webkit-scrollbar-thumb:hover,
        .atricon-search-results::-webkit-scrollbar-thumb:hover {
            background-color: rgba(0,0,0,0.3);
        }
        
        /* Responsividade */
        @media (max-width: 1024px) {
            #atricon-sidebar { width: <?php echo $behavior === 'always_show' ? '240px' : '50px'; ?>; }
            <?php if ($behavior === 'icon_only'): ?>
            /* Hover expandido removido para manter sidebar compacta e usar fly-out */
            <?php endif; ?>
        }
        @media (max-width: 768px) {
            #atricon-sidebar {
                transform: translateX(<?php echo $side === 'right' ? '100%' : '-100%'; ?>);
                width: 280px;
                transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }
            #atricon-sidebar.mobile-active { transform: translateX(0); }
            .atricon-mobile-toggle {
                display: block;
                position: fixed;
                top: 20px;
                <?php echo $side; ?>: 20px;
                z-index: 1001;
                background: #2271b1;
                color: white;
                border: none;
                border-radius: 50%;
                width: 50px;
                height: 50px;
                font-size: 20px;
                cursor: pointer;
                box-shadow: 0 2px 10px rgba(0,0,0,0.2);
                transition: all 0.3s;
            }
            .atricon-mobile-toggle:hover { background: #1e5a8a; transform: scale(1.1); }
            #atricon-sidebar .atricon-submenu {
                position: static !important;
                display: block !important;
                box-shadow: inset 2px 0 0 #2271b1;
                background: #f8fafc;
                border: none;
                border-radius: 0;
                margin-left: 20px;
            }
            #atricon-sidebar .atricon-submenu .atricon-link { padding: 6px 12px; font-size: 12px; }
        }
        @media (min-width: 769px) { .atricon-mobile-toggle { display: none; } }
        
        /* === MENU LISTA === */
        .atricon-menu {
            list-style: none;
            padding: 0;
            margin: 0;
            padding-bottom: 80px;
            flex: 1;
            overflow-y: auto;
            overflow-x: visible; /* Permite que submenus apareçam ao lado */
        }
        .atricon-item { position: relative; }
        /* === SUBMENU LATERAL === */
        .atricon-submenu {
            display: none;
            position: absolute;
            top: 0;
            <?php if ($side === 'right'): ?>
            right: 100%;
            margin-right: 8px;
            <?php else: ?>
            left: 100%;
            margin-left: 8px;
            <?php endif; ?>
            white-space: nowrap;
            background-color: #fff;
            border: 1px solid #ddd;
            padding: 0;
            margin-top: 0;
            list-style: none;
            min-width: 240px;
            max-width: 350px;
            max-height: 80vh;
            overflow-y: auto;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            z-index: 10020;
            border-radius: 8px;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }
        
        /* Garantir que o container pai permita overflow */
        .atricon-menu, 
        .atricon-item {
            overflow: visible !important;
        }
        
        /* Mostrar submenu no hover */
        .atricon-item:hover > .atricon-submenu {
            display: block;
        }
        .atricon-submenu .atricon-item { margin: 0; padding: 0; }
        .atricon-submenu .atricon-link {
            padding: 8px 16px;
            font-size: 13px;
            border: none;
            margin: 0;
            border-radius: 0;
            display: flex;
            align-items: center;
            color: #374151;
            text-decoration: none;
            transition: all 0.2s ease;
        }
        .atricon-submenu .atricon-link:hover {
            background: linear-gradient(135deg, rgba(34, 113, 177, 0.08) 0%, rgba(34, 113, 177, 0.03) 100%);
            color: #2271b1;
        }
        
        /* === LINKS DO MENU === */
        .atricon-link {
            display: flex;
            align-items: center;
            padding: 8px 10px;
            color: #374151;
            text-decoration: none;
            font-family: -apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif;
            font-size: 14px;
            font-weight: 500;
            border-bottom: 1px solid rgba(0,0,0,0.03);
            transition: all .3s cubic-bezier(0.4, 0, 0.2, 1);
            min-height: 32px;
            position: relative;
            backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(5px);
        }
        .atricon-link:hover {
            background: linear-gradient(135deg, rgba(34, 113, 177, 0.08) 0%, rgba(34, 113, 177, 0.03) 100%);
            color: #2271b1;
            transform: translateX(<?php echo $side === 'right' ? '-3px' : '3px'; ?>);
            box-shadow: inset <?php echo $side === 'right' ? '-3px' : '3px'; ?> 0 0 #2271b1;
            border-radius: 8px;
            margin: 0 8px;
        }
        .atricon-link:active {
            transform: translateX(0);
            background: linear-gradient(135deg, rgba(34, 113, 177, 0.12) 0%, rgba(34, 113, 177, 0.06) 100%);
            box-shadow: inset <?php echo $side === 'right' ? '-4px' : '4px'; ?> 0 0 #1e5a8a;
        }
        
        /* === ÍCONES UNIFICADOS === */
        .atricon-icon, ._mi._before {
            width: 24px;
            height: 24px;
            text-align: center;
            margin-right: 0;
            font-size: 18px !important;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6b7280;
            min-width: 24px;
            min-height: 24px;
            background: none !important;
            box-shadow: none !important;
            border-radius: 0 !important;
            position: relative;
            overflow: hidden;
            transition: all .3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .atricon-link:hover .atricon-icon, .atricon-link:hover ._mi._before {
            color: #2271b1;
            background: none !important;
            transform: scale(1.1) rotate(2deg);
        }
        <?php if ($behavior === 'icon_only'): ?>
        #atricon-sidebar:hover .atricon-icon, #atricon-sidebar:hover ._mi._before { margin-right: 12px; }
        <?php else: ?>
        .atricon-icon, ._mi._before { margin-right: 12px; }
        <?php endif; ?>
        
        /* === VISIBILIDADE DE TEXTOS === */
        <?php if ($behavior === 'icon_only'): ?>
        #atricon-sidebar .atricon-link .menu-icon-label,
        #atricon-sidebar .atricon-link span:not([class*="dashicons"]):not([class*="_mi"]),
        #atricon-sidebar .atricon-label,
        .atricon-brand-text {
            opacity: 0;
            width: 0;
            overflow: hidden;
            white-space: nowrap;
            transition: all .3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        #atricon-sidebar:hover .atricon-link .menu-icon-label,
        #atricon-sidebar:hover .atricon-link span:not([class*="dashicons"]):not([class*="_mi"]),
        #atricon-sidebar:hover .atricon-label,
        #atricon-sidebar:hover .atricon-brand-text {
            opacity: 1;
            width: auto;
        }
        #atricon-sidebar:not(:hover) .atricon-link { justify-content: center; padding: 16px 12px; }
        <?php else: ?>
        #atricon-sidebar .atricon-link .menu-icon-label,
        #atricon-sidebar .atricon-link span:not([class*="dashicons"]):not([class*="_mi"]),
        #atricon-sidebar .atricon-label,
        .atricon-brand-text {
            opacity: 1;
            width: auto;
            transition: all .3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        <?php endif; ?>
        /* Garante que o texto do submenu apareça quando o submenu está visível */
        .atricon-item:hover > .atricon-submenu .atricon-link .menu-icon-label,
        .atricon-item:hover > .atricon-submenu .atricon-link span:not([class*="dashicons"]):not([class*="_mi"]),
        .atricon-item:hover > .atricon-submenu .atricon-link .atricon-label {
          opacity: 1 !important;
          width: auto !important;
          overflow: visible !important;
        }
        /* Garante que o texto do item pai apareça no hover */
        .atricon-item:hover > .atricon-link .menu-icon-label,
        .atricon-item:hover > .atricon-link span:not([class*="dashicons"]):not([class*="_mi"]),
        .atricon-item:hover > .atricon-link .atricon-label {
          opacity: 1 !important;
          width: auto !important;
          overflow: visible !important;
        }
        
        /* === FOOTER === */
        .atricon-footer {
            position: fixed;
            bottom: 0;
            <?php echo $side; ?>: 0;
            width: <?php echo $collapsed_width; ?>;
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            background: linear-gradient(135deg, #fff 0%, #f8fafc 100%);
            border-top: 1px solid rgba(34, 113, 177, 0.1);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            opacity: 0.95;
            transition: all .3s cubic-bezier(0.4, 0, 0.2, 1);
            padding: 0 16px;
            box-shadow: 0 -4px 20px rgba(0,0,0,0.08);
            z-index: 11;
            cursor: pointer;
        }
        .atricon-footer:hover {
            opacity: 1;
            background: linear-gradient(135deg, #fff 0%, #f1f5f9 100%);
            transform: translateY(-1px);
            box-shadow: 0 -6px 25px rgba(0,0,0,0.12);
        }
        .atricon-logo-img {
            max-width: 36px;
            max-height: 36px;
            object-fit: contain;
            flex-shrink: 0;
            filter: drop-shadow(0 1px 2px rgba(0,0,0,0.1));
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .atricon-footer:hover .atricon-logo-img { transform: scale(1.1); }
        
        /* === BUSCA === */
        .atricon-search-container {
            padding: 16px 12px;
            border-bottom: 1px solid rgba(0,0,0,0.08);
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            margin: 0;
            position: relative;
            z-index: 10;
            box-sizing: border-box;
            width: 100%;
            flex-shrink: 0;
            display: block !important;
            visibility: visible !important;
        }
        .atricon-search-wrapper {
            position: relative;
            display: flex !important;
            align-items: center;
            background: #fff;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            width: 100%;
            min-height: 40px;
            height: 40px;
            box-sizing: border-box;
            visibility: visible !important;
        }
        .atricon-search-wrapper:focus-within {
            border-color: #2271b1;
            box-shadow: 0 0 0 2px rgba(34, 113, 177, 0.2), 0 2px 4px rgba(0,0,0,0.1);
        }
        .atricon-search-icon {
            padding: 10px;
            color: #6b7280;
            font-size: 16px !important;
            flex-shrink: 0;
            width: 16px;
            height: 16px;
            display: flex !important;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s;
        }
        .atricon-search-wrapper:focus-within .atricon-search-icon { color: #2271b1; }
        .atricon-search-input {
            border: none;
            background: transparent;
            padding: 10px 12px 10px 4px;
            font-size: 14px;
            width: 100%;
            height: 40px;
            line-height: 20px;
            outline: none;
            font-family: -apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif;
            color: #374151;
            font-weight: 400;
            box-sizing: border-box;
            flex: 1;
            min-width: 0;
            margin: 0;
            border-radius: 0;
            display: block !important;
            visibility: visible !important;
        }
        .atricon-search-input::placeholder { color: #9ca3af; font-style: normal; font-weight: 400; }
        <?php if ($behavior === 'icon_only'): ?>
        #atricon-sidebar:not(:hover) .atricon-search-container { padding: 10px; background: rgba(248, 250, 252, 0.8); border-bottom: 1px solid rgba(0,0,0,0.05); }
        #atricon-sidebar:not(:hover) .atricon-search-wrapper { width: 40px; height: 40px; border-radius: 50%; justify-content: center; }
        #atricon-sidebar:not(:hover) .atricon-search-icon { padding: 0; margin: 0; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); color: #2271b1; }
        #atricon-sidebar:not(:hover) .atricon-search-input { position: absolute; left: -9999px; opacity: 0; width: 1px; height: 1px; pointer-events: none; }
        #atricon-sidebar:hover .atricon-search-container { padding: 16px 12px; background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%); }
        #atricon-sidebar:hover .atricon-search-wrapper { width: 100%; height: 40px; border-radius: 8px; justify-content: flex-start; }
        #atricon-sidebar:hover .atricon-search-icon { position: static; transform: none; padding: 10px; color: #6b7280; }
        #atricon-sidebar:hover .atricon-search-input { position: static; left: auto; opacity: 1; width: 100%; height: 40px; pointer-events: auto; flex: 1; }
        <?php endif; ?>
        
        /* === RESULTADOS DE BUSCA === */
        .atricon-search-results {
            position: relative;
            background: #fff;
            border: 1px solid #d1d5db;
            border-top: none;
            border-radius: 0 0 8px 8px;
            max-height: 250px;
            overflow-y: auto;
            z-index: 1001;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin: 0;
            width: 100%;
            box-sizing: border-box;
            contain: layout;
            scrollbar-width: thin;
            scrollbar-color: rgba(0,0,0,0.2) transparent;
        }
        .atricon-search-result-item {
            padding: 10px 12px;
            border-bottom: 1px solid rgba(0,0,0,0.05);
            cursor: pointer;
            font-size: 12px;
            color: #374151;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            font-weight: 400;
            position: relative;
            line-height: 1.4;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }
        .atricon-search-result-item:hover,
        .atricon-search-result-item.atricon-active {
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
            color: #1d4ed8;
            padding-left: 16px;
        }
        .atricon-search-result-item:last-child { border-bottom: none; border-radius: 0 0 8px 8px; }
        .atricon-search-highlight {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            font-weight: 600;
            padding: 1px 3px;
            border-radius: 3px;
            color: #92400e;
        }
        .atricon-ajax-result {
            border-left: 3px solid #10b981;
            background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
            position: relative;
        }
        .atricon-ajax-result::after {
            content: 'AJAX';
            position: absolute;
            top: 2px;
            right: 6px;
            font-size: 9px;
            color: #10b981;
            font-weight: 600;
            opacity: 0.7;
        }
        .atricon-search-no-results {
            padding: 16px 12px;
            text-align: center;
            color: #6b7280;
            font-style: italic;
            font-size: 12px;
            background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%);
        }
        .atricon-search-loading {
            padding: 16px 12px;
            text-align: center;
            color: #2271b1;
            font-size: 12px;
            font-weight: 500;
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
        }
        .atricon-search-loading::before {
            content: '';
            display: inline-block;
            width: 14px;
            height: 14px;
            margin-right: 8px;
            border: 2px solid rgba(34, 113, 177, 0.3);
            border-top: 2px solid #2271b1;
            border-radius: 50%;
            animation: atricon-spin 1s linear infinite;
        }
        <?php if ($behavior === 'icon_only'): ?>
        #atricon-sidebar:not(:hover) .atricon-search-results { display: none !important; }
        #atricon-sidebar:hover .atricon-search-results { margin-top: 0; border-radius: 0 0 8px 8px; }
        <?php endif; ?>
        
        @keyframes atricon-spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        </style>
        <?php
    });


    // Garantir que o Menu Icons seja carregado corretamente no frontend
    add_action('wp_enqueue_scripts', function(){
        // Garante que o jQuery está enfileirado antes de qualquer script do plugin
        wp_enqueue_script('jquery');
        // Se Menu Icons está ativo, garante que seus estilos sejam carregados
        if (atricon_menu_icons_active() && function_exists('menu_icons')) {
            wp_enqueue_style('menu-icons');
        }
        // Fallback: se jQuery não estiver carregado, carrega do CDN
        ?>
        <script type="text/javascript">
        if (typeof window.jQuery === 'undefined') {
            var script = document.createElement('script');
            script.src = 'https://code.jquery.com/jquery-3.6.0.min.js';
            script.onload = function() {
                if (window.jQuery) {
                    jQuery(document).ready(function($){
                        // Re-execute any plugin JS that depends on jQuery here if needed
                    });
                }
            };
            document.head.appendChild(script);
        }
        </script>
        <?php
    });

    // Hook para inicializar Menu Icons no nosso menu se necessário
    add_action('wp_footer', function(){
        if (atricon_menu_icons_active() && function_exists('menu_icons')) {
            ?>
            <script>
            jQuery(document).ready(function($) {
                // === INICIALIZAÇÃO E COMPATIBILIDADE ===
                // Força a aplicação dos ícones se necessário
                if (typeof MenuIcons !== 'undefined') {
                    $('#atricon-sidebar').trigger('menu-icons-ready');
                }
                
                // === CONTROLE MOBILE ===
                // Adiciona botão toggle para mobile
                function addMobileToggle() {
                    if ($(window).width() <= 768 && !$('.atricon-mobile-toggle').length) {
                        $('body').prepend('<button class="atricon-mobile-toggle" aria-label="Toggle Menu"><i class="dashicons dashicons-menu"></i></button>');
                    }
                }
                
                // Controla sidebar mobile
                $(document).on('click', '.atricon-mobile-toggle', function() {
                    $('#atricon-sidebar').toggleClass('mobile-active');
                    $(this).find('i').toggleClass('dashicons-menu dashicons-no-alt');
                });
                
                // Fecha sidebar ao clicar fora (mobile)
                $(document).on('click', function(e) {
                    if ($(window).width() <= 768 && 
                        !$(e.target).closest('#atricon-sidebar, .atricon-mobile-toggle').length &&
                        $('#atricon-sidebar').hasClass('mobile-active')) {
                        $('#atricon-sidebar').removeClass('mobile-active');
                        $('.atricon-mobile-toggle i').removeClass('dashicons-no-alt').addClass('dashicons-menu');
                    }
                });
                
                // === GERENCIAMENTO DE SUBMENUS ===
                // Os submenus agora usam posicionamento CSS puro (position: absolute)
                // e aparecem automaticamente ao lado do sidebar no hover
                
                // === MELHORIAS NA ACESSIBILIDADE ===
                // Navegação por teclado
                $('#atricon-sidebar .atricon-link').on('keydown', function(e) {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        $(this).click();
                    } else if (e.key === 'ArrowDown') {
                        e.preventDefault();
                        $(this).closest('.atricon-item').next().find('.atricon-link').focus();
                    } else if (e.key === 'ArrowUp') {
                        e.preventDefault();
                        $(this).closest('.atricon-item').prev().find('.atricon-link').focus();
                    }
                });
                
                // === BUSCA MELHORADA ===
                var searchTimeout;
                var allMenuItems = [];
                var isSearching = false;
                
                // Coleta todos os itens do menu para busca local
                function collectMenuItems() {
                    allMenuItems = [];
                    $('#atricon-sidebar .atricon-link').each(function() {
                        var $link = $(this);
                        var text = $link.text().trim();
                        var href = $link.attr('href');
                        if (text && href && href !== '#busca-servico') {
                            allMenuItems.push({
                                text: text,
                                href: href,
                                element: $link.closest('.atricon-item')
                            });
                        }
                    });
                }
                
                // Função principal de busca
                function performSearch(query) {
                    var $results = $('#atricon-search-results');
                    
                    if (!query || query.length < 2) {
                        $results.hide().empty();
                        showAllMenuItems();
                        isSearching = false;
                        return;
                    }
                    
                    isSearching = true;
                    
                    // Busca local primeiro (mais rápida)
                    var localMatches = allMenuItems.filter(function(item) {
                        return item.text.toLowerCase().indexOf(query.toLowerCase()) !== -1;
                    });
                    
                    $results.empty();
                    
                    if (localMatches.length > 0) {
                        localMatches.forEach(function(match) {
                            var highlightedText = highlightMatch(match.text, query);
                            var $resultItem = $('<div class="atricon-search-result-item">' + highlightedText + '</div>');
                            
                            $resultItem.on('click', function() {
                                var originalLink = match.element.find('.atricon-link');
                                if (originalLink.length && originalLink.attr('href') !== '#') {
                                    window.location.href = originalLink.attr('href');
                                }
                                clearSearch();
                            });
                            
                            $results.append($resultItem);
                        });
                        
                        // Oculta itens que não correspondem à busca
                        hideNonMatchingItems(localMatches);
                    } else {
                        $results.html('<div class="atricon-search-no-results">Nenhum serviço encontrado</div>');
                        hideAllMenuItems();
                    }
                    
                    $results.show();
                    
                    // Busca AJAX como complemento (se configurada)
                    performAjaxSearch(query);
                }
                
                // Busca AJAX complementar
                function performAjaxSearch(query) {
                    $.post(atricon_ajax.ajax_url, {
                        action: 'atricon_search_menu',
                        nonce: atricon_ajax.nonce,
                        query: query
                    }, function(response) {
                        // Adiciona resultados AJAX se diferentes dos locais
                        if (response.success && response.data.length > 0) {
                            var ajaxMatches = response.data;
                            var currentResults = $('#atricon-search-results .atricon-search-result-item');
                            
                            ajaxMatches.forEach(function(item) {
                                var alreadyExists = false;
                                currentResults.each(function() {
                                    if ($(this).text().toLowerCase().indexOf(item.title.toLowerCase()) !== -1) {
                                        alreadyExists = true;
                                        return false;
                                    }
                                });
                                
                                if (!alreadyExists) {
                                    var highlightedText = highlightMatch(item.title, query);
                                    var $resultItem = $('<div class="atricon-search-result-item atricon-ajax-result">' + highlightedText + '</div>');
                                    
                                    $resultItem.on('click', function() {
                                        if (item.url && item.url !== '#') {
                                            window.location.href = item.url;
                                        }
                                        clearSearch();
                                    });
                                    
                                    $('#atricon-search-results').append($resultItem);
                                }
                            });
                        }
                    }).fail(function() {
                        // Silently fail - local search already working
                    });
                }            
                // Limpa a busca
                function clearSearch() {
                    $('#atricon-search-input').val('').blur();
                    $('#atricon-search-results').hide().empty();
                    showAllMenuItems();
                    isSearching = false;
                }
                
                // Destaca o texto encontrado
                function highlightMatch(text, query) {
                    var regex = new RegExp('(' + escapeRegex(query) + ')', 'gi');
                    return text.replace(regex, '<span class="atricon-search-highlight">$1</span>');
                }
                
                // Escapa caracteres especiais para regex
                function escapeRegex(string) {
                    return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
                }
                
                // Esconde itens que não correspondem à busca
                function hideNonMatchingItems(matches) {
                    var matchHrefs = matches.map(function(m) { return m.href; });
                    $('#atricon-sidebar .atricon-item').each(function() {
                        var $item = $(this);
                        var $link = $item.find('.atricon-link');
                        var href = $link.attr('href');
                        
                        if (href && matchHrefs.indexOf(href) === -1) {
                            $item.hide();
                        } else if (matchHrefs.indexOf(href) !== -1) {
                            $item.show();
                        }
                    });
                }
                
                // Esconde todos os itens do menu
                function hideAllMenuItems() {
                    $('#atricon-sidebar .atricon-item').hide();
                }
                
                // Mostra todos os itens do menu
                function showAllMenuItems() {
                    $('#atricon-sidebar .atricon-item').show();
                }            
                // Event listeners para busca
                $('#atricon-search-input').on('input', function() {
                    var query = $(this).val().trim();
                    
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(function() {
                        performSearch(query);
                    }, 200); // Debounce reduzido para resposta mais rápida
                });
                
                // Suporte para navegação por teclado
                $('#atricon-search-input').on('keydown', function(e) {
                    var $results = $('#atricon-search-results');
                    var $items = $results.find('.atricon-search-result-item');
                    var $current = $items.filter('.atricon-active');
                    
                    switch(e.key) {
                        case 'Escape':
                            clearSearch();
                            break;
                        case 'ArrowDown':
                            e.preventDefault();
                            if ($current.length === 0) {
                                $items.first().addClass('atricon-active');
                            } else {
                                $current.removeClass('atricon-active');
                                var $next = $current.next('.atricon-search-result-item');
                                if ($next.length === 0) $next = $items.first();
                                $next.addClass('atricon-active');
                            }
                            break;
                        case 'ArrowUp':
                            e.preventDefault();
                            if ($current.length === 0) {
                                $items.last().addClass('atricon-active');
                            } else {
                                $current.removeClass('atricon-active');
                                var $prev = $current.prev('.atricon-search-result-item');
                                if ($prev.length === 0) $prev = $items.last();
                                $prev.addClass('atricon-active');
                            }
                            break;
                        case 'Enter':
                            e.preventDefault();
                            if ($current.length > 0) {
                                $current.click();
                            }
                            break;
                    }
                });
                
                // Fecha resultados quando clica fora
                $(document).on('click', function(e) {
                    if (!$(e.target).closest('.atricon-search-container').length) {
                        if (isSearching && $('#atricon-search-input').val()) {
                            $('#atricon-search-results').hide();
                        } else {
                            clearSearch();
                        }
                    }
                });
                
                // Reabre resultados quando foca no input
                $('#atricon-search-input').on('focus', function() {
                    var query = $(this).val().trim();
                    if (query.length >= 2) {
                        $('#atricon-search-results').show();
                    }
                });            
                // Inicializa coleta de itens e funcionalidades
                collectMenuItems();
                addMobileToggle();
                
                // === EVENTOS DE REDIMENSIONAMENTO E OTIMIZAÇÕES ===
                $(window).on('resize', throttle(function() {
                    addMobileToggle();
                    if ($(window).width() > 768) {
                        $('#atricon-sidebar').removeClass('mobile-active');
                        $('.atricon-mobile-toggle').remove();
                    }
                }, 250));
                
                // === SMOOTH SCROLL E NAVEGAÇÃO ===
                $('#atricon-sidebar .atricon-link[href^="#"]').on('click', function(e) {
                    const target = $(this).attr('href');
                    if (target && target !== '#' && target !== '#busca-servico') {
                        const targetElement = document.querySelector(target);
                        if (targetElement) {
                            e.preventDefault();
                            targetElement.scrollIntoView({ behavior: 'smooth', block: 'start' });
                            
                            // Fecha sidebar mobile
                            if ($(window).width() <= 768) {
                                $('#atricon-sidebar').removeClass('mobile-active');
                                $('.atricon-mobile-toggle i').removeClass('dashicons-no-alt').addClass('dashicons-menu');
                            }
                        }
                    }
                });
                
                // Função throttle para performance
                function throttle(func, wait) {
                    let timeout;
                    return function executedFunction(...args) {
                        const later = () => { clearTimeout(timeout); func(...args); };
                        clearTimeout(timeout);
                        timeout = setTimeout(later, wait);
                    };
                }
                
                console.log('ATRICON Sidebar inicializado com sucesso!');
                
                // === BUSCA LOCAL DIRETA NO MENU ===
                $('#atricon-search-input').on('input', function() {
                    var query = $(this).val().trim().toLowerCase();
                    if (!query) {
                        // Mostra tudo se busca vazia
                        $('#atricon-sidebar .atricon-item').show();
                        $('#atricon-sidebar .atricon-submenu').show();
                        return;
                    }
                    // Esconde todos os itens inicialmente
                    $('#atricon-sidebar .atricon-item').hide();
                    $('#atricon-sidebar .atricon-submenu').hide();
                    // Filtra principais e subitens
                    $('#atricon-sidebar .atricon-item').each(function() {
                        var $item = $(this);
                        var $link = $item.children('.atricon-link');
                        var text = $link.text().toLowerCase();
                        var found = false;
                        // Verifica se o item principal corresponde
                        if (text.indexOf(query) !== -1) {
                            $item.show();
                            found = true;
                        }
                        // Verifica subitens
                        var $submenu = $item.children('.atricon-submenu');
                        if ($submenu.length) {
                            var subFound = false;
                            $submenu.children('.atricon-item').each(function() {
                                var $subitem = $(this);
                                var $sublink = $subitem.children('.atricon-link');
                                var subtext = $sublink.text().toLowerCase();
                                if (subtext.indexOf(query) !== -1) {
                                    $subitem.show();
                                    subFound = true;
                                } else {
                                    $subitem.hide();
                                }
                            });
                            if (subFound) {
                                $item.show();
                                $submenu.show();
                            }
                        }
                    });
                });
            });
            </script>
            <?php
        } else {
            ?>
            <script>
            jQuery(document).ready(function($) {
                // Comportamento para quando Menu Icons não está ativo
                // (Removido: controle de submenus via JS, agora é só via CSS)
                // === BUSCA LOCAL DIRETA NO MENU ===
                $('#atricon-search-input').on('input', function() {
                    var query = $(this).val().trim().toLowerCase();
                    if (!query) {
                        $('#atricon-sidebar .atricon-item').show();
                        $('#atricon-sidebar .atricon-submenu').show();
                        return;
                    }
                    $('#atricon-sidebar .atricon-item').hide();
                    $('#atricon-sidebar .atricon-submenu').hide();
                    $('#atricon-sidebar .atricon-item').each(function() {
                        var $item = $(this);
                        var $link = $item.children('.atricon-link');
                        var text = $link.text().toLowerCase();
                        var found = false;
                        if (text.indexOf(query) !== -1) {
                            $item.show();
                            found = true;
                        }
                        var $submenu = $item.children('.atricon-submenu');
                        if ($submenu.length) {
                            var subFound = false;
                            $submenu.children('.atricon-item').each(function() {
                                var $subitem = $(this);
                                var $sublink = $subitem.children('.atricon-link');
                                var subtext = $sublink.text().toLowerCase();
                                if (subtext.indexOf(query) !== -1) {
                                    $subitem.show();
                                    subFound = true;
                                } else {
                                    $subitem.hide();
                                }
                            });
                            if (subFound) {
                                $item.show();
                                $submenu.show();
                            }
                        }
                    });
                });
            });
            </script>
            <?php
        }
    });

    // Garantir que os dashicons, CSS e JS do menu sejam carregados apenas no frontend
    if (!is_admin()) {
        add_action('wp_enqueue_scripts', function() {
            wp_enqueue_style('dashicons');
            wp_enqueue_style('atricon-material-icons', 'https://fonts.googleapis.com/icon?family=Material+Icons', [], null);
            wp_enqueue_style('atricon-roboto', 'https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap', [], null);
            wp_enqueue_style('atricon-sidebar', plugin_dir_url(__FILE__) . 'includes/sidebar.css', [], null);
            wp_enqueue_style('atricon-sidebar-responsive', plugin_dir_url(__FILE__) . 'includes/sidebar-responsive.css', ['atricon-sidebar'], null);
            wp_enqueue_script('atricon-sidebar', plugin_dir_url(__FILE__) . 'includes/sidebar.js', ['jquery'], null, true);
        });
        add_action('wp_body_open', function() {
            $logo_url = plugin_dir_url(__FILE__) . 'logo.png';
            ?>
            <div class="container" id="menu-container">
                <aside class="sidebar" id="main-sidebar">
                    <div class="search-box">
                        <div style="position: relative;">
                            <input type="text" placeholder="Buscar no menu..." id="menu-search">
                            <i class="material-icons" id="clear-search" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer; display: none; color: #666;">close</i>
                        </div>
                    </div>
                    <ul class="menu" id="menu-list"></ul>
                    <div class="sidebar-footer">
                        <img src="<?php echo esc_url($logo_url); ?>" alt="ATRICON Logo" class="sidebar-logo" />
                        <span class="sidebar-brand-text">ATRICON</span>
                    </div>
                </aside>
                <aside class="submenu-sidebar" id="submenu">
                    <h2 id="submenu-title">Título</h2>
                    <div class="submenu" id="submenu-content"></div>
                </aside>
            </div>
            <main>
                <h1>Bem-vindo</h1>
                <p>Selecione um item no menu lateral.</p>
            </main>
            <!-- CSS do rodapé/brand movido para sidebar.css para clean architecture -->
            <?php
        });
    }

    // Função para reconfigurar ícones quando Menu Icons for ativado
    function atricon_update_menu_icons() {
        if (!atricon_menu_icons_active()) return;
        
        // Limpa meta data antiga primeiro
        atricon_cleanup_old_meta();
        
        $menu = wp_get_nav_menu_object('ATRICON');
        if (!$menu) return;
        
        $menu_items = wp_get_nav_menu_items($menu->term_id);
        if (!$menu_items) return;
        
        // Usa o mesmo array de itens centralizado
        $items = atricon_get_menu_items();
        $icon_map = atricon_build_icon_map($items);
        
        foreach ($menu_items as $item) {
            if (isset($icon_map[$item->url])) {
                $existing_meta = get_post_meta($item->ID, 'menu-icons', true);
                
                // Força a atualização do ícone mesmo se já existir (para corrigir problemas)
                $font_size = $item->menu_item_parent == 0 ? '1' : '0.9';
                $icon_data = [
                    'type' => 'dashicons',
                    'icon' => 'dashicons-' . $icon_map[$item->url],
                    'hide_label' => '',
                    'position' => 'before',
                    'vertical_align' => 'middle',
                    'font_size' => $font_size,
                    'svg_width' => '1',
                    'image_size' => 'thumbnail'
                ];
                
                // Remove o meta antigo e adiciona o novo
                delete_post_meta($item->ID, 'menu-icons');
                update_post_meta($item->ID, 'menu-icons', $icon_data);
                
                // Log para debug
                error_log("ATRICON: Ícone atualizado para {$item->title}: " . $icon_map[$item->url]);
            }
        }
    }

    // Função para forçar atualização de TODOS os ícones (sobrescreve existentes)
    function atricon_force_update_all_icons() {
        if (!atricon_menu_icons_active()) return;
        
        $menu = wp_get_nav_menu_object('ATRICON');
        if (!$menu) return;
        
        $menu_items = wp_get_nav_menu_items($menu->term_id);
        if (!$menu_items) return;
        
        $items = atricon_get_menu_items();
        $icon_map = atricon_build_icon_map($items);
        
        foreach ($menu_items as $item) {
            if (isset($icon_map[$item->url])) {
                $font_size = $item->menu_item_parent == 0 ? '1' : '0.9';
                $icon_data = [
                    'type' => 'dashicons',
                    'icon' => 'dashicons-' . $icon_map[$item->url],
                    'hide_label' => '',
                    'position' => 'before',
                    'vertical_align' => 'middle',
                    'font_size' => $font_size,
                    'svg_width' => '1',
                    'image_size' => 'thumbnail'
                ];
                
                // Remove completamente e adiciona novamente
                delete_post_meta($item->ID, 'menu-icons');
                add_post_meta($item->ID, 'menu-icons', $icon_data);
                
                error_log("ATRICON FORCE: Ícone forçado para {$item->title}: dashicons-" . $icon_map[$item->url]);
            }
        }
    }

    // Função auxiliar para construir o mapa de ícones baseado no array de itens
    function atricon_build_icon_map($items) {
        $map = [];
        foreach ($items as $item) {
            $map['#' . $item['v']] = $item['icon'];
            if (!empty($item['c'])) {
                foreach ($item['c'] as $child) {
                    $map['#' . $child['v']] = $child['icon'];
                }
            }
        }
        return $map;
    }

    // Hook para verificar quando plugins são ativados
    add_action('activated_plugin', function($plugin) {
        if ($plugin === 'menu-icons/menu-icons.php') {
            atricon_update_menu_icons();
        }
    });

    // Função para limpar meta data incorreta deixada de versões anteriores
    function atricon_cleanup_old_meta() {
        $menu = wp_get_nav_menu_object('ATRICON');
        if (!$menu) return;
        
        $menu_items = wp_get_nav_menu_items($menu->term_id);
        if (!$menu_items) return;
        
        foreach ($menu_items as $item) {
            // Remove meta data antiga com chave incorreta
            delete_post_meta($item->ID, '_menu_item_menu_icons');
            
            // Também limpa possíveis meta dados malformados
            $all_meta = get_post_meta($item->ID);
            foreach ($all_meta as $key => $value) {
                if (strpos($key, 'menu-icon') !== false && $key !== 'menu-icons') {
                    delete_post_meta($item->ID, $key);
                }
            }
        }
    }

    // Função para resetar o menu ATRICON
    function atricon_reset_menu() {
        // Limpa meta data antiga primeiro
        atricon_cleanup_old_meta();
        
        // Remove o menu existente se houver
        $existing_menu = wp_get_nav_menu_object('ATRICON');
        if ($existing_menu) {
            wp_delete_nav_menu($existing_menu->term_id);
        }
        
        // Recria o menu com todas as configurações
        $menu_id = wp_create_nav_menu('ATRICON');
        $items = atricon_get_menu_items();

        foreach($items as $i){
            $pid = wp_update_nav_menu_item($menu_id, 0, [
                'menu-item-title'  => $i['t'],
                'menu-item-url'    => '#'.$i['v'],
                'menu-item-status' => 'publish',
            ]);
            
            // Configura o ícone do Menu Icons se estiver ativo
            if (atricon_menu_icons_active() && !empty($i['icon'])) {
                update_post_meta($pid, 'menu-icons', [
                    'type' => 'dashicons',
                    'icon' => 'dashicons-' . $i['icon'],
                    'hide_label' => '',
                    'position' => 'before',
                    'vertical_align' => 'middle',
                    'font_size' => '1',
                    'svg_width' => '1',
                    'image_size' => 'thumbnail'
                ]);
            }
            
            if(!empty($i['c'])){
                foreach($i['c'] as $c){
                    $cid = wp_update_nav_menu_item($menu_id, 0, [
                        'menu-item-title'         => $c['t'].' '.($c['code']? "({$c['code']})":''),
                        'menu-item-url'       => '#'.$c['v'],
                        'menu-item-parent-id' => $pid,
                        'menu-item-status'    => 'publish',
                    ]);
                    
                    // Configura o ícone do submenu se estiver ativo
                    if (atricon_menu_icons_active() && !empty($c['icon'])) {
                        update_post_meta($cid, 'menu-icons', [
                            'type' => 'dashicons',
                            'icon' => 'dashicons-' . $c['icon'],
                            'hide_label' => '',
                            'position' => 'before',
                            'vertical_align' => 'middle',
                            'font_size' => '0.9',
                            'svg_width' => '1',
                            'image_size' => 'thumbnail'
                        ]);
                    }
                }
            }
        }

        // Reatribui à localização
        set_theme_mod('nav_menu_locations', array_merge(
            (array)get_theme_mod('nav_menu_locations'), ['atrcn-sidebar'=>$menu_id]
        ));
    }

    // Função auxiliar para obter o ícone correto baseado no item do menu
    function atricon_get_icon_for_item($item) {
        $items = atricon_get_menu_items();
        $icon_map = atricon_build_icon_map($items);
        
        // Busca o ícone baseado na URL do item
        if (isset($icon_map[$item->url])) {
            return 'dashicons-' . $icon_map[$item->url];
        }
        
        // Fallback padrão
        return 'dashicons-admin-generic';
    }

    // Função de debug para verificar ícones (pode ser removida depois)
    function atricon_debug_menu_icons() {
        if (!current_user_can('manage_options')) return;
        
        $menu = wp_get_nav_menu_object('ATRICON');
        if (!$menu) return;
        
        $menu_items = wp_get_nav_menu_items($menu->term_id);
        if (!$menu_items) return;
        
        error_log('=== ATRICON DEBUG MENU ICONS ===');
        foreach ($menu_items as $item) {
            $icon_meta = get_post_meta($item->ID, 'menu-icons', true);
            error_log("Item: {$item->title} (URL: {$item->url})");
            error_log("Icon Meta: " . print_r($icon_meta, true));
            error_log("---");
        }
        error_log('=== FIM DEBUG ===');
    }

    // Adiciona debug no admin (remover depois se necessário)
    add_action('admin_init', function() {
        if (isset($_GET['atricon_debug']) && current_user_can('manage_options')) {
            atricon_debug_menu_icons();
        }
    });

    // Hook específico para garantir que o Menu Icons processe nosso menu
    add_filter('walker_nav_menu_start_el', function($item_output, $item, $depth, $args) {
        // Só processa se for nosso menu ATRICON
        if (isset($args->theme_location) && $args->theme_location === 'atrcn-sidebar') {
            // Se Menu Icons está ativo, aplica seu processamento
            if (atricon_menu_icons_active() && function_exists('menu_icons')) {
                // Aplica o filtro específico do Menu Icons
                $item_output = apply_filters('menu_icons_item_title', $item_output, $item->ID, $args, $item);
            }
        }
        return $item_output;
    }, 10, 4);

    // IMPORTANTE: Para funcionar corretamente, o hide_label deve ser '0' (false) 
    // para permitir que nosso CSS controle a visibilidade dos textos

    // Endpoint AJAX para buscar itens do menu
    add_action('wp_ajax_atricon_search_menu', 'atricon_search_menu_ajax');
    add_action('wp_ajax_nopriv_atricon_search_menu', 'atricon_search_menu_ajax');

    function atricon_search_menu_ajax() {
        check_ajax_referer('atricon_search_nonce', 'nonce');
        
        $query = sanitize_text_field($_POST['query'] ?? '');
        
        if (strlen($query) < 2) {
            wp_send_json_success([]);
            return;
        }
        
        // Busca nos itens do menu registrado
        $menu_locations = get_nav_menu_locations();
        $menu_id = $menu_locations['atrcn-sidebar'] ?? 0;
        
        if (!$menu_id) {
            wp_send_json_error('Menu não encontrado');
            return;
        }
        
        $menu_items = wp_get_nav_menu_items($menu_id);
        $results = [];
        
        if ($menu_items) {
            foreach ($menu_items as $item) {
                // Pula o item de busca
                if ($item->url === '#busca-servico') {
                    continue;
                }
                
                // Verifica se o título contém a busca
                if (stripos($item->title, $query) !== false) {
                    $results[] = [
                        'id' => $item->ID,
                        'title' => $item->title,
                        'url' => $item->url,
                        'parent' => $item->menu_item_parent,
                        'classes' => implode(' ', $item->classes ?? [])
                    ];
                }
            }
        }
        
        wp_send_json_success($results);
    }

    // Enqueue scripts necessários para AJAX
    add_action('wp_enqueue_scripts', function() {
        wp_localize_script('jquery', 'atricon_ajax', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('atricon_search_nonce')
        ]);
    });

    // NEW: JavaScript for dynamic body margin and class toggling
    add_action('wp_footer', function(){
        $pos = get_option('atricon_sidebar_position', 'left');
        $behavior = get_option('atricon_sidebar_behavior', 'icon_only');
        $side = $pos === 'right' ? 'right' : 'left';
        ?>
        <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function() {
            var body = document.body;
            var sidebar = document.getElementById('atricon-sidebar');
            var mobileToggle = document.querySelector('.atricon-mobile-toggle');
        
            if (sidebar) {
                body.classList.add('atricon-sidebar-<?php echo $side; ?>-visible');
        
                <?php if ($behavior === 'icon_only'): ?>
                sidebar.addEventListener('mouseenter', function() {
                    if (window.innerWidth > 768) { // Only apply hover effect on desktop/tablet
                        body.classList.add('atricon-sidebar-expanded');
                    }
                });
                sidebar.addEventListener('mouseleave', function() {
                    if (window.innerWidth > 768) {
                        body.classList.remove('atricon-sidebar-expanded');
                    }
                });
                <?php endif; ?>
        
                if (mobileToggle) {
                    mobileToggle.addEventListener('click', function() {
                        sidebar.classList.toggle('mobile-active');
                        body.classList.toggle('atricon-sidebar-mobile-active');
                        // Ensure expanded class is removed if mobile menu is closed and was expanded
                        if (!sidebar.classList.contains('mobile-active')) {
                            body.classList.remove('atricon-sidebar-expanded');
                        }
                    });
                }
        
                var lastWidth = window.innerWidth;
                window.addEventListener('resize', function() {
                    var currentWidth = window.innerWidth;
                    if (!body || !sidebar) return;
        
                    var isMobile = currentWidth <= 768;
                    var wasMobile = lastWidth <= 768;
        
                    if (isMobile && !wasMobile) { // Transitioned to Mobile
                        body.classList.remove('atricon-sidebar-expanded');
                        // If sidebar was not explicitly toggled active on mobile, remove mobile active class for body margin
                        if (!sidebar.classList.contains('mobile-active')) {
                            body.classList.remove('atricon-sidebar-mobile-active');
                        }
                    } else if (!isMobile && wasMobile) { // Transitioned to Desktop/Tablet
                        body.classList.remove('atricon-sidebar-mobile-active');
                        <?php if ($behavior === 'icon_only'): ?>
                        // If sidebar is currently hovered, ensure expanded class is on body
                        if (sidebar.matches(':hover')) {
                            body.classList.add('atricon-sidebar-expanded');
                        } else {
                            body.classList.remove('atricon-sidebar-expanded');
                        }
                        <?php endif; ?>
                    }
                    lastWidth = currentWidth;
                });
            }
        });
        </script>
        <?php
    });
