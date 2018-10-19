define(function(require){
    var Backbone = require('backbone'),
        Form = require('view/user/registration/registration-form-view');

    return Form.extend({
      template: _.template('\
<form name="registerIndividual" method="post">\n' +
      '<div class="cards-container cards-container__registration cards-container__registration_first">\n' +
      '    <div class="registration-container__wrap global-errors" style="display:none;"></div>' +
      '    <div class="registration-container__wrap">\n' +
      '        <div class="registration-container__title">\n' +
      '            Личные данные\n' +
      '        </div>\n' +
      '        <div class="registration-container__step">\n' +
      '           <div class="password-wrap" data-fields="lastname,firstname,middlename,phone,email"></div>' +
      '           <div class="password-wrap" data-fields="plainPasswordFirst,plainPasswordSecond"></div>\n' +
      '        </div>\n' +
      '    </div>\n' +
      '</div>\n' +
        '<div class="cards-container cards-container__registration cards-container__registration_document">' +
        '   <div class="registration-container__wrap">' +
        '      <div class="cards-container__registration" data-editors="contragent"></div>\n' +
        '        <div class="registration-container__step">' +
        '            <div class="registration-container__title">\n' +
        '              Документы <span class="registration-container__title-aside">— необходимо загрузить все документы</span>\n' +
        '            </div>\n' +
        '            <div class="document-list"></div>\n' +
        '        </div>' +
        '   </div>' +
        '</div>' +
        // '           <div class="step-item"><span class="field-description"><label for="registerIndividual_city">Город</label></span><select id="registerIndividual_city" name="registerIndividual[city]"><option value=""></option></select></div>\n' +
      '<div class="cart-block-aside cart-block-aside_left-alignment">\n' +
      '    <div class="cart-block-aside__wrap">' +
          '<div class="password-wrap" data-fields="tos"></div>' +
      '       <button type="submit" class="button" name="role">Зарегистрироваться</button>\n' +
'          </div>\n' +
      '</div>\n\
  </form>\
      '),
      schema: _.extend({}, Form.prototype.schema, {

      })
    });
});