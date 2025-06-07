# ATRICON Sidebar Menu

**Version:** 1.2
**Author:** Hermes
**Text Domain:** atricon-sidebar-menu

## Descrição

O **ATRICON Sidebar Menu** é um plugin WordPress que cria automaticamente um menu lateral fixo, responsivo e expansível, inspirado no estilo de navegação de portais governamentais. Ele:

* Registra uma localização de menu chamada **ATRICON Sidebar**
* Cria, na ativação, um menu chamado **ATRICON** com toda a hierarquia e ícones pré-definidos
* Renderiza o menu em todas as páginas no início do `<body>`
* Usa ícones Material Icons para cada item principal
* Exibe submenus expansíveis ao passar o mouse
* Adiciona um logo no rodapé da barra lateral
* Injeta CSS inline para garantir o comportamento de hover e colapso

## Funcionalidades

1. **Criação automática de menu**
   Ao ativar o plugin, o menu `ATRICON` é criado caso não exista e já é povoado com todos os itens e sub-itens.

2. **Localização de menu registrada**
   Registra a área de menu `atrcn-sidebar` para atribuição manual ou automática ao tema.

3. **Ícones via Material Icons**
   Cada link principal recebe um ícone correspondente do Google Material Icons, carregado automaticamente via CDN.

4. **Submenus expansíveis**
   Sub-itens são mostrados em listas aninhadas, revelando conteúdo ao passar o cursor.

5. **Expansão em hover**
   A barra lateral aumenta de largura de `60px` para `240px` ao passar o mouse.

6. **Logo fixa no rodapé**
   Exibe um `logo.png` na parte inferior da sidebar para reforçar a identidade visual.

7. **CSS inline gerado dinamicamente**
   Todo o estilo necessário é injetado no `<head>` para facilitar ajustes rápidos.

8. **Comportamento configurável**
   * Opção para posicionar o menu à esquerda ou direita
   * Modo "apenas ícones" ou "sempre expandido"
   * Design responsivo com suporte a dispositivos móveis

## Requisitos

* WordPress 6.0 ou superior
* PHP 7.4 ou superior
* Tema que suporte o hook `wp_body_open` (ou equivalente para inserir HTML após `<body>`)
* Conexão com internet para carregar os ícones Material Icons via CDN do Google Fonts

## Configuração

O plugin pode ser configurado através do painel de administração do WordPress em:

1. **Configurações > ATRICON Sidebar**
   * Posição do menu (esquerda/direita)
   * Comportamento do menu (apenas ícones/sempre expandido)
   * Opção para resetar o menu para as configurações padrão

## Notas de Atualização

### Versão 1.2
* Migração de dashicons para Material Icons
* Melhorias na responsividade
* Otimização do carregamento de recursos
* Correções de bugs e melhorias de performance

### Versão 1.1
* Adição de configurações de posicionamento
* Melhorias na responsividade
* Correções de bugs 