/* 
 * @author Max D. Selezenev <selezenev at artsofte.ru>
 * @version SVN: $Id$
 * @revision SVN: $Revision$ 
 */


define(function(require){
  var Backbone     = require('backbone'),      
      CartItemView = require('view/cart/checkout/cart-item-simple'),
      CartPersistLinkView = require('view/cart/widget/cart-persist-link-view'),
      app = require('core/app');
      
  require('acommerce/view/lib/collection-view');
//  require('/js/jquery.jscrollpane.js');
  require('core-lib/utils/date.format');
      
  var discountTemplate = _.template('<% if(level > 0) { %>Скидка <%= level %>% <b>—</b><%= (amount).toCurrencyString() %><% } %>');
  var cost = _.template('Итого: <%= (cost).toCurrencyString() %>');
  
  var template  = _.template('\
  <p class="h2"><b>Ваш заказ:</b></p>\n\
  <ol class="order-list"></ol>\n\
    <% /*<p class="discount"></p>*/ %>\n\
    <p class="order-price"></p>\n\
    <p class="retutn"><a href="<%= cart_url %>"><i class="sr sr-16 sr-pencil"></i>&nbsp;<span>Изменить заказ</span></a></p>\n\
    <p class="persisted-cart-link-container"></p>\n\
 \n\
  ');
  
var OrderContentView = Backbone.CollectionView.extend({
    el: '.order-info-block',
    container: '.order-list',
    itemView: CartItemView,    
    
    events: {

    },
    initialize: function() {
      
      this.delegateEvents();

      this.isFloating = false;
      this.checkoutFormPanel = $('.ordering');
      
      this.model.on('change:discount', this.update_totals, this);
      this.model.on('change:cost', this.update_totals, this);
      Backbone.CollectionView.prototype.initialize.apply(this, arguments);
//      this.content = new OrderItemsView({
//        el: '.order-list'
//      })

      this.contentListPanel = this.$('.order-list');
      this.contentListPanelInitialHeight = this.contentListPanel.height();
      this.contentListPanel.jScrollPane({
        showArrows: true
      });

      $(window).bind('scroll', _.bind(this.onWindowScroll, this));

      this.subViews = {
        persistLinkView: new CartPersistLinkView()
      };
      this.$('.persisted-cart-link-container').append(this.subViews.persistLinkView.$el);
      this.subViews.persistLinkView.delegateEvents();
    },
            
    update_totals: function()
    {
      this.$el.find('.discount').html(discountTemplate(this.model.get('discount')));
      this.$el.find('.order-price').html(cost({cost: this.model.get('cost')}));      
    },  
    
    template: function() {
     this.$el.html(template({
       cart_url: app.url("/cart")+ '?fromPlace=checkoutPageLink&fromUrl=' + encodeURIComponent(window.location.pathname) +
          '&dateTime=' + (new Date()).format('yyyy-mm-dd HH:MM:ss')
     }));
     this.update_totals();
     
    },
    onWindowScroll: function(e){      
      if ($(window).scrollTop() + 42 > this.checkoutFormPanel.offset().top) {
        if (!this.isFloating){
          this.$el.addClass('floating-panel');
          this.isFloating = true;
        }          
//        console.log($(window).scrollTop().toString() + ' ' + this.checkoutFormPanel.offset().top.toString() + ' ' + ($(window).scrollTop() - this.checkoutFormPanel.offset().top).toString())
        this.$el.css({ top: Math.max(60, $(window).scrollTop() - this.checkoutFormPanel.offset().top + 135) });
      } else {
        if (this.isFloating){
          this.$el.removeClass('floating-panel');
          this.$el.css({ top: 'auto' });
          this.isFloating = false;
        }
      }

      var listPanel = this.$('.order-list');
      var listHeight = Math.min(this.contentListPanelInitialHeight, listPanel.height() + (this.checkoutFormPanel.offset().top + this.checkoutFormPanel.outerHeight() - this.$el.offset().top - this.$el.outerHeight(true)));
      if (listHeight != listPanel.height()){        
        listPanel.css({ 'height': listHeight });      

        var api = listPanel.data('jsp');
        if (api){
          api.reinitialise();
        }
      }
    }
  });
  
  return OrderContentView;
  
});