// Função para normalizar texto (remove acentos) - com cache
const textCache = new Map();
function normalizeText(text) {
  if (textCache.has(text)) {
    return textCache.get(text);
  }
  const normalized = text.normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase();
  textCache.set(text, normalized);
  return normalized;
}

// Cache para estrutura de busca
let searchIndex = null;

// Debounce function para otimizar busca
function debounce(func, wait) {
  let timeout;
  return function executedFunction(...args) {
    const later = () => {
      clearTimeout(timeout);
      func(...args);
    };
    clearTimeout(timeout);
    timeout = setTimeout(later, wait);
  };
}

jQuery(function($){  // Função para criar índice de busca otimizado
  function createSearchIndex() {
    if (searchIndex) return searchIndex;
    
    searchIndex = {
      items: [],
      terms: new Map()
    };
    
    // Garante que todos os itens estejam visíveis antes de indexar
    $('#menu-list > li.menu-item-top').each(function(){
      $(this).removeClass('hidden').show();
    });
    
    $('#menu-list > li.menu-item-top').each(function(index) {
      const $menuItem = $(this);
      const mainText = $menuItem.children('a').text().trim();
      const normalizedMainText = normalizeText(mainText);
      const $submenu = $menuItem.children('ul.sub-menu');
      
      const item = {
        index: index,
        element: $menuItem,
        mainText: mainText,
        normalizedMainText: normalizedMainText,
        subItems: []
      };
      
      // Indexa subitens
      $submenu.children('li.menu-item').each(function(subIndex) {
        const $subItem = $(this);
        const subText = $subItem.children('a').text().trim();
        const normalizedSubText = normalizeText(subText);
        
        item.subItems.push({
          index: subIndex,
          element: $subItem,
          text: subText,
          normalizedText: normalizedSubText
        });
      });
      
      searchIndex.items.push(item);
      
      // Indexa termos para busca rápida
      const words = normalizedMainText.split(/\s+/);
      words.forEach(word => {
        if (word.length > 2) {
          if (!searchIndex.terms.has(word)) {
            searchIndex.terms.set(word, []);
          }
          searchIndex.terms.get(word).push(index);
        }
      });
      
      item.subItems.forEach(subItem => {
        const subWords = subItem.normalizedText.split(/\s+/);
        subWords.forEach(word => {
          if (word.length > 2) {
            if (!searchIndex.terms.has(word)) {
              searchIndex.terms.set(word, []);
            }
            searchIndex.terms.get(word).push(index);
          }
        });
      });
    });
    
    return searchIndex;
  }

  // Função de busca otimizada
  function performSearch(term) {
    if (!searchIndex) {
      createSearchIndex();
    }
    
    const normalizedTerm = normalizeText(term);
    if (normalizedTerm.length < 2) {
      return { results: [], totalMatches: 0 };
    }
    
    const results = new Set();
    const matches = [];
    
    // Busca por termo exato primeiro
    if (searchIndex.terms.has(normalizedTerm)) {
      searchIndex.terms.get(normalizedTerm).forEach(index => {
        results.add(index);
      });
    }
    
    // Busca por substring em todos os itens
    searchIndex.items.forEach((item, index) => {
      let itemMatches = 0;
      
      // Verifica item principal
      if (item.normalizedMainText.includes(normalizedTerm)) {
        results.add(index);
        itemMatches++;
      }
      
      // Verifica subitens
      item.subItems.forEach(subItem => {
        if (subItem.normalizedText.includes(normalizedTerm)) {
          results.add(index);
          itemMatches++;
        }
      });
      
      if (itemMatches > 0) {
        matches.push({ index, matches: itemMatches });
      }
    });
    
    return {
      results: Array.from(results),
      totalMatches: matches.reduce((sum, m) => sum + m.matches, 0),
      bestMatch: matches.length > 0 ? matches.reduce((best, current) => 
        current.matches > best.matches ? current : best
      ).index : null
    };
  }

  // Função para abrir o submenu do item de menu
  function openSubmenu($menuItem) {
    const $submenu = $menuItem.children('ul.sub-menu');
    const title = $menuItem.children('a').find('.label').text().trim() || $menuItem.children('a').text().trim();

    // Só expande a sidebar e mostra o título se houver subitens válidos
    if ($submenu.length > 0 && $submenu.children('li.menu-item').length > 0) {
      $('#submenu-title').text(title);
      $('#main-sidebar').addClass('expand');
      $('#submenu').addClass('open');
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
    } else {
      // Se não há subitens, limpa o conteúdo e não expande o sidebar
      $('#submenu-title').text('');
      $('#submenu-content').empty();
      $('#main-sidebar').removeClass('expand');
      $('#submenu').removeClass('open');
    }
  }

  function closeSubmenu(){
    $('#main-sidebar').removeClass('expand');
    $('#submenu').removeClass('open');
    $('#submenu-title').text('');
    $('#submenu-content').empty();
  }
  
  function showDefaultSubmenuMessage(title) {
    $('#submenu-title').text(title);
    $('#main-sidebar').addClass('expand');
    $('#submenu').addClass('open');
    $('#submenu-content').html('<div class="submenu-default-message">Clique em <b>' + title + '</b> para acessar o conteúdo.</div>');
  }

  let submenuShouldClose = null;

  $('#menu-list').on('mouseenter', '> li.menu-item-top', function(){
    $('#menu-list > li.menu-item-top').removeClass('menu-item-hover');
    const $menuItem = $(this).addClass('menu-item-hover');
    const $submenu = $menuItem.children('ul.sub-menu');
    const title = $menuItem.children('a').find('.label').text().trim() || $menuItem.children('a').text().trim();
    if ($submenu.length > 0 && $submenu.children('li.menu-item').length > 0) {
      if (submenuShouldClose) {
        clearTimeout(submenuShouldClose);
        submenuShouldClose = null;
      }
      openSubmenu($menuItem);
    } else {
      if (submenuShouldClose) {
        clearTimeout(submenuShouldClose);
        submenuShouldClose = null;
      }
      showDefaultSubmenuMessage(title);
    }
  });
  
  $('#menu-list').on('mouseleave', '> li.menu-item-top', function(){
    $(this).removeClass('menu-item-hover');
  });
  
  $('#menu-container').on('mouseleave', function(e) {
    if (!$(e.relatedTarget).closest('#menu-container').length) {
      closeSubmenu();
      $('#menu-list > li.menu-item-top').removeClass('menu-item-hover');
    }
  });
  // Busca otimizada com debounce (reescrita)
  const debouncedSearch = debounce(function(term) {
    const normalizedTerm = normalizeText(term);
    $('#clear-search').toggle(!!term);
    $('.search-highlight').removeClass('search-highlight');
    closeSubmenu();

    if (!term) {
      $('#menu-list > li.menu-item-top').removeClass('hidden');
      $('#search-results-count').hide();
      return;
    }

    // Esconde todos os itens primeiro
    $('#menu-list > li.menu-item-top').addClass('hidden');

    // Busca e mostra apenas os itens que correspondem
    let totalMatches = 0;
    let bestMatchIndex = null;
    let bestMatchCount = 0;

    searchIndex.items.forEach((item, index) => {
      let matches = 0;
      if (item.normalizedMainText.includes(normalizedTerm)) matches++;
      item.subItems.forEach(subItem => {
        if (subItem.normalizedText.includes(normalizedTerm)) matches++;
      });
      if (matches > 0) {
        $('#menu-list > li.menu-item-top').eq(index).removeClass('hidden');
        totalMatches += matches;
        if (matches > bestMatchCount) {
          bestMatchCount = matches;
          bestMatchIndex = index;
        }
      }
    });

    // Se encontrou subitem, abre submenu e destaca
    if (bestMatchIndex !== null) {
      openSubmenu(searchIndex.items[bestMatchIndex].element);
      setTimeout(function() {
        $('#submenu-content a').each(function() {
          const $link = $(this);
          const normalizedLinkText = normalizeText($link.text());
          if (normalizedLinkText.includes(normalizedTerm)) {
            $link.addClass('search-highlight');
          }
        });
      }, 100);
    }

    // Mostra contador de resultados
    if (totalMatches > 0) {
      const resultText = totalMatches === 1 ? '1 resultado encontrado' : `${totalMatches} resultados encontrados`;
      $('#search-results-count').text(resultText).show();
    } else {
      $('#search-results-count').text('Nenhum resultado encontrado').show();
    }
  }, 150);

  // Evento de input
  $('#menu-search').on('input', function(){
    const term = $(this).val().trim();
    debouncedSearch(term);
  });

  // Botão limpar busca
  $('#clear-search').on('click', function(){
    $('#menu-search').val('').trigger('input').focus();
    $('#menu-list > li.menu-item-top').removeClass('hidden');
    $('#search-results-count').hide();
    closeSubmenu();
    $('.search-highlight').removeClass('search-highlight');
  });
  
  // Função utilitária para destacar texto
  function highlightText(text, term) {
    if (!term) return text;
    const regex = new RegExp(`(${term})`, 'gi');
    return text.replace(regex, '<mark class="search-term">$1</mark>');
  }
    // Inicializa o índice de busca quando o DOM estiver pronto
  $(document).ready(function() {
    // Força a exibição de todos os itens do menu inicialmente
    $('#menu-list > li.menu-item-top').each(function(){
      $(this).removeClass('hidden').show().removeAttr('style');
    });
    // Cria o índice após garantir visibilidade
    setTimeout(function() {
      createSearchIndex();
    }, 100);
  });
});
