# ATRICON Sidebar Menu

**Version:** 1.5
**Author:** Hermes
**Text Domain:** atricon-sidebar-menu

## Descrição

O **ATRICON Sidebar Menu** é um plugin WordPress que cria automaticamente um menu lateral fixo, responsivo e expansível, inspirado no estilo de navegação de portais governamentais. Ele:

* Registra uma localização de menu chamada **ATRICON Sidebar**
* Cria, na ativação, um menu chamado **ATRICON** com toda a hierarquia e ícones pré-definidos
* Renderiza o menu em todas as páginas no início do `<body>`
* Usa ícones Material Icons para cada item principal
* Exibe submenus expansíveis ao passar o mouse
* **NOVO**: Busca inteligente que funciona tanto em itens principais quanto em subitens
* Adiciona um logo no rodapé da barra lateral

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

6. **Busca inteligente melhorada**
   - Busca funciona em itens principais e subitens
   - Remove acentos automaticamente para melhor compatibilidade
   - Mostra automaticamente o submenu quando encontra resultado em subitem
   - Destaca visualmente os itens encontrados
   - Exibe contador de resultados em tempo real
   - Exemplo: digite "Convênio" e verá "Organização Administrativa" expandido com "Convênios e Transferências" destacado

7. **Logo fixa no rodapé**
   Exibe um `logo.png` na parte inferior da sidebar para reforçar a identidade visual.

## Exemplos de Uso da Busca

- Digite **"Convênio"** → Mostra "Organização Administrativa" com "Convênios e Transferências" destacado
- Digite **"LAI"** → Mostra "Normas e Leis" com "LAI" destacado  
- Digite **"Transparência"** → Mostra tanto o item principal quanto subitens relacionados
- Digite **"diaria"** → Encontra "Diárias e Passagens" em "Despesas com Pessoal"
  