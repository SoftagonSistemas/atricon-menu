jQuery(function($){
  // Função para abrir o submenu do item de menu
  function openSubmenu($menuItem) {
    const $submenu = $menuItem.children('ul.sub-menu');
    const title = $menuItem.children('a').find('.label').text().trim() || $menuItem.children('a').text().trim();
    
    // Só abre a sidebar se houver subitens
    if ($submenu.length > 0) {
      $('#submenu-title').text(title);
      $('#submenu-content').empty();
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
    }
    // Se não houver subitens, não faz nada (não chama closeSubmenu)
  }

  function closeSubmenu(){
    $('#main-sidebar').removeClass('expand');
    $('#submenu').removeClass('open');
    $('#submenu-title').text('');
    $('#submenu-content').empty();
  }
  
  // Hover: abre submenu se existir (apenas para itens de nível superior)
  $('#menu-list').on('mouseenter', '> li.menu-item-top', function(){
    const $menuItem = $(this);
    const $submenu = $menuItem.children('ul.sub-menu');
    
    // Só chama openSubmenu se o item realmente tiver subitens
    if ($submenu.length > 0) {
      openSubmenu($menuItem);
    }
  });
  
  // Fecha submenu quando sair do container inteiro
  $('#menu-container').on('mouseleave', function(e) {
    // Verifica se realmente saiu do container (não apenas mudou entre elementos filhos)
    if (!$(e.relatedTarget).closest('#menu-container').length) {
      closeSubmenu();
    }
  });

  // Busca (apenas nos itens de nível superior)
  $('#menu-search').on('input', function(){
    const term = $(this).val().toLowerCase();
    $('#clear-search').toggle(!!term);
    $('#menu-list > li.menu-item-top').each(function(){
      const txt = $(this).children('a').text().toLowerCase();
      $(this).toggle(txt.includes(term));
    });
  });
  $('#clear-search').on('click', function(){
    $('#menu-search').val('').trigger('input').focus();
  });
});
