jQuery(function($){
  // Função para abrir o submenu do item de menu
  function openSubmenu($menuItem) {
    const $submenu = $menuItem.children('ul.sub-menu');
    const title = $menuItem.children('a').find('.label').text().trim() || $menuItem.children('a').text().trim();
    $('#submenu-title').text(title);
    $('#submenu-content').empty();
    if ($submenu.length > 0) {
      $submenu.children('li.menu-item').each(function(){
        const $a = $(this).children('a');
        const icon = $a.find('i.material-icons').text().trim();
        const label = $a.find('.label').text().trim() || $a.text().trim();
        const href = $a.attr('href');
        $('#submenu-content').append(`
          <a href="${href}">
            <i class="material-icons menu-icon" aria-hidden="true">${icon}</i> <span class="label">${label}</span>
          </a>
        `);
      });
      $('#main-sidebar').addClass('expand');
      $('#submenu').addClass('open');
    } else {
      closeSubmenu();
    }
  }

  function closeSubmenu(){
    $('#main-sidebar').removeClass('expand');
    $('#submenu').removeClass('open');
    $('#submenu-title').text('');
    $('#submenu-content').empty();
  }

  // Hover: abre submenu se existir
  $('#menu-list').on('mouseenter', 'li.menu-item', function(){
    openSubmenu($(this));
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
