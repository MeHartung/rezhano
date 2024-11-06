define(function (require) {
  var Backbone = require('backbone'),
      User = require('model/user/user'),
      CommonView = require('view/common/common-view'),
      JuridicalRegistrationForm = require('view/user/registration/registration-juridical-view'),
      IndividualRegistrationForm = require('view/user/registration/registration-individual-view'),
      EntrepreneurRegistrationForm = require('view/user/registration/registration-entrepreneur-view'),
      RegistrationListView = require('view/user/registration/registration-doc-load-view'),
      DocumentItemCollection = require('model/user/document-item-collection'),
      DocumentItem = require('model/user/document-item');

  require('jquery-ui/widgets/tabs');

  return CommonView.extend({
    events: {
      'tabsactivate #registerTabs': 'onTabActivate'
    },
    initialize: function (options) {
      var self = this;
      CommonView.prototype.initialize.apply(this, arguments);

      this.arrEnterpreneur = DocumentItemCollection.fromCache(ObjectCache.EnterpreneurDocumentTypes || []);
      this.enterpreneurDoc = new RegistrationListView({
        collection: this.arrEnterpreneur
      });

      this.arrIndividual = DocumentItemCollection.fromCache(ObjectCache.IndividualDocumentTypes || []);
      this.individualDoc = new RegistrationListView({
        collection: this.arrIndividual
      });

      this.arrJuridical = DocumentItemCollection.fromCache(ObjectCache.JuridicalDocumentTypes || []);
      this.juridicalDoc = new RegistrationListView({
        collection: this.arrJuridical
      });

      this.model = new User({
        role: 'ROLE_JURIDICAL' //ROLE_INDIVIDUAL, ROLE_ENTREPRENEUR
      });

      this.forms = {
        'ROLE_JURIDICAL': new JuridicalRegistrationForm({
          model: this.model,
          idPrefix: 'register_juridical',
        }),
        'ROLE_INDIVIDUAL': new IndividualRegistrationForm({
          model: this.model,
          idPrefix: 'register_individual',
        }),
        'ROLE_ENTREPRENEUR': new EntrepreneurRegistrationForm({
          model: this.model,
          idPrefix: 'register_entrepreneur',
        })
      };

      _.each(this.forms, function (_form) {
        self.listenTo(_form, 'change', self.onFormChange)
      })
    },
    render: function () {
      CommonView.prototype.render.apply(this, arguments);

      this.$('#registerTabs').tabs();

      this.forms.ROLE_JURIDICAL.render().$el.appendTo(this.$('#registerJuridical').html(''));
      this.forms.ROLE_INDIVIDUAL.render().$el.appendTo(this.$('#registerIndividual').html(''));
      this.forms.ROLE_ENTREPRENEUR.render().$el.appendTo(this.$('#registerEnterpreneur').html(''));

      this.enterpreneurDoc.render().$el.appendTo(this.$('#registerEnterpreneur .document-list'));
      this.individualDoc.render().$el.appendTo(this.$('#registerIndividual .document-list'));
      this.juridicalDoc.render().$el.appendTo(this.$('#registerJuridical .document-list'));

      return this;
    },
    onTabActivate: function (event, ui) {
      this.model.set('role', ui.newTab.data('role'));
    },
    onFormChange: function (form) {
      var self = this;
      //Обновим значения без валидации (она будет выполняться перед сабмитом)
      form.model.set(form.getValue());

      _.each(this.forms, function (_form) {
        if (_form !== form){
          _form.setValue(self.model.attributes);
        }
      })
    }
  });
});