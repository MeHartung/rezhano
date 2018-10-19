/* 
 *  @author Denis N. Ragozin <dragozin at accurateweb.ru>
 */
define(function(require){
  var Backbone = require('backbone');

  require('jquery-ui/widgets/autocomplete');

  return Backbone.View.extend({
    tagName: 'form',
    events: {
      'keyup input': 'onSearchInputKeyup',
      'change input': 'onSearchInputChange',
      'click #search-area-close': 'onResetClick',
      'submit': 'onSearchSubmit'
    },
    initialize: function(){
      this.$search = this.element = this.$('input');
      this.$reset = this.$('#search-area-close');
      this.detailingsLinkClicked = false;

      var self = this,
          sectionTitle = true,
          productTitle = true,
          sparesTitle = true,
          detailingsTitle = true;

      this.xhrs = [];

      this.requestTimeoutIntercal = setInterval(function(){
        self.abortTimedoutRequests();
      }, 500);

      self.$search.autocomplete({
        source: function(request, response){
          var sid_select = self.$('*[name="sid"]'),
              val = $.trim(self.$search.val());
          if (val.length){
            self.abortTimedoutRequests();
            var autocompleteStartTime = (new Date()).getTime();
            var xhr = $.get(urlPrefix + '/search/suggest?term=' + encodeURIComponent(val) + '&sid=' + encodeURIComponent(sid_select.length ? sid_select.val() : 'all'),
              response)
              .done(function () {
                var autocompleteEndTime = (new Date()).getTime();
                // window.dataLayer = window.dataLayer || [];
                // window.dataLayer.push({
                //   event: "catalogSearchAutocompleteSuccess",
                //   time: autocompleteEndTime - autocompleteStartTime,
                //   version: ObjectCache.Application[0].version
                // });
              })
              .always(function(xhr){
                var idx = -1;
                for (var i=0;i<self.xhrs.length;i++){
                  if (self.xhrs[i].xhr.__requestId === xhr.__requestId){
                    idx = i; break;
                  }
                }

                if (idx >= 0){
                  self.xhrs.splice(idx, 1);
                }
            });
            xhr.__requestId = ++self.xhrCounter;
            self.xhrs.push({
              xhr: xhr,
              time: autocompleteStartTime
            });
          }
        },
        appendTo: this.element.parent(),
        minLength: 3,
        
        select: function( event, ui ) {
            if (typeof(ui.item) !== 'undefined'){
                window.location.href = ui.item.url;
            } else{
              window.open(urlPrefix + '/search?q=' + encodeURIComponent(self.$search.val()), '_blank');
            }
        }
      });

      self.$search.autocomplete("instance")._renderMenu = function(ul, items) {
        var self2 = this;
        $.each( items, function( index, item ) {
          if (sectionTitle && item.type == 'taxon') {
            ul.append("<li class='grey small'>Разделы каталога</li>");
            sectionTitle = false;
          } else if (productTitle && item.type == 'products') {
            ul.append("<li class='grey small'>Товары</li>");
            productTitle = false;
          } else if (item.type == 'all-results') {
            ul.addClass(item.type);
          }
          if (item.type == 'no-result'){
            ul.append("<li class=\"small grey\">Нет результатов</li>");
          } else {
            self2._renderItem( ul, item );
          }
        });
        sectionTitle = productTitle = sparesTitle = detailingsTitle = true;

        $(ul).addClass(self.element.attr('id'));
      };

      self.$search.autocomplete("instance")._renderItem = function(ul, item)
      {
        var element = $( "<li class='autocomplete-item' ></li>" );

        if (item.type == 'separate') {
          element.addClass("suggest-separate");
        }
        
        element.data("ui-autocomplete-item", item).append(item.label).appendTo(ul);

        return element;
      };      
      
      $(document).on('keypress', function(e)
      {
        var $target = $(e.target);

        if (e.which > 0x19 && !$target.is('input') && !$target.is('textarea') && !$target.is('[contenteditable]')){
          var val = self.$search.val();
          if (val.length){
            val += ' ';
            self.$search.val(val);
          }
          
          self.$search.focus();
        }
      });
    },
    _searchInputChanged: function(){
       if (this.$search.val() != '') {
        this.$reset.fadeIn(150);
      } else {
        this.$reset.fadeOut(150);
      }
    },
    onSearchInputKeyup: function(){
      this._searchInputChanged();
    },
    onSearchInputChange: function(){
      this._searchInputChanged();
    },
    onResetClick: function(){
      this.$search.val('');
      this.$reset.fadeOut(150);
    },
    /**
     * Сбрасывает запросы автодополнения, которые выполняются дольше двух секунд, кроме самого последнего запроса
     */
    abortTimedoutRequests: function(){
      var self = this;
      if (self.xhrs.length > 1){
        for (var i=0;i<self.xhrs.length;i++){
          if ((new Date()).getTime() - self.xhrs[i].time > 2000 && i < self.xhrs.length - 1){
            self.xhrs[i].xhr.abort();
          }
        }
      }
    },
    onSearchSubmit: function(e){
      if (!this.$search.val().length){
        e.preventDefault();
      }
    }
  });
});

