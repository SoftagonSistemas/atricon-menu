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

jQuery(function($){
  // Função para criar índice de busca otimizado
  function createSearchIndex() {
    if (searchIndex) return searchIndex;
    
    searchIndex = {
      items: [],
      terms: new Map()
    };
    
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

  // Busca otimizada com debounce
  const debouncedSearch = debounce(function(term) {
    const normalizedTerm = normalizeText(term);
    $('#clear-search').toggle(!!term);
    
    if (!term) {
      // Se não há termo de busca, mostra todos os itens principais
      $('#menu-list > li.menu-item-top').show();
      closeSubmenu();
      // Remove todos os destaques
      $('.search-highlight').removeClass('search-highlight');
      // Esconde contador de resultados
      $('#search-results-count').hide();
      return;
    }
    
    const searchResult = performSearch(term);
    
    // Esconde todos os itens primeiro
    $('#menu-list > li.menu-item-top').hide();
    
    // Mostra apenas os itens com resultados
    searchResult.results.forEach(index => {
      searchIndex.items[index].element.show();
    });
    
    // Se encontrou um termo em um subitem, abre automaticamente o submenu correspondente
    if (searchResult.bestMatch !== null && term.length >= 2) {
      const bestItem = searchIndex.items[searchResult.bestMatch];
      openSubmenu(bestItem.element);
      
      // Destaca os subitens que correspondem à busca
      setTimeout(function() {
        $('#submenu-content a').each(function() {
          const $link = $(this);
          const linkText = $link.text().toLowerCase();
          const normalizedLinkText = normalizeText(linkText);
          
          if (normalizedLinkText.includes(normalizedTerm)) {
            $link.addClass('search-highlight');
          } else {
            $link.removeClass('search-highlight');
          }
        });
      }, 150);
    } else if (searchResult.results.length === 0) {
      closeSubmenu();
    }
    
    // Mostra contador de resultados
    if (searchResult.totalMatches > 0) {
      const resultText = searchResult.totalMatches === 1 ? '1 resultado encontrado' : `${searchResult.totalMatches} resultados encontrados`;
      $('#search-results-count').text(resultText).show();
    } else {
      $('#search-results-count').text('Nenhum resultado encontrado').show();
    }
  }, 150); // Debounce de 150ms

  $('#menu-search').on('input', function(){
    const term = $(this).val().trim();
    debouncedSearch(term);
  });

  $('#clear-search').on('click', function(){
    $('#menu-search').val('').trigger('input').focus();
    // Remove todos os destaques
    $('.search-highlight').removeClass('search-highlight');
    // Esconde contador de resultados se existir
    if ($('#search-results-count').length) {
      $('#search-results-count').hide();
    }
  });
  
  // Função utilitária para destacar texto
  function highlightText(text, term) {
    if (!term) return text;
    const regex = new RegExp(`(${term})`, 'gi');
    return text.replace(regex, '<mark class="search-term">$1</mark>');
  }
  
  // Inicializa o índice de busca quando o DOM estiver pronto
  $(document).ready(function() {
    createSearchIndex();
  });
});
