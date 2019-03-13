/**
 * Created by Денис on 06.06.2017.
 */
define(function(require){
  var Backbone = require('backbone'),
      $ = require('jquery'),
      AddToCartSuccessLayerView = require('view/cart/add-to-cart-success-layer-view'),
      AddToCartInvalidLayerView = require('view/cart/add-to-cart-invalid-layer-view'),
      CartWidgetView = require('view/cart/widget'),
      CatalogSearchFormView = require('view/catalog/search/catalog-search-form-view'),
      //CitySelectLinkView = require('view/common/header/city-select-link'),
      UserPanelView = require('view/user/user-panel-view'),
      //Location = require('model/geography/location')
      MapViewDialog = require('view/common/map-view-dialog'),
      QuestionDialogView = require('view/common/question-view-dialog');

  var device = require('current-device').default;

  return Backbone.View.extend({
    events: {
      'click .button-question': 'onQuestionClick',
      'click .footer-maps__link' : 'onAddressClick',
      'click .section-see-works__video-play-overlay' : 'onAboutVideoPlay',
      'click .cmn-toggle-switch' : 'onShowMobileMenu',
      'click .homepage_top' : 'scrollTopHomePage'
    },
    initialize: function(options){
      this.options = $.extend({
        cartWidget: true,
        search: false
      }, options);
      this.cart = options.cart;
      this.user = options.user;

      this.stores = new Backbone.Collection(ObjectCache.Stores || {});

      this.playAboutVideo = false;
      this.addToCartSuccessLayer = null;
      this.addToCartInvalidLayerView = null;

      if (this.options.cartWidget){
        this.cartWidget = new CartWidgetView({
          model: this.cart,
          el: $('.header-controls__cart')
        });
      }

      $('#reviewsSliders').slick({
        dots: true,
        arrows: true,
        infinite: true,
        speed: 500,
        fade: true,
        cssEase: 'linear'
      });

      $('#oneEyeSlider').on('init', function (e, slick) {}).slick({
        dots: true,
        infinite: true,
        centerPadding: '120px'
      });

      $('#productionSlider').on('init', function (e, slick) {}).slick({
        dots: true,
        infinite: true,
        centerPadding: '120px'
      });

      // this.location = new Location(ObjectCache.Location);

      // this.citySelectLinkView = new CitySelectLinkView({
      //   model: this.location
      // });

      this.userPanelView = new UserPanelView({
        model: this.user
      });

      this.questionDialogView = null;

      this.listenTo(this.cart, 'item:add', this.onCartItemAdded);
      this.listenTo(this.cart, 'item:invalid', this.onCartItemInvalid);
    },
    onCartItemAdded: function(model, quantity){
      if (this.addToCartSuccessLayer){
        this.addToCartSuccessLayer.dispose();
        this.addToCartSuccessLayer = null;
      }
      this.addToCartSuccessLayer = new AddToCartSuccessLayerView({
        model: model,
        quantity: model._previousAttributes.quantity
        // quantity: quantity
      });
      this.addToCartSuccessLayer.$el.appendTo($('body'));
      this.addToCartSuccessLayer.render();
      this.addToCartSuccessLayer.open();

      window.dataLayer = window.dataLayer || [];

      window.dataLayer.push({
        'event': 'addToCart',
        ecommerce: {
          currencyCode: 'RUB',
          add: {
            products: [model.toGaJson({
              quantity: quantity
            })]
          }
        }
      });
    },
    onAddressClick: function(e) {
      var self = this;
      var currentStore = self.stores.where({fullAddress:$(e.currentTarget).attr('data-address')})[0];
      this.mapViewDialog = new MapViewDialog({
        model: new Backbone.Model({
          address: $(e.currentTarget).attr('data-address'),
          store: currentStore.attributes
        }),
      });
      this.mapViewDialog.render().$el.appendTo($('body'));

      this.mapViewDialog.open();
    },
    onCartItemInvalid: function (item, product) {
      if (this.addToCartInvalidLayerView){
        this.addToCartInvalidLayerView.dispose();
        this.addToCartInvalidLayerView = null;
      }
      this.addToCartInvalidLayerView = new AddToCartInvalidLayerView({
        cartItem: item,
        cart: this.cart,
        quantity: item.get('quantity'),
        product: product
      });
      this.addToCartInvalidLayerView.$el.appendTo($('body'));
      this.addToCartInvalidLayerView.render();
      this.addToCartInvalidLayerView.open();
    },
    render: function(){
      var self = this;

      if (this.options.cartWidget) {
        this.cartWidget.render();
      }

      if (this.options.search) {
        this.searchFormView = new CatalogSearchFormView({
          el: this.$('.header-menu__input')
        });
      }
      // this.citySelectLinkView.setElement(this.$('#cityselection')).render();

      this.userPanelView.setElement(this.$('.header-panel__sign')).render();

      _.forEach(this.$('.member-club'), function (item) {
        new UserPanelView({
          el: $(item),
          dialog: self.userPanelView
        }).render();
      });



      var $headerMenu = $('.header__fixed');
      var headerMenuHeight = $('.header__fixed').height();
      var $html = $('html');
      // this.st = $(this).scrollTop();
      this.lastScrollTop = 5;

      if (navigator.userAgent.match(/(iPod|iPhone|iPad|Android)/) && self.st <= 0 ) {

        $(window).on("touchstart", function (event) {
          if ( $html.hasClass('mobile') || $html.hasClass('tablet') ) {
            self.st = $(this).scrollTop();
            // console.log(self.st, self.lastScrollTop);
            if (navigator.userAgent.match(/(iPod|iPhone|iPad|Android)/) && self.st <= 0 ) {
              setTimeout(function () {
                window.scrollTo(0,0);
              }, 200);
            }
            if (self.st > self.lastScrollTop){
              $headerMenu.addClass('header__mobile');
            } else {
              $headerMenu.removeClass('header__mobile');
            }
            self.lastScrollTop = st;

            if ( $(this).scrollTop()>2 ) {
              $headerMenu.addClass('fixed');
            } else if ( $(this).scrollTop()<headerMenuHeight) {
              $headerMenu.removeClass('fixed');
            }

          } else {
            if ( $(this).scrollTop()>2 ) {
              $headerMenu.addClass('fixed');
            } else if ( $(this).scrollTop()<headerMenuHeight) {
              $headerMenu.removeClass('fixed');
            }
          }
        });
      } else {

        $(window).scroll(function (event) {
          self.st = $(this).scrollTop();
          if ( $html.hasClass('mobile') || $html.hasClass('tablet') ) {
            // console.log(self.st, self.lastScrollTop);
            if (self.st > self.lastScrollTop){
              $headerMenu.addClass('header__mobile');
            } else {
              $headerMenu.removeClass('header__mobile');
            }
            self.lastScrollTop = self.st;

            if ( $(this).scrollTop()>2 ) {
              $headerMenu.addClass('fixed');
            } else if ( $(this).scrollTop()<headerMenuHeight) {
              $headerMenu.removeClass('fixed');
            }

          } else {
            if ( $(this).scrollTop()>2 ) {
              $headerMenu.addClass('fixed');
            } else if ( $(this).scrollTop()<headerMenuHeight) {
              $headerMenu.removeClass('fixed');
            }
          }
        });
      }




      return this;
    },
    onQuestionClick: function (e) {
      e.preventDefault();

      $('body').css({
        'overflow': 'hidden'
      });

      this.questionDialogView = new QuestionDialogView({

      });
      this.questionDialogView.render().$el.appendTo($('body'));

      this.questionDialogView.open();
    },
    onAboutVideoPlay: function (e) {
      e.preventDefault();
      var self = this;

      if (!this.playAboutVideo) {
        this.playAboutVideo = true;
        this.$('.section-see-works__video video').get(0).play();
        this.$('.section-see-works__video').addClass('show-controls');
        this.$('.section-see-works__video video').prop("controls","controls");
        this.$('.section-see-works__video-play').fadeOut();



        this.$('.section-see-works__video-play-overlay').css('z-index', 0)
      } else {
        this.playAboutVideo = false;
        this.$('.section-see-works__video video').get(0).pause();
        // this.$('.section-see-works__video').addClass('.show-controls');
        this.$('.section-see-works__video video').prop("controls", null);
        this.$('.section-see-works__video-play').fadeIn();

        this.$('.section-see-works__video-play-overlay').css('z-index', 3)
      }

      this.$('.section-see-works__video video').bind('ended', function () {
        self.playAboutVideo = false;
        self.$('.section-see-works__video-play').fadeIn();
        self.$('.section-see-works__video').removeClass('.show-controls');
        self.$('.section-see-works__video video').prop("controls", null);

        self.$('.section-see-works__video-play-overlay').css('z-index', 3)
      })
    },
    onShowMobileMenu: function (e) {
      e.preventDefault();

      $(e.currentTarget).toggleClass('active');
      $(e.currentTarget).parent().find('.header-mobile').toggleClass('active');

      var bTop = $('body');
      console.log(bTop, $(this).scrollTop());
      $('body').toggleClass('no-scroll');

    },
    scrollTopHomePage: function (e) {
      e.preventDefault();

      $("html, body").animate({
        scrollTop: 0
      }, 500)
    }
  });
});