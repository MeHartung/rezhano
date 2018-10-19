/* 
 * @author Denis N. Ragozin <ragozin at artsofte.ru>
 * @version SVN: $Id$
 * @revision SVN: $Revision$
 */
define(function(require){
  var Backbone = require('backbone'),
      ShippingChoiceCollection = require('model/shipping/shipping-choice-collection'),
      CourierShippingUnavailableView = require('view/checkout/shipping/courier-shipping-unavailable-view'),
      CourierShippingChoiceListView = require('view/checkout/shipping/shipping-choice-list-view'),
      ListView = require('view/base/list-view');

  require('lib/string');

  var template = _.template(require('templates/checkout/shipping/shipping-panel'));

  return Backbone.View.extend({
    events: {
//      'click .delivery-tabs a': 'onDeliveryTabClick',
      'change #delivery-tab-courier input': 'onCourierChoiceChange',
      'change .shipping-method-choice input': 'onItemSelect',
      'click .embedded-calculator-link': 'onEmbeddedCalculatorLinkClick'
    },
    initialize: function(options) {
      this.options = options;
      this.nbMethods = 0;
      this.nbMethodsLoaded = 0;
      
      this.collection.on('fetch:start', this.showLoader, this);
      this.collection.on('fetch:end', this.hideLoader, this);

      var shippingMethodListLoaded = this.collection.isLoaded;
      if (!shippingMethodListLoaded){
        this.collection.on('fetch:end', this.onShippingMethodCollectionFetchEnd, this);
      }
      if (shippingMethodListLoaded && !this.collection.length){         
        this.courierShippingUnavailableView = new CourierShippingUnavailableView();
      } else {
        this.courierShippingChoices = new ShippingChoiceCollection();

        this.courierShippingChoices.on('add remove reset', this.onCourierShippingChoiceCollectionChange, this);
        this.courierShippingChoices.on('change:cost', this.onCourierShippingChoiceCostChange, this);

        this.courierChoiceListView = new CourierShippingChoiceListView({
          collection: this.courierShippingChoices,
          loading: true
        });
      }
      if (shippingMethodListLoaded){
        this.onShippingMethodCollectionFetchEnd();
      }
    },
    render: function(){
      var errorMessage = this.$('#shipping-method-errors').html() || '';

      this.$el.html(template({}));
      if (this.courierChoiceListView){
        this.courierChoiceListView.render().$el.appendTo(this.$el);
      }
      if (this.courierShippingUnavailableView){
        this.courierShippingUnavailableView.render().$el.appendTo(this.$el).show();
      }

      this.$el.append($('<div id="shipping-method-errors">'+errorMessage+'</div>'));

      return this;
    },
    showLoader: function() {
      var loader = $(this.options.loader);
      
      if(loader) {
        loader.show();
      }
    },
    hideLoader: function() {
      var loader = $(this.options.loader);
      
      if(loader) {
        loader.hide();
      }  
    },
    _createItemView: function(item){
      var view = Backbone.CollectionView.prototype._createItemView.apply(this, arguments);
      
      this.listenTo(view, 'item:selected', this.onItemSelect, this);
      
      return view;
    },
    onItemSelect: function(event){
      var $val = this.$el.find('input:checked').val(),
          shippingMethod = this.courierShippingChoices.get($val);

      if (shippingMethod){
        this.model.set({
          shipping_method_id: $val,
          shipping_cost: shippingMethod.get('cost')
        });
      } else {
        this.model.set({
          shipping_method_id: null,
          shipping_cost: null
        })
      }
    },
    onEmbeddedCalculatorLinkClick: function(e){
      var lnk = $(e.currentTarget),
          self = this;
      
      e.preventDefault();
      
      if (null === this.embeddedCalculatorDialog){
        this.embeddedCalculatorDialog = $('<div class="embedded-calculator-layer layer popup" style="display:none;"></div>').html('\n\
<div class="layer-wrap"><a href="#" class="close-btn"><img width="20" height="21" src="/images/close_layer.png" alt="X"></a><p class="layer-title">'+lnk.attr('data-title')+'</p><div class="layer-content"></div></div>');
        $('body').append(this.embeddedCalculatorDialog);
        
        this.embeddedCalculatorDialog.isOpening = false;
        
        this.embeddedCalculatorDialog.layer();        
        this.embeddedCalculatorDialog.on('hide', function(e){
          if (self.embeddedCalculatorDialog.isOpening){
            e.preventDefault();
            self.embeddedCalculatorDialog.isOpening = false;
          }
        });
      }
      
      this.embeddedCalculatorDialog.isOpening = true;
      this.embeddedCalculatorDialog.show();
      
      var layerInner = this.embeddedCalculatorDialog.find('.layer-wrap'),
          layerContent = this.embeddedCalculatorDialog.find('.layer-content');
      
      layerContent.html(lnk.attr('data-calculator').base64Decode());
      layerInner.css({
        width: layerInner.find('iframe').width(),        
      });
      layerContent.css({
        height: layerInner.find('iframe').height()
      });
      this.embeddedCalculatorDialog.css({
        top: ($(window).height() - this.embeddedCalculatorDialog.height()) / 2 + $(window).scrollTop(),
        left: ($(window).width() - this.embeddedCalculatorDialog.width()) / 2 + $(window).scrollLeft()
      });
    },
    onShippingMethodCollectionFetchEnd: function(){
      var self = this;

      this.$('#delivery-method-loader').hide();

      this.nbMethods = this.collection.length;
      this.courierShippingChoices.reset();
      this.nbMethodsLoaded = 0;
      if (this.collection.length){
        this.collection.each(function(method){
          self.listenTo(method, 'choices:add', self._onShippingChoiceListAdd, self);
          self.listenTo(method, 'choices:reset', self._onShippingChoiceListUpdate, self);
          self.listenTo(method, 'choices:loaded', self._onShippingChoiceListLoaded, self);

        method.fetchShippingChoices();
        });
      }
    },
    _onShippingChoiceListAdd: function(choice){
      this.courierShippingChoices.add(choice);
    },
    _onShippingChoiceListUpdate: function(choices){
        this.courierShippingChoices.set(choices.models, {remove: false});
    },
    _onShippingChoiceListLoaded: function(method){
      this.nbMethodsLoaded++;

      if (this.nbMethodsLoaded >= this.nbMethods){
        this._hideLoaders();
        
        this.trigger('Loaded');

        var shippingMethodId = this.model.get('shipping_method_id');
        if (shippingMethodId){
          this.courierShippingChoices.selectItem(shippingMethodId);
        }
      }
    },
    /**
     * Скрывает загрузчики на вкладках
     * 
     * @param Array idx
     * @returns {undefined}
     */
    _hideLoaders: function(idx){
      $('.shipping-ajax-loader').hide();
      // var viewMap = [this.pickupChoiceListView, this.courierChoiceListView],
      //     _idx = ('undefined' !== typeof idx && idx.length) ? idx : [0, 1],
      //     self = this;
      //
      // _.each(_idx, function(i){
      //   self.tabs.at(i).set('loading', false);
      //  /*
      //   * Не будем особо заморачиваться с проверками типов. Настоящие представления коллекции списка вариантов сопобов
      //   * доставки будут иметь функцию setLoading, которая будет отключать лоадер. Если эта функция есть, вызовем ее.
      //   */
      //   if ($.isFunction(viewMap[i].setLoading)){
      //     viewMap[i].setLoading(false);
      //   }
      // });
    },
    onCourierShippingChoiceCostChange: function(choice){
      if (choice.get('id') === this.model.get('shipping_method_id')){
        this.model.set({
          shipping_cost: choice.get('cost')
        })
      }
    }
  });
});

