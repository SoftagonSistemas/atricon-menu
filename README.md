# ATRICON Sidebar Menu

**Version:** 1.1
**Author:** Hermes
**Text Domain:** atricon-sidebar-menu

## Descrição

O **ATRICON Sidebar Menu** é um plugin WordPress que cria automaticamente um menu lateral fixo, responsivo e expansível, inspirado no estilo de navegação de portais governamentais. Ele:

* Registra uma localização de menu chamada **ATRICON Sidebar**
* Cria, na ativação, um menu chamado **ATRICON** com toda a hierarquia e ícones pré-definidos
* Renderiza o menu em todas as páginas no início do `<body>`
* Usa ícones FontAwesome para cada item principal
* Exibe submenus expansíveis ao passar o mouse
* Adiciona um logo no rodapé da barra lateral
* Injeta CSS inline para garantir o comportamento de hover e colapso

## Funcionalidades

1. **Criação automática de menu**
   Ao ativar o plugin, o menu `ATRICON` é criado caso não exista e já é povoado com todos os itens e sub-itens.

2. **Localização de menu registrada**
   Registra a área de menu `atrcn-sidebar` para atribuição manual ou automática ao tema.

3. **Ícones via FontAwesome**
   Cada link principal recebe um ícone correspondente (mapeado internamente em `atricon_fa_map()`).

4. **Submenus expansíveis**
   Sub-itens são mostrados em listas aninhadas, revelando conteúdo ao passar o cursor.

5. **Expansão em hover**
   A barra lateral aumenta de largura de `60px` para `240px` ao passar o mouse.

6. **Logo fixa no rodapé**
   Exibe um `logo.png` na parte inferior da sidebar para reforçar a identidade visual.

7. **CSS inline gerado dinamicamente**
   Todo o estilo necessário é injetado no `<head>` para facilitar ajustes rápidos.

## Requisitos

* WordPress 6.0 ou superior
* PHP 7.4 ou superior
* Tema que suporte o hook `wp_body_open` (ou equivalente para inserir HTML após `<body>`)
* FontAwesome (você deve ter o CSS do FontAwesome disponível no seu tema ou carregá-lo manualmente)
 