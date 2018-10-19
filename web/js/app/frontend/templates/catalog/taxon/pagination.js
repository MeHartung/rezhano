define(function () {
  return '\
<% if(pages.last > 1) { %>\n\
    <% if(pages.current > 1) { %>\n\
      <a class="pagination__prev" href="<%= pages.links[pages.current - 1]%>">Назад</a>\n\
    <% } else { %>\
      <span class="pagination__prev">Назад</span>\n\
    <% } %>\
    <div class="pagination__wrap-item">\
    <% $.each(pages.links, function(page, url) { %>\n\
       <% if(page == pages.current) { %>\n\
         <span class="pagination__item"><%= page %></span>\n\
       <% } else if (url !== undefined) { %>\n\
         <a href="<%= url %>" class="pagination__item"><%= page %></a>\n\
       <% } %>\n\
     <% }); %>\
    </div>\n\
    <% if(pages.current < pages.last) { %>\n\
        <a class="pagination__next" href="<%= pages.links[pages.current + 1]%>">Вперед</a>\n\
    <% } else { %>\
        <span class="pagination__next">Вперед</span>\n\
    <% } %>\
    <span class="pagination__show-count">Показывать по 16</span>\n\
<% } %>';
});