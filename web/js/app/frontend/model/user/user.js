define(function(require){
  var Backbone = require('backbone');

  var User = Backbone.Model.extend({
    urlRoot: urlPrefix + '/api/user',
    defaults: {
      firstname: '',
      lastname: '',
      middlename: '',
      fullname: '',
      phone: '',
      email: '',
      authenticated: false
    },
    getUsername: function(){
      return this.get('fullname') ? this.get('fullname') : this.get('email');
    },
    toJSON: function(){
      var values = _.omit(Backbone.Model.prototype.toJSON.apply(this, arguments),
          ['authenticated', 'role', 'fullname']);

      var
        commonFields = ['tos'],
        accountFields = ['lastname', 'firstname', 'middlename', 'phone', 'email', 'plainPasswordFirst', 'plainPasswordSecond', 'registration'],
        valueMap = {
        'ROLE_JURIDICAL': ['company_name', 'company_inn', 'company_kpp', 'company_ogrn', 'company_country',
          'company_address', 'company_director', 'company_phone', 'company_email', 'contragent'].concat(accountFields, commonFields),
        'ROLE_INDIVIDUAL': ['contragent'].concat(accountFields, commonFields),
        'ROLE_ENTREPRENEUR': accountFields.concat(commonFields)
      };

      values = _.pick(values, valueMap[this.get('role')]);
      values['roles'] = [ this.get('role') ];

      if ('undefined' !== typeof values.plainPasswordFirst &&
        'undefined' !== typeof values.plainPasswordSecond){
        values['plainPassword'] = {
          first: values.plainPasswordFirst,
          second: values.plainPasswordSecond
        };

        delete values.plainPasswordFirst;
        delete values.plainPasswordSecond;
      }

      if (this.attributes.role === 'ROLE_JURIDICAL'){
          var matcher = function(value, key){
              var match = key.match(/^company_(.+)$/);

              if (null !== match){
                return match[0];
              }
          };

          values.company = _.map(values, matcher);
          var _vals = {};
          _.each(values, function(value, key){
            if (!matcher(value, key)){
              _vals[key] = value;
            }
          });

          values = _vals;
      }

      return values;
    }
  });

  User.getCurrentUser = function () {
    if (!User.currentUser)
    {
      User.currentUser = new User(ObjectCache.User);
    }

    return User.currentUser;
  };

  return User;
});