/**
 * Created by Денис on 06.06.2017.
 */
define(function(require){
  var Backbone = require('backbone'),
      $ = require('jquery'),
      CartDialogView = require('view/cart/cart-dialog');

  require('lib/string')

  var template = _.template('\
    <span class="header-menu__info-amount-item"><span class="card-icon"></span><%= quantity %></span>\
    <span class="header-menu__info-amount-payment"><%= total %></span>\
');

  return Backbone.View.extend({
    events: {
      'click': 'onClick'
    },
    id: "btnCart",
    initialize: function(){
      this.cartDialog = null;
      // $(window).on('scroll.'+this.cid, $.proxy(this.onWindowScroll, this));
      this.listenTo(this.model, 'add', this.render);
      this.listenTo(this.model, 'remove', this.render);
      this.listenTo(this.model, 'change:quantity', this.render);
    },
    render: function(){
      this.$el.html(template({
        quantity: this.model.items.length,
        total: Number(this.model.get('total')).toCurrencyString(),
      }));

      return this;
    },
    onClick: function(){
      var self = this;

      this.$el.addClass('loading');

      this.model.items
                .fetch()
                .always(function(){
                  self.$el.removeClass('loading');
                })
                .done(function(){
                  // self.openCartDialog()
                });

      // var btn = document.id('btnCart');
      //
      // popup_cart = document.id('gkPopupCart');
      // popup_cart.setStyle('display', 'none');
      // popup_cart_h = popup_cart.getElement('.gkPopupWrap').getSize().y;
      // popup_cart_fx = new Fx.Morph(popup_cart, {duration:200, transition: Fx.Transitions.Circ.easeInOut}).set({'opacity': 0, 'height': 0 });
      // var wait_for_results = true;
      // var wait = false;
      //
      // document.id('btnCart').addEvent('click', function(e) {
      //   new Event(e).stop();
      //
      //   if(!wait) {
      //     new Request.HTML({
      //       url: $GK_URL + 'index.php?tmpl=cart',
      //       onRequest: function() {
      //         document.id('btnCart').addClass('loading');
      //         wait = true;
      //       },
      //       onComplete: function() {
      //         var timer = (function() {
      //           if(!wait_for_results) {
      //             popup_overlay.fade(0.45);
      //             popup_cart_fx.start({'opacity':1, 'height': popup_cart_h});
      //             opened_popup = 'cart';
      //             wait_for_results = true;
      //             wait = false;
      //             clearInterval(timer);
      //             document.id('btnCart').removeClass('loading');
      //           }
      //         }).periodical(200);
      //       },
      //       onSuccess: function(nodes, xml, text) {
      //         document.id('gkAjaxCart').innerHTML = text;
      //         popup_cart.setStyle('display', 'block');
      //         popup_cart_h = popup_cart.getElement('.gkPopupWrap').getSize().y;
      //         popup_cart_fx = new Fx.Morph(popup_cart, {duration:200, transition: Fx.Transitions.Circ.easeInOut}).set({'opacity': 0, 'height': 0 });
      //         wait_for_results = false;
      //         wait = false;
      //       }
      //     }).send();
      //   }
      // });
    },
    openCartDialog: function(){
      if (null === this.cartDialog){
        this.cartDialog = new CartDialogView({
          model: this.model
        });
        $('body').append(this.cartDialog.$el);
      }
      this.cartDialog.render();
      this.cartDialog.open();
    },
    // onWindowScroll: function(){
    //   var scroll = $(window).scrollTop();
    //   var max = $('#gkMainWrap').height();
    //   var final = 0;
    //   if(scroll > 70) {
    //     if(scroll < max - 122) {
    //       final = scroll;
    //     } else {
    //       final = max - 172;
    //     }
    //   } else {
    //     final = 50;
    //   }
    //
    //   this.$el.css('top', final + "px");
    // }
  });
});