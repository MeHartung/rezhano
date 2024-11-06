/**
 * Created by Dancy on 14.09.2017.
 */
define(function(require){
  return '\
  Показано <%= start %> - <%= end %> из <%= total %>\
  <% if (counters.length > 1) { %>\n\
  <select class="inputbox per-page-select">\n\
    <% for (var i in counters) { %>\
      <option value="<%= counters[i] %>" <% if (per_page == counters[i] ) { %>selected<% } %>><%= counters[i] %></option>\n\
    <% } %>\n\
  </select>\n\
  <% } %>\n\
';
});