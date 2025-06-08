jQuery(function($){
  // Extrai do DOM WP a estrutura de submenus
  const submenus = {};
  $('#menu-list > li.menu-item').each(function(){
    const key = $(this).attr('id') || $(this).data('menu-item-id');
    const title = $(this).children('a').text().trim();
    const items = [];
    $(this).find('> ul.sub-menu > li.menu-item').each(function(){
      const $a = $(this).children('a');
      items.push({ title: $a.text().trim(), href: $a.attr('href') });
    });
    submenus[key] = { title, items };
  });

  function openSubmenu(key){
    const data = submenus[key];
    if (!data) return;
    $('#submenu-title').text(data.title);
    $('#submenu-content').empty();
    data.items.forEach(i => {
      $('#submenu-content').append(`
        <a href="${i.href}">
          <div class="submenu-title">${i.title}</div>
          <div class="submenu-code"></div>
        </a>
      `);
    });
    $('#main-sidebar').addClass('expand');
    $('#submenu').addClass('open');
  }

  function closeSubmenu(){
    $('#main-sidebar').removeClass('expand');
    $('#submenu').removeClass('open');
  }

  // Hover e saída
  $('#menu-list').on('mouseenter', 'li.menu-item', function(){
    openSubmenu(this.id || $(this).data('menu-item-id'));
  });
  $('#menu-container').on('mouseleave', closeSubmenu);

  // Busca
  $('#menu-search').on('input', function(){
    const term = $(this).val().toLowerCase();
    $('#clear-search').toggle(!!term);
    $('#menu-list li.menu-item').each(function(){
      const txt = $(this).children('a').text().toLowerCase();
      $(this).toggle(txt.includes(term));
    });
  });
  $('#clear-search').on('click', function(){
    $('#menu-search').val('').trigger('input').focus();
  });
});
