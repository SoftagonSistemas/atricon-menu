const menuData = [
  { t: 'TRANSPARÊNCIA', v: 'transparencia', icon: 'visibility' },
  { t: 'Organização Administrativa', v: 'organizacao', icon: 'account_tree', c: [
    ['Estrutura Organizacional', '2.1 a 2.5'],
    ['Recursos Humanos', '6.1 a 6.6'],
    ['Convênios e Transferências', '5.1 a 5.3']
  ]},
  { t: 'Normas e Leis', v: 'leis', icon: 'gavel', c: [
    ['Legislações e Atos', '2.6'],
    ['LAI', 'Lei 12.527/2011'],
    ['LRF', 'LC 101/2000'],
    ['LGPD e Governo Digital', '15.1 a 15.6']
  ]},
  { t: 'Contabilidade Pública', v: 'contabilidade', icon: 'attach_money', c: [
    ['Receitas', '3.1 a 3.3'],
    ['Despesas', '4.1 a 4.2'],
    ['Renúncias de Receitas', '16.1 a 16.4'],
    ['Dívida Ativa', '3.3']
  ]},
  { t: 'Gestão de Recursos', v: 'recursos', icon: 'bar_chart', c: [
    ['Planejamento e Contas', '11.1 a 11.10'],
    ['Emendas Parlamentares', '17.1 a 17.2']
  ]},
  { t: 'Contratos e Licitações', v: 'contratos', icon: 'description', c: [
    ['Licitações e Contratos', '8.1 a 9.4'],
    ['Ordem Cronológica', '9.4']
  ]},
  { t: 'Despesas com Pessoal', v: 'pessoal', icon: 'group', c: [
    ['Diárias e Passagens', '7.1 a 7.2'],
    ['Valores das Diárias', '7.2']
  ]},
  { t: 'Cidadania e Acesso', v: 'cidadania', icon: 'support_agent', c: [
    ['SIC', '12.1 a 12.9'],
    ['Ouvidorias', '14.1 a 14.3'],
    ['Perguntas Frequentes', '2.7'],
    ['Carta de Serviços ao Cidadão', '']
  ]},
  { t: 'Publicações Oficiais', v: 'publicacoes', icon: 'library_books', c: [
    ['Diário Oficial', 'Lei 4.965/1966'],
    ['Transparência COVID-19', 'PRSE/MPF 12/2022']
  ]},
  { t: 'Indicadores e Avaliação', v: 'avaliacao', icon: 'emoji_events', c: [
    ['Radar da Transparência Pública', '2.9'],
    ['Dados Abertos', 'CGU']
  ]},
  { t: 'Serviços Essenciais', v: 'servicos', icon: 'apartment', c: [
    ['Obras', '10.1 a 10.4'],
    ['Saúde', '18.1 a 18.3'],
    ['Educação', '19.1 a 19.2']
  ]},
];

const submenus = {};
function renderMenu() {
  const $list = jQuery('#menu-list');
  $list.empty();
  menuData.forEach(item => {
    if (item.c) {
      submenus[item.v] = { title: item.t, items: item.c };
    } else {
      submenus[item.v] = { title: item.t, items: [] };
    }
    $list.append(`
      <li class="menu-item" data-submenu="${item.v}">
        <a href="#">
          <i class="material-icons">${item.icon}</i>
          <span class="label">${item.t}</span>
        </a>
      </li>
    `);
  });
}

function renderSubmenu(key) {
  if (key && submenus[key] && submenus[key].items.length > 0) {
    jQuery('#submenu-title').text(submenus[key].title);
    jQuery('#submenu-content').html(
      submenus[key].items.map(([title, code]) => `
        <a href='#'>
          <div class='submenu-title'>${title}</div>
          <div class='submenu-code'>Item ${code}</div>
        </a>`).join('')
    );
    jQuery('#submenu').addClass('open');
    jQuery('#main-sidebar').addClass('expand');
  } else {
    jQuery('#submenu').removeClass('open');
    jQuery('#submenu-title').text('');
    jQuery('#submenu-content').empty();
    jQuery('#main-sidebar').addClass('expand');
  }
}

jQuery(document).ready(function () {
  renderMenu();

  jQuery('.menu').on('mouseenter', '.menu-item', function () {
    const key = jQuery(this).data('submenu');
    renderSubmenu(key);
  });

  jQuery('#menu-container').on('mouseleave', function () {
    jQuery('#submenu').removeClass('open');
    jQuery('#main-sidebar').removeClass('expand');
  });

  jQuery('#menu-search').on('input', function () {
    const search = jQuery(this).val().toLowerCase();
    jQuery('#clear-search').toggle(!!search);
    jQuery('.menu-item').each(function () {
      const key = jQuery(this).data('submenu');
      const mainTitle = jQuery(this).text().toLowerCase();
      const subItems = submenus[key]?.items?.map(([title]) => title.toLowerCase()) || [];
      const match = mainTitle.includes(search) || subItems.some(sub => sub.includes(search));
      jQuery(this).toggle(match);
    });
  });

  jQuery('#clear-search').on('click', function () {
    jQuery('#menu-search').val('').trigger('input').focus();
  });
});
