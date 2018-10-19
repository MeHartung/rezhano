define(function (require) {
  var Backbone = require('backbone'),
    WidgetFactory = require('view/catalog/filter/catalog-filter-widget-factory'),
    FilterField = require('view/catalog/filter/catalog-filter-field-view'),
    FilterFieldStub = require('view/catalog/filter/catalog-filter-field-stub-view');

  require('lib/string');

  var loadingResultTemplate = _.template('<%= String.formatEnding(nbproducts, ["Найдено", "Найден", "Найдено"]) %> <% if (linked) { %><a class="dashed scroll-to-top" href="#"><% } %><%= nbproducts %> <%= String.formatEnding(nbproducts, ["товаров", "товар", "товара"]) %><% if (linked) { %></a><% } %>');

  var count = 0; //Количество поставленных галочек брендов
  var brands = new Array(); //Названия брендов

  /**
   *
   */
  var FilterView = Backbone.View.extend(
    /** @lends FilterView.prototype */
    {
    defaults: {
      pushState: true
    },
    events: {
      'change .filter-widget': 'changeState',
      // 'keydown .slider-min-value, .slider-max-value': 'onSliderValueInputKeydown',
      // 'slide .filter-controll .slider' : 'changeState'
      'click .filter-remove-all': 'onResetClick'
    },
      /**
       * @class
       * @constructs
       * @param options
       */
    initialize: function (options) {
      this.fieldViews = [];
      this.lastChangedWidgetId = null;
      this.options = $.extend({}, this.defaults, options);

      this.previousUrl = null;
      this.loaderDisplayTimer = null;

      this.ajaxRequest = null;

      var self = this;

      this.initLoader();


      this.model.on('filtering-start request', this.showLoader, this);
      this.model.on('filtering-error sync', this.hideLoader, this);
      this.model.on('change:value', function (model) {
        self.onChangeValue(model);
      });

      this.listenTo(this.model, 'filtered', this.onFilterLoad, this);
      this.listenTo(this.model, 'filteringStart', this.onFilterBeforeLoad, this);

      this.locationPushed = false;

      var initialUrl = window.location.href;
      window.addEventListener('popstate',  function (e){
        /*
         * Некоторые браузеры вызывают событие onpopstate при первой загрузке страницы, что приводит к циклической перезагрузке.
         * Чтобы избежать этого введем дополнительную проверку.
         */
        if (self.locationPushed || initialUrl !== window.location.href) {
          window.location.reload(true);
        }
      });

      this.widgetFactory = new WidgetFactory(this.model.get('schema'));
    },
    onChangeValue: function(model) {
      this.lastChangedWidgetId = model.get('id');
      this.changeState();
    },
    changeState: function () {
      var data = this.model.getFilterState();
      this.sendRequest(data);
    },
    sendRequest: function (data) {
      if (this.ajaxRequest && typeof this.ajaxRequest.abort == 'function')
        this.ajaxRequest.abort();

      var self = this;
      var url = this.model.get('section').url,
        requestStartTime, requestTime;

      this.ajaxRequest = $.ajax({
        type: "GET",
        url: url,
        data: data,
        dataType: "json",
        beforeSend: function () {
          self.model.trigger('filtering-start');
          requestStartTime = (new Date).getTime();
        },
        success: function (r) {
          var requestEndTime = (new Date()).getTime();

          if (self.model.get('reload')) {
            self.model.set('reload', 0, {silent: true});

            self.$el.find('.filter-fields:first').html(r.filter_fields);
            self.model.reset(r.filter);
            self.update();

            self.model.trigger("filtered", r);

          } else {

            self.model.set("state", r.filter.state);
            self.model.set({view: r.filter.view});
            self.model.set({pagination: r.filter.pagination});

            self.model.Pager.set(r.filter.pagination);
            self.model.trigger("filtered", r);

            self.update();
          }
        },
        complete: function () {
          self.model.trigger('filtering-end');
        },
        error: function (r) {
          if (r.status == "320") {
            var data = JSON.parse(r.responseText);
            window.location.href = data.url;


          } else {

            self.model.trigger('filtering-error');
          }
        }
      });
    },
    generateId: function (name, value) {

      var val = value.replace(/[^A-Za-z0-9\:_\.\-]/g, "_");

      return name + "_" + val;
    },

    initLoader: function () {
      //this.filterDisable = $('.filter-disable:first');
      this.loaderNotice = $('<div>').attr('id', 'load-notice').attr('class', 'found-layer')
        .attr('style', 'display: none;')
        .addClass('msg-panel')
        .insertBefore($('body *:first'));

      this.loaderMessage = $('<div>').addClass('msg-notice').appendTo(this.loaderNotice);

      this.loaderNotice.on('click', '.scroll-to-top', _.bind(this.onScrollToTopLinkClick, this));
    },
    showLoader: function () {
      var $sectionFilter = $('.filter-section[data-field-id ='+ this.lastChangedWidgetId +' ]');

      this.loaderNotice.css({top: $(document).scrollTop(), left: 0})
                      .position({my: 'left center', at: 'right center', of: $sectionFilter});

      this.loaderMessage.html('Загрузка... <img class="found-layer__loader" src="/images/ajax-loader-gpn.gif" alt="">');
      if (null !== this.loaderDisplayTimer) {
        clearTimeout(this.loaderDisplayTimer);
        this.loaderDisplayTimer = null;
      }

      //this.filterDisable.fadeIn();
      this.loaderNotice.fadeIn();
    },

    hideLoader: function () {
      //this.filterDisable.fadeOut();
      this.loaderNotice.fadeOut();
    },

    update: function () {
      var self = this;

      var filterState = this.model.get("state"),
        pos = -1;
      for (var filter in filterState) {
        pos++;
        if (filterState[filter].widget == "asWidgetFormFilterSelect") {
          var filterControll = $("#f_" + filter + ".filter-controll");

          if (!filterControll.length)
            continue;

          for (var value in filterState[filter].choices) {
            var option = filterControll.find("option[value='" + value + "']");

            if (filterState[filter].choices[value].enabled == true)
              option.removeClass("disabled");
            else
              option.addClass("disabled");
          }

          if ($.inArray(filterState[filter].value, $(option).attr('value')) >= 0) {
            option.attr('selected', 'selected');
          } else {
            option.removeAttr('selected');
          }
        }


        if (filterState[filter].widget == "asWidgetFormFilterBoolCheckbox" ||
          filterState[filter].widget == "sprWidgetFormFilterSelectCheckboxExpandable" ||
          filterState[filter].widget == "sprWidgetFormFilterSelectCheckboxExpandable2Column" ||
          filterState[filter].widget == "sprWidgetFormFilterSelectCheckbox2Column") {
          for (var value in filterState[filter].choices) {


            var option = $("input#f_" + self.generateId(filter, value));

            // нужно добавить нормальную проверку
            if (!option.length)
              continue;

            if (filterState[filter].choices[value].enabled == true) {
//                            if($(option).data("checkbox"))
//                                $(option).data("checkbox").enable();
//                            else
              $(option).removeClass('disabled');
            }
            else {
//                            if($(option).data("checkbox"))
//                                $(option).data("checkbox").disable();
//                            else
              $(option).addClass('disabled');
            }
            if ($.inArray($(option).attr('value'), filterState[filter].value) >= 0) {
              $(option).attr('checked', 'checked');
              $(option).siblings('.checkbox').addClass('checked');
            } else {
              $(option).removeAttr('checked');
              $(option).siblings('.checkbox').removeClass('checked');
            }
          }
        }
        if (filterState[filter].widget == "sprWidgetFormFilterSelectCheckboxInverted") {
          for (var value in filterState[filter].choices) {
            var option = $("input#f_" + self.generateId(filter, value)),
              $input = $("input#f_" + self.generateId(filter, value) + '_input');

            // нужно добавить нормальную проверку
            if (!option.length)
              continue;

            if (filterState[filter].choices[value].enabled == true) {
//                            if($(option).data("checkbox"))
//                                $(option).data("checkbox").enable();
//                            else
              $(option).removeClass('disabled');
            }
            else {
//                            if($(option).data("checkbox"))
//                                $(option).data("checkbox").disable();
//                            else
              $(option).addClass('disabled');
            }
            if ('undefined' !== typeof filterState[filter].value[$input.attr('value')]) {
              $(option).removeAttr('checked');
              $(option).siblings('.checkbox').removeClass('checked');
            } else {
              $(option).attr('checked', 'checked');
              $(option).siblings('.checkbox').addClass('checked');
            }

          }
        }
        if (filterState[filter].widget == "asWidgetFormFilterSelectRadio") {
          for (var value in filterState[filter].choices) {


            var option = $("input#f_" + self.generateId(filter, value));
            // нужно добавить нормальную проверку
            if (!option.length)
              continue;

            if (filterState[filter].choices[value].enabled == true) {
              option.removeClass("disabled");
              option.parent().removeClass("disabled");
            }
            else {
              option.addClass("disabled");
              option.parent().addClass("disabled");
            }

            if ($.inArray($(option).attr('value'), filterState[filter].value) >= 0) {
              option.attr('checked', 'checked');
            } else {
              option.removeAttr('checked');
            }
          }
        }

        if (filterState[filter].widget == "asWidgetFormFilterSelectLinkedMany"
          || filterState[filter].widget == "asWidgetFormFilterSelectLinked") {
          var filterControll = $("#f_" + filter + ".filter-controll");
          // нужно добавить нормальную проверку
          if (!filterControll.length)
            continue;

          for (var value in filterState[filter].choices) {
            var option = filterControll.find("option[value='" + value + "']");
            var optionWrapper = filterControll.parent().find(".filter-section-list .filter-item a[rel='" + value + "']").parent();


            if (filterState[filter].choices[value].enabled == true) {
              option.removeClass("disabled");
              optionWrapper.removeClass("disabled");
            }
            else {
              option.addClass("disabled");
              optionWrapper.addClass("disabled");
            }

            if ($.inArray($(option).attr('value'), filterState[filter].value) >= 0) {
              option.attr('selected', 'selected');
              optionWrapper.addClass('active');
            } else {
              option.removeAttr('selected');
              optionWrapper.removeClass('active');
            }
          }
        }

        //Создаем отсутствующие поля фильтра
        if ('undefined' === typeof self.fieldViews[filter] && self.widgetFactory.canCreate(filter)) {
          var fieldView = self.createFilterField({
            id: filter,
            name: 'f[' + filter + ']'
          });

          fieldView.$el.insertBefore(this.$('.filter-section:eq(' + pos + ')'));
          fieldView.render();

          self.fieldViews[filter] = fieldView;
        }
      }
    },
    onFilterLoad: function (data) {
      var self = this;
      if (this.model.url() != this.previousUrl && this.options.pushState) {
        this._pushLocation();
      }

      var linked = $(window).scrollTop() > $('#product-listview').offset().top;

      //this.filterDisable.fadeOut();
      this.loaderMessage.html(loadingResultTemplate({
        nbproducts: this.model.Pager.get('nbresults'),
        linked: linked
      }));
      if (null !== this.loaderDisplayTimer) {
        clearTimeout(this.loaderDisplayTimer);
      }
      this.loaderDisplayTimer = setTimeout(function () {
        self.loaderNotice.fadeOut();
      }, linked ? 5000 : 1500);
    },
    onBeforeLoad: function () {
      this.previousUrl = this.model.url();
    },
    _pushLocation: function () {
      this.locationPushed = true;
      if (window.history && window.history.pushState) {
        window.history.pushState({location: this.model.url()}, '', this.model.url());
      }
    },
    onScrollToTopLinkClick: function (e) {
      e.preventDefault();

      $('body').animate({scrollTop: $('#product-listview').offset().top - 30}, 'fast');
      if (null !== this.loaderDisplayTimer) {
        clearTimeout(this.loaderDisplayTimer);
        this.loaderDisplayTimer = null;
      }
      this.loaderNotice.fadeOut();
    },
    onResetClick: function (e) {
      e.preventDefault();
      this.model.set('filterState', {});
      this.sendRequest({});
    },
    /**
     * Создает поле фильтра по заданному набору параметров, или заглушку фильтра, если для этого поля фильтра отсутствует
     * виджет, поддерживаемый фабрикой виджетов.
     *
     * @param {object} options
     */
    createFilterField: function (options) {
      var fieldCls = this.widgetFactory.canCreate(options.id) ? FilterField : FilterFieldStub,
        fieldView = new fieldCls({
          id: options.id,
          name: options.name,
          filter: this.model,
          el: options.el,
          widgetFactory: this.widgetFactory
        });

      return fieldView;
    },
    render: function(){
      var self = this;
      this.$('.filter-field').each(function () {
        var $filterField = $(this),
          fieldId = $filterField.data('field-id'),
          fieldView = self.createFilterField({
            el: $filterField,
            id: fieldId,
            name: $filterField.data('field-name')
          });

        fieldView.render();

        self.fieldViews[fieldId] = fieldView;
      });

      /*
       * Нужно обновить пейджер, чтобы обновились ссылки в паджинации, если фильтр уже был загружен с сервера
       */
      this.model.updatePager();
    }

  });

  return FilterView;

});


