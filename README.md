# ATRICON Sidebar Menu

**Versão:** 2.0  
**Compatibilidade:** WordPress 5.0+  
**Tecnologias:** PHP, JavaScript (jQuery), CSS (compatível com Tailwind CSS 4)

## 📋 Descrição

Plugin WordPress profissional que cria um menu lateral fixo moderno e responsivo para o site da ATRICON. **Versão 2.0 completamente refatorada** com arquitetura robusta, eliminação total de marcadores de lista (`<li>`) e integração nativa com Tailwind CSS 4.

## ✨ Principais Melhorias da v2.0

### 🚀 Arquitetura Moderna
- **Programação orientada a objetos** com padrão Singleton
- **Código limpo e modular** com separação de responsabilidades
- **Compatibilidade e integração nativa com Tailwind CSS 4**
- **Zero conflitos** com temas, plugins e frameworks utilitários

### 🎯 Eliminação de Bugs de Marcadores
- **Reset CSS completo** para listas (`ul`, `ol`, `li`)
- **Remoção forçada** de todos os `list-style` e pseudo-elementos
- **Estrutura HTML robusta** sem dependência de listas nativas
- **CSS inline prioritário** para evitar conflitos de especificidade

### 📱 Design Responsivo Aprimorado
- **Mobile-first** com breakpoints otimizados
- **Toggle button** elegante para dispositivos móveis
- **Overlay de fundo** para melhor UX mobile
- **Transições suaves** com animações CSS3

## 🔧 Funcionalidades Técnicas

### Menu Inteligente
- **Criação automática** do menu "ATRICON Menu" na ativação
- **Estrutura hierárquica** com suporte a submenus infinitos
- **Integração nativa** com WordPress Menu System
- **Suporte completo** ao plugin Menu Icons (Dashicons)

### Interface Moderna
- **Design dark** com gradientes sutis
- **Ícones Dashicons** pré-configurados
- **Campo de busca** em tempo real
- **Estados visuais** para item ativo/hover

### Configurações Flexíveis
- **Painel de administração** dedicado
- **Posicionamento** (esquerda/direita)
- **Comportamento** (ícones/expandido)
- **Gerenciamento de ícones** automático
- **Reset de menu** com um clique

## 📦 Instalação

1. Faça upload da pasta `atricon-sidebar-menu` para `/wp-content/plugins/`
2. Ative o plugin no painel administrativo
3. Acesse **Configurações > ATRICON Sidebar** para personalizar
4. *(Recomendado)* Instale o plugin **Menu Icons** para ícones automáticos

## ⚙️ Configuração Técnica

### Estrutura de Arquivos
```
atricon-sidebar-menu/
├── atricon-sidebar-menu.php    # Arquivo principal (classe única)
├── logo.png                    # Logo da ATRICON
├── README.md                   # Documentação
├── CHANGELOG.md                # Histórico de versões
├── assets/                     # (opcional) Assets estáticos (imagens, SVG, fontes)
├── includes/                   # (opcional) Códigos auxiliares/modulares
```

### Hooks e Filtros Utilizados
```php
// Hooks principais
add_action('init', array($this, 'init'));
add_action('admin_menu', array($this, 'admin_menu'));
add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
add_action('wp_footer', array($this, 'render_sidebar'));

// AJAX
add_action('wp_ajax_atricon_search_menu', array($this, 'ajax_search_menu'));
add_action('wp_ajax_nopriv_atricon_search_menu', array($this, 'ajax_search_menu'));

// Ativação
register_activation_hook(__FILE__, function() {
    ATRICON_Sidebar_Menu::get_instance()->create_atricon_menu();
});
```

### CSS Inline Estratégico e Tailwind
O plugin utiliza CSS inline com alta especificidade para garantir que não haja conflitos, mesmo em projetos com Tailwind CSS:

```css
/* Reset completo para listas - evita marcadores */
#atricon-sidebar,
#atricon-sidebar *,
#atricon-sidebar ul,
#atricon-sidebar ol,
#atricon-sidebar li {
    list-style: none !important;
    list-style-type: none !important;
    list-style-image: none !important;
    list-style-position: outside !important;
    margin-left: 0 !important;
    padding-left: 0 !important;
    text-indent: 0 !important;
}
```

#### Como customizar com Tailwind
Você pode adicionar classes utilitárias do Tailwind diretamente nos elementos do menu via painel de administração ou via filtros/hooks do WordPress. Exemplo:

```php
<div id="atricon-sidebar" class="bg-gradient-to-b from-slate-800 to-slate-900 text-slate-200 w-72">
    <!-- ... -->
</div>
```
Ou sobrescrever estilos via CSS customizado:
```css
#atricon-sidebar {
  @apply bg-gradient-to-b from-slate-800 to-slate-900 text-slate-200;
}
```

## 🎨 Personalização CSS

### Variáveis Principais
```css
/* Cores do tema */
--bg-primary: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
--text-primary: #cbd5e1;
--accent-color: #3b82f6;
--border-color: rgba(148, 163, 184, 0.1);

/* Dimensões */
--sidebar-width-collapsed: 60px;
--sidebar-width-expanded: 280px;
--transition-speed: 0.3s cubic-bezier(0.4, 0, 0.2, 1);
```


