<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Menu functionality and structure
 */
class ATRICON_Menu {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('init', array($this, 'init'), 20);
    }
    
    public function init() {
        // Create menu if it doesn't exist
        if (!wp_get_nav_menu_object('ATRICON Menu')) {
            $this->create_atricon_menu();
        }
    }
    
    public function create_atricon_menu() {
        // Create the menu
        $menu_id = wp_create_nav_menu('ATRICON Menu');
        if (is_wp_error($menu_id)) {
            return false;
        }
        
        $menu_structure = $this->get_menu_structure();
        $this->add_menu_items($menu_id, $menu_structure);
        
        return true;
    }
    
    public function reset_menu() {
        $menu = wp_get_nav_menu_object('ATRICON Menu');
        if ($menu) {
            wp_delete_nav_menu($menu->term_id);
        }
        return $this->create_atricon_menu();
    }
    
    public function render_menu_items() {
        $menu_structure = $this->get_menu_structure();
        $this->render_menu_items_recursive($menu_structure);
    }
    
    public function search_menu_items($query) {
        $menu = wp_get_nav_menu_object('ATRICON Menu');
        if (!$menu) {
            return array();
        }
        
        $menu_items = wp_get_nav_menu_items($menu->term_id);
        $results = array();
        
        foreach ($menu_items as $item) {
            if (stripos($item->title, $query) !== false) {
                $results[] = array(
                    'title' => $item->title,
                    'url' => $item->url
                );
            }
        }
        
        return $results;
    }
    
    public function is_menu_icons_active() {
        if (!function_exists('is_plugin_active')) {
            include_once(ABSPATH . 'wp-admin/includes/plugin.php');
        }
        return is_plugin_active('menu-icons/menu-icons.php');
    }
    
    public function update_menu_icons() {
        if (!$this->is_menu_icons_active()) {
            return false;
        }
        
        $menu = wp_get_nav_menu_object('ATRICON Menu');
        if (!$menu) {
            return false;
        }
        
        $menu_items = wp_get_nav_menu_items($menu->term_id);
        $icon_mapping = $this->get_icon_mapping();
        
        foreach ($menu_items as $item) {
            $title = trim($item->title);
            
            if (isset($icon_mapping[$title])) {
                update_post_meta($item->ID, '_menu_item_icon', array(
                    'type' => 'dashicons',
                    'icon' => $icon_mapping[$title]
                ));
            }
        }
        
        return true;
    }
    
    private function get_menu_structure() {
        return array(
            ['t'=>'BUSCA_PLACEHOLDER','v'=>'busca-servico','icon'=>'<svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px" fill="currentColor"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/></svg>'], 
            ['t'=>'TRANSPARÊNCIA','v'=>'transparencia','icon'=>'<svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px" fill="currentColor"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>'],
            ['t'=>'Organização Administrativa','v'=>'organizacao','icon'=>'<svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px" fill="currentColor"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8h5z"/></svg>','c'=>[
                ['t'=>'Estrutura Organizacional','v'=>'estrutura-organizacional','code'=>'2.1 a 2.5','icon'=>'<svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px" fill="currentColor"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.95-.49-7-3.85-7-7.93s3.05-7.44 7-7.93v15.86zm2-15.86c1.03.13 2 .45 2.87.93H13v-.93zM13 7h5.24c.25.31.48.65.68 1H13V7zm0 3h6.74c.08.33.15.66.18 1H13v-1zm0 9.93V19h2.87c-.87.48-1.84.8-2.87.93zM18.24 17H13v-1h5.92c-.2.35-.43.69-.68 1zm1.5-3H13v-1h6.92c-.03.34-.09.67-.18 1z"/></svg>'],
                ['t'=>'Recursos Humanos','v'=>'recursos-humanos','code'=>'6.1 a 6.6','icon'=>'<svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px" fill="currentColor"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>'],
                ['t'=>'Convênios e Transferências','v'=>'convenios-transferencias','code'=>'5.1 a 5.3','icon'=>'<svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px" fill="currentColor"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M15 7v4h1v2h-3V5h2l-3-4-3 4h2v8H8v-2.07C8.7.7 9.79 0 11 0c.88 0 1.63.29 2.26.76L15 3V1h4v4h-4zm-6 6H3v6c0 1.1.9 2 2 2h6v-3l4 4 4-4v-3h-3.45c-.28-.58-.63-1.11-1.05-1.55L19 15.03V13h-1.3c-.25-.92-.65-1.76-1.17-2.5H15v-2H9v2z"/></svg>'],
            ]],
            ['t'=>'Normas e Leis','v'=>'leis','icon'=>'<svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px" fill="currentColor"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M14.17 1L7 8.18V21h12V4.83L14.17 1zM15 15H9v-2h6v2zm0-4H9V9h6v2zm-3-5V3.5L18.5 9H13V6z" opacity=".3"/><path d="M15 9H9v2h6V9zm0 4H9v2h6v-2zm5-12.5L14.17 1H7c-1.1 0-1.99.9-1.99 2L5 21c0 1.1.89 2 1.99 2H19c1.1 0 2-.9 2-2V4.83c0-.53-.21-1.04-.59-1.41zM19 21H7V8.18l7.17-7.17c.19-.19.44-.3.71-.3H19V21zm-6-15h1.5L13 3.5V6z"/></svg>','c'=>[
                ['t'=>'Legislações e Atos','v'=>'legislacoes','code'=>'2.6','icon'=>'<svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px" fill="currentColor"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/></svg>'],
                ['t'=>'LAI','v'=>'lai','code'=>'Lei 12.527/2011','icon'=>'<svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px" fill="currentColor"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M19.5 9.5c-1.03 0-1.9.62-2.29 1.5h-2.92c-.4-1.54-1.7-2.75-3.29-2.95V6.5c.55-.24.9-.79.9-1.42C11.9 4.03 11.03 3.2 10 3.2c-1.03 0-1.9.83-1.9 1.88 0 .63.35 1.18.9 1.42v1.55c-1.59.2-2.89 1.41-3.29 2.95H3.79c-.39-.88-1.26-1.5-2.29-1.5C.67 9.5 0 10.17 0 11s.67 1.5 1.5 1.5c1.03 0 1.9-.62 2.29-1.5h2.92c.4 1.54 1.7 2.75 3.29 2.95v1.55c-.55.24-.9.79-.9 1.42 0 1.05.87 1.88 1.9 1.88 1.03 0 1.9-.83 1.9-1.88 0-.63-.35-1.18-.9-1.42v-1.55c1.59-.2 2.89-1.41 3.29-2.95h2.92c.39.88 1.26 1.5 2.29 1.5 1.03 0 1.9-.67 1.9-1.5s-.87-1.5-1.9-1.5zm-9.5 4c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2z"/></svg>'],
                ['t'=>'LRF','v'=>'lrf','code'=>'LC 101/2000','icon'=>'<svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px" fill="currentColor"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M12 6c1.1 0 2 .9 2 2s-.9 2-2 2-2-.9-2-2 .9-2 2-2m0 10c2.7 0 5.8 1.29 6 2H6c.23-.72 3.31-2 6-2m0-12C9.79 4 8 5.79 8 8s1.79 4 4 4 4-1.79 4-4-1.79-4-4-4zm0 10c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>'],
                ['t'=>'LGPD e Governo Digital','v'=>'lgpd-governo','code'=>'15.1 a 15.6','icon'=>'<svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px" fill="currentColor"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm0 10.99h7c-.53 4.12-3.28 7.79-7 8.94V12H5V6.3l7-3.11v8.8z"/></svg>'],
            ]],
            ['t'=>'Contabilidade Pública','v'=>'contabilidade','icon'=>'<svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px" fill="currentColor"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M21.99 4c0-1.1-.89-2-1.99-2H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h14l4 4-.01-18zM18 14H6v-2h12v2zm0-3H6V9h12v2zm0-3H6V6h12v2z"/></svg>','c'=>[
                ['t'=>'Receitas','v'=>'receitas','code'=>'3.1 a 3.3','icon'=>'<svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px" fill="currentColor"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zm10 0c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2zm-1.45-5c.75 0 1.41-.41 1.75-1.03l3.58-6.49c.37-.66-.11-1.48-.87-1.48H5.21l-.94-2H1v2h2l3.6 7.59-1.35 2.44C4.52 15.37 5.48 17 7 17h12v-2H7l1.1-2h7.45zM6.16 6h12.15l-2.76 5H8.53L6.16 6z"/></svg>'],
                ['t'=>'Despesas','v'=>'despesas','code'=>'4.1 a 4.2','icon'=>'<svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px" fill="currentColor"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.77-2.7 1.77-2.24 0-2.81-.99-2.81-2.15h-2.2c.04 2.02 1.35 3.46 3.51 3.87V21h3v-2.15c1.95-.37 3.5-1.65 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z"/></svg>'],
                ['t'=>'Renúncias de Receitas','v'=>'renuncias','code'=>'16.1 a 16.4','icon'=>'<svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px" fill="currentColor"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-5.5-2.5l1.41-1.41L12 16.17l4.09-4.09L17.5 13.5 12 19l-5.5-5.5zM8.5 11.5l1.41-1.41L12 12.17l4.09-4.09L17.5 9.5 12 15l-5.5-5.5z" opacity=".3"/><path d="M12 4c-4.41 0-8 3.59-8 8s3.59 8 8 8 8-3.59 8-8-3.59-8-8-8zm0 16c-3.31 0-6-2.69-6-6s2.69-6 6-6 6 2.69 6 6-2.69 6-6 6zm-1.41-4.09L12 13.33l1.41-1.42L12 10.5l-1.41 1.41zm0-2.82L12 9.09l1.41-1.41L12 6.27l-1.41 1.41z"/></svg>'],
                ['t'=>'Dívida Ativa','v'=>'divida-ativa','code'=>'3.3','icon'=>'<svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px" fill="currentColor"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z"/></svg>'],
            ]],
            ['t'=>'Gestão de Recursos','v'=>'recursos','icon'=>'<svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px" fill="currentColor"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M5 9.2h3V19H5zM10.6 5h2.8v14h-2.8zm5.6 8H19v6h-2.8z"/></svg>','c'=>[
                ['t'=>'Planejamento e Contas','v'=>'planejamento','code'=>'11.1 a 11.10','icon'=>'<svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px" fill="currentColor"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>'],
                ['t'=>'Emendas Parlamentares','v'=>'emendas','code'=>'17.1 a 17.2','icon'=>'<svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px" fill="currentColor"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/></svg>'],
            ]],
            ['t'=>'Contratos e Licitações','v'=>'contratos','icon'=>'<svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px" fill="currentColor"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/></svg>','c'=>[
                ['t'=>'Licitações e Contratos','v'=>'licitacoes','code'=>'8.1 a 9.4','icon'=>'<svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px" fill="currentColor"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/></svg>'],
                ['t'=>'Ordem Cronológica','v'=>'ordem-cronologica','code'=>'9.4','icon'=>'<svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px" fill="currentColor"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM7 10h5v5H7v-5z"/></svg>'],
            ]],
            ['t'=>'Despesas com Pessoal','v'=>'pessoal','icon'=>'<svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px" fill="currentColor"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>','c'=>[
                ['t'=>'Diárias e Passagens','v'=>'diarias','code'=>'7.1 a 7.2','icon'=>'<svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px" fill="currentColor"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M21 16v-2l-8-5V3.5c0-.83-.67-1.5-1.5-1.5S10 2.67 10 3.5V9l-8 5v2l8-2.5V19l-2 1.5V22l3.5-1 3.5 1v-1.5L13 19v-5.5l8 2.5z"/></svg>'],
                ['t'=>'Valores das Diárias','v'=>'valores-diarias','code'=>'7.2','icon'=>'<svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px" fill="currentColor"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-4 14H9V7h6v10z" opacity=".3"/><path d="M5 21h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2zM5 5h14v14H5V5zm4 2v10h6V7H9zm2 2h2v2h-2V9zm0 4h2v2h-2v-2z"/></svg>'],
            ]],
            ['t'=>'Cidadania e Acesso','v'=>'cidadania','icon'=>'<svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px" fill="currentColor"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M16.5 12c1.38 0 2.5-1.12 2.5-2.5S17.88 7 16.5 7C15.12 7 14 8.12 14 9.5s1.12 2.5 2.5 2.5zM9 11c1.66 0 2.99-1.34 2.99-3S10.66 5 9 5C7.34 5 6 6.34 6 8s1.34 3 3 3zm7.5 3c-1.83 0-5.5.92-5.5 2.75V19h11v-2.25c0-1.83-3.67-2.75-5.5-2.75zM9 13c-2.33 0-7 1.17-7 3.5V19h7v-2.5c0-.85.33-2.34 2.37-3.47C10.5 13.1 9.66 13 9 13z"/></svg>','c'=>[
                ['t'=>'SIC','v'=>'sic','code'=>'12.1 a 12.9','icon'=>'<svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px" fill="currentColor"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M11 7h2v2h-2zm0 4h2v6h-2zm1-9C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"/></svg>'],
                ['t'=>'Ouvidorias','v'=>'ouvidorias','code'=>'14.1 a 14.3','icon'=>'<svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px" fill="currentColor"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M12 14c1.66 0 2.99-1.34 2.99-3L15 5c0-1.66-1.34-3-3-3S9 3.34 9 5v6c0 1.66 1.34 3 3 3zm5.3-3c0 3-2.54 5.1-5.3 5.1S6.7 14 6.7 11H5c0 3.41 2.72 6.23 6 6.72V21h2v-3.28c3.28-.48 6-3.3 6-6.72h-1.7z"/></svg>'],
                ['t'=>'Perguntas Frequentes','v'=>'faq','code'=>'2.7','icon'=>'<svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px" fill="currentColor"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M11 18h2v-2h-2v2zm1-16C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm0-14c-2.21 0-4 1.79-4 4h2c0-1.1.9-2 2-2s2 .9 2 2c0 2-3 1.75-3 5h2c0-2.25 3-2.5 3-5 0-2.21-1.79-4-4-4z"/></svg>'],
                ['t'=>'Carta de Serviços ao Cidadão','v'=>'carta-servicos','code'=>'','icon'=>'<svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px" fill="currentColor"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg>'],
            ]],
            ['t'=>'Publicações Oficiais','v'=>'publicacoes','icon'=>'<svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px" fill="currentColor"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/></svg>','c'=>[
                ['t'=>'Diário Oficial','v'=>'diario-oficial','code'=>'Lei 4.965/1966','icon'=>'<svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px" fill="currentColor"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M18 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zM6 4h5v8l-2.5-1.5L6 12V4z" opacity=".3"/><path d="M18 0H6C4.9 0 4 .9 4 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V2c0-1.1-.9-2-2-2zM6 2h12v1c0 .55-.45 1-1 1H7c-.55 0-1-.45-1-1V2zm0 18V4h5l.01 8L8.5 10.5 6 12.01V20zm12 0h-5v-8l2.5 1.5L18 12V4h0v16z"/></svg>'],
                ['t'=>'Transparência COVID-19','v'=>'transparencia-covid','code'=>'PRSE/MPF 12/2022','icon'=>'<svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px" fill="currentColor"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M11 1v2c0 .55.45 1 1 1s1-.45 1-1V1h-2zm7.05 3.56c-.39-.39-1.02-.39-1.41 0s-.39 1.02 0 1.41l1.06 1.06c.39.39 1.02.39 1.41 0s.39-1.02 0-1.41l-1.06-1.06zM12 6c-3.31 0-6 2.69-6 6s2.69 6 6 6 6-2.69 6-6-2.69-6-6-6zm0 10c-2.21 0-4-1.79-4-4s1.79-4 4-4 4 1.79 4 4-1.79 4-4 4zm-7.05-5.01c-.39-.39-1.02-.39-1.41 0s-.39 1.02 0 1.41l1.06 1.06c.39.39 1.02.39 1.41 0s.39-1.02 0-1.41l-1.06-1.06zM12 20c.55 0 1 .45 1 1v2c0 .55-.45 1-1 1s-1-.45-1-1v-2c0-.55.45-1 1-1zm4.95-2.44l1.06-1.06c.39-.39.39-1.02 0-1.41s-1.02-.39-1.41 0l-1.06 1.06c-.39.39-.39 1.02 0 1.41s1.03.39 1.41 0zm-9.9 0c.39.39 1.02.39 1.41 0s.39-1.02 0-1.41l-1.06-1.06c-.39-.39-1.02-.39-1.41 0s-.39 1.02 0 1.41l1.06 1.06z"/></svg>'],
            ]],
            ['t'=>'Indicadores e Avaliação','v'=>'avaliacao','icon'=>'<svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px" fill="currentColor"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 15l-3.76-2.27.72-4.22-3.24-2.83 4.02-.58L12 3.5l1.26 3.6 4.02.58-3.24 2.83.72 4.22L12 17z" opacity=".3"/><path d="M22 9.24l-7.19-.62L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21 12 17.27 18.18 21l-1.63-7.03L22 9.24zM12 15.4l-3.76 2.27 1-4.22-3.24-2.83 4.02-.58L12 6.5l1.26 3.6 4.02.58-3.24 2.83 1 4.22L12 15.4z"/></svg>','c'=>[
                ['t'=>'Radar da Transparência Pública','v'=>'radar-transparencia','code'=>'2.9','icon'=>'<svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px" fill="currentColor"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>'],
                ['t'=>'Dados Abertos','v'=>'dados-abertos','code'=>'CGU','icon'=>'<svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px" fill="currentColor"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V5h14v14zM7 10h2v7H7zm4-3h2v10h-2zm4 6h2v4h-2z"/></svg>'],
            ]],
            ['t'=>'Serviços Essenciais','v'=>'servicos','icon'=>'<svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px" fill="currentColor"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M12 3L2 12h3v8h14v-8h3L12 3zm0 13c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2z" opacity=".3"/><path d="M12 3L4.2 10.27C4.08 10.43 4 10.62 4 10.82V19c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2v-8.18c0-.2-.08-.39-.2-.55L12 3zm0 15c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm0-4c-.06 0-.12-.01-.18-.02l.18.18V10h-3V8h2.03c.11-.44.31-.84.57-1.2L9 5l3-2.45 3 2.45-2.6 1.8c.26.36.46.76.57 1.2H15v2h-3v3.98c-.06.01-.12.02-.18.02z"/></svg>','c'=>[
                ['t'=>'Obras','v'=>'obras','code'=>'10.1 a 10.4','icon'=>'<svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px" fill="currentColor"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M22.7 19l-9.1-9.1c.9-2.3.4-5-1.5-6.9-2-2-5-2.4-7.4-1.3L9 6 6 9 1.6 4.7C.4 7.1.9 10.1 2.9 12.1c1.9 1.9 4.6 2.4 6.9 1.5l9.1 9.1c.4.4 1 .4 1.4 0l2.3-2.3c.5-.4.5-1.1.1-1.4z"/></svg>'],
                ['t'=>'Saúde','v'=>'saude','code'=>'18.1 a 18.3','icon'=>'<svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px" fill="currentColor"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>'],
                ['t'=>'Educação','v'=>'educacao','code'=>'19.1 a 19.2','icon'=>'<svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px" fill="currentColor"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82zM12 3L1 9l11 6 9-4.91V17h2V9L12 3z"/></svg>'],
            ]],
        );
    }
    
    private function render_menu_items_recursive($items, $level = 0) {
        $default_icon_svg = '<svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px" fill="currentColor"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M3 18h18v-2H3v2zm0-5h18v-2H3v2zm0-7v2h18V6H3z"/></svg>';
        $submenu_indicator_svg = '<svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px" fill="currentColor"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6-6-6z"/></svg>';

        foreach ($items as $item) {
            $has_children = !empty($item['children']);

            echo '<div class="atricon-menu-item' . ($level > 0 ? ' atricon-submenu-item' : '') . '" role="menuitem">';

            if ($has_children) {
                echo '<a href="#" class="atricon-menu-link" role="button" aria-expanded="false">';
            } else {
                echo '<a href="' . esc_attr($item['url']) . '" class="atricon-menu-link">';
            }

            // SVG Icon
            echo '<span class="atricon-menu-icon" aria-hidden="true">';
            if (isset($item['icon'])) {
                // Directly output SVG
                echo $item['icon']; 
            } else {
                // Default SVG icon
                echo $default_icon_svg; 
            }
            echo '</span>';

            echo '<span class="atricon-menu-text">' . esc_html($item['title']) . '</span>';

            if ($has_children) {
                // SVG Submenu Indicator
                echo '<span class="atricon-submenu-indicator" aria-hidden="true">' . $submenu_indicator_svg . '</span>';
            }

            echo '</a>';

            if ($has_children) {
                echo '<div class="atricon-submenu" role="menu" aria-hidden="true">';
                $this->render_menu_items_recursive($item['children'], $level + 1);
                echo '</div>';
            }

            echo '</div>';
        }
    }
    
    private function add_menu_items($menu_id, $items, $parent_id = 0) {
        foreach ($items as $item) {
            $menu_item_data = array(
                'menu-item-title' => $item['title'],
                'menu-item-url' => $item['url'],
                'menu-item-status' => 'publish',
                'menu-item-type' => 'custom',
                'menu-item-object' => 'custom',
                'menu-item-parent-id' => $parent_id
            );

            $item_id = wp_update_nav_menu_item($menu_id, 0, $menu_item_data);

            // Add icon if Menu Icons is active
            if (!is_wp_error($item_id) && $this->is_menu_icons_active() && isset($item['icon'])) {
                update_post_meta($item_id, '_menu_item_icon', array(
                    'type' => 'dashicons',
                    'icon' => $item['icon']
                ));
            }

            // Add children
            if (isset($item['children']) && !is_wp_error($item_id)) {
                $this->add_menu_items($menu_id, $item['children'], $item_id);
            }
        }
    }
    
    private function get_icon_mapping() {
        return array(
            'TRANSPARÊNCIA' => 'dashicons-visibility',
            'Organização Administrativa' => 'dashicons-networking',
            'Normas e Leis' => 'dashicons-admin-page',
            'Contabilidade Pública' => 'dashicons-money-alt',
            'Gestão de Recursos' => 'dashicons-chart-bar',
            'Contratos e Licitações' => 'dashicons-media-text',
            'Despesas com Pessoal' => 'dashicons-admin-users',
            'Cidadania e Acesso' => 'dashicons-admin-users',
            'Publicações Oficiais' => 'dashicons-media-text',
            'Indicadores e Avaliação' => 'dashicons-awards',
            'Serviços Essenciais' => 'dashicons-building'
        );
    }
}