### Integração e Compatibilidade com Tailwind CSS
O plugin foi projetado para funcionar perfeitamente em ambientes que utilizam Tailwind CSS 4, sem sobrescrever ou conflitar com utilitários do framework. Principais pontos:
- **Utilização de Flexbox e CSS Grid** para layouts responsivos
- **Custom Properties** para fácil customização de temas
- **Classes utilitárias** compatíveis com Tailwind (ex: `flex`, `items-center`, `bg-gradient-to-b`)
- **Reset CSS inline** com alta especificidade para garantir que estilos do Tailwind ou de outros plugins/temas não afetem o menu
- **Pode ser customizado com classes Tailwind diretamente no HTML do menu** (ver exemplos abaixo)

## 📱 Responsividade

### Breakpoints
- **Desktop:** `min-width: 769px` – Menu fixo lateral
- **Tablet/Mobile:** `max-width: 768px` – Menu toggle + overlay

### Comportamento Mobile
```javascript
// Toggle mobile
$(document).on("click", ".atricon-mobile-toggle", function() {
    $("#atricon-sidebar").toggleClass("mobile-open");
    $(".atricon-overlay").toggleClass("active");
});
```

### Dica para Tailwind
Você pode customizar breakpoints e responsividade do menu usando classes Tailwind, por exemplo:
```html
<div id="atricon-sidebar" class="hidden md:block md:w-72 w-16">
    <!-- ... -->
</div>
```

## 🔍 Busca Inteligente

### Funcionalidade
- **Busca em tempo real** com debounce de 300ms
- **Filtro por título** dos itens de menu
- **Contador de resultados** dinâmico
- **Reset automático** ao limpar o campo

### Implementação
```javascript
let searchTimeout;
$(document).on("input", "#atricon-search-input", function() {
    clearTimeout(searchTimeout);
    let query = $(this).val().toLowerCase();
    
    searchTimeout = setTimeout(function() {
        // Lógica de busca
    }, 300);
});
```

## 🔧 API e Extensibilidade

### Métodos Públicos da Classe
```php
// Instância singleton
ATRICON_Sidebar_Menu::get_instance()

// Métodos principais
->create_atricon_menu()     // Cria menu automaticamente
->update_menu_icons()       // Atualiza ícones Dashicons
->reset_menu()              // Reseta menu para padrão
```

### Estrutura de Menu Padrão
```php
$menu_structure = array(
    array(
        'title' => 'Início',
        'url' => home_url('/'),
        'icon' => 'dashicons-admin-home'
    ),
    array(
        'title' => 'Serviços',
        'url' => home_url('/servicos/'),
        'icon' => 'dashicons-admin-tools',
        'children' => array(
            array(
                'title' => 'Consultoria',
                'url' => home_url('/servicos/consultoria/'),
                'icon' => 'dashicons-lightbulb'
            )
        )
    )
);
```

## 🔒 Segurança

### Medidas Implementadas
- **Prevenção de acesso direto** aos arquivos
- **Sanitização** de dados AJAX
- **Nonces** para formulários administrativos
- **Escape** de saídas HTML
- **Validação** de permissões de usuário

### Exemplo de Sanitização
```php
public function ajax_search_menu() {
    $query = sanitize_text_field($_POST['query']);
    // ... resto da implementação
    wp_send_json($results);
}
```

## 🚀 Performance

### Otimizações
- **CSS/JS inline** para reduzir requests HTTP
- **Debounce** na busca para evitar requests excessivos
- **Lazy loading** do menu apenas no frontend
- **Cache nativo** do WordPress para menus

### Métricas
- **Tamanho:** ~29KB (arquivo principal)
- **Requests:** 0 adicionais (CSS/JS inline)
- **Compatibilidade:** WordPress 5.0+ e PHP 7.4+

## 🧪 Testes e Validação

### Compatibilidade Testada
- ✅ **WordPress:** 5.0 - 6.4+
- ✅ **PHP:** 7.4 - 8.2
- ✅ **Temas:** Twenty Twenty-Three, Twenty Twenty-Four, Twenty Twenty-Five
- ✅ **Plugins:** Menu Icons, Elementor, WooCommerce
- ✅ **Browsers:** Chrome, Firefox, Safari, Edge

### Testes de Marcadores
- ✅ **Tema Twenty Twenty-Five:** Sem marcadores
- ✅ **Tailwind CSS:** Compatibilidade total
- ✅ **Bootstrap CSS:** Sem conflitos
- ✅ **CSS customizados:** Reset forçado funciona

## 📞 Suporte

### Resolução de Problemas Comuns

**Marcadores ainda aparecem?**
```css
/* Adicione ao CSS do tema se necessário */
#atricon-sidebar li::before,
#atricon-sidebar li::after {
    content: none !important;
    display: none !important;
}
```

**Menu não aparece?**
1. Verifique se o plugin está ativo
2. Limpe o cache do site
3. Acesse Configurações > ATRICON Sidebar > Resetar Menu


**Conflito com Tailwind?**
O plugin v2.0 é totalmente compatível. O CSS inline do plugin tem prioridade e evita sobrescritas indesejadas. Caso precise de ajustes finos, utilize utilitários Tailwind ou sobrescreva via painel de personalização do tema.

**Dica:** Se usar purge do Tailwind, garanta que as classes usadas no menu estejam incluídas no safelist do Tailwind para não serem removidas na build.

## 📄 Licença

Plugin proprietário desenvolvido para ATRICON. Todos os direitos reservados.

---

**Desenvolvido por:** Hermes  
**Versão:** 2.0 (Refatoração Completa)  
**Data:** 2024
