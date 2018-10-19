define(function (require) {
    var Backbone = require('backbone'),
        Form = require('view/user/registration/registration-form-view');

    return Form.extend({
        template: _.template('\
<form name="registerJuridical" method="post">\n\
<div class="cards-container cards-container__registration cards-container__registration_first">\n\
  <div class="type-registered">\
    <div class="registration-container__wrap global-errors" style="display:none;"></div>\n\
    <div class="registration-container__wrap">\n\
      <div class="registration-container__step" data-fields="company_name">\n' +
// '                          <div class="step-item">\n' +
// '                            <span class="field-description">ИНН или полное название организации</span>\n' +
// '                            <input type="text" class="input-text_biggest" placeholder="Название или ИНН в свободной форме">\n' +
// '                          </div>\n' +
'      </div>\n' +
'    </div>\n' +
'    <div class="registration-container__wrap">\n' +
'      <div class="registration-container__step" data-fields="company_inn,company_kpp,company_ogrn,company_country,company_address"></div>\n' +
'    </div>\n' +
'    <div class="registration-container__wrap">\n' +
'      <div class="registration-container__step" data-fields="company_director,company_phone,company_email"></div>\n' +
'    </div>\n' +
'    <div class="registration-container__wrap">\n' +
'      <div class="registration-container__title">Данные ответственного лица</div>\n' +
'      <div class="registration-container__step">\n' +
'        <div class="password-wrap" data-fields="lastname,firstname,middlename,phone,email"></div>' +
'        <div class="password-wrap" data-fields="plainPasswordFirst,plainPasswordSecond"></div>\n' +
'      </div>\n' +
'    </div>\n' +
'  </div>\n' +
'</div>' +
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
'<div class="cart-block-aside cart-block-aside_left-alignment">\n' +
'    <div class="cart-block-aside__wrap">' +
'<div class="password-wrap" data-fields="tos"></div>' +
'       <button type="submit" class="button" name="role">Зарегистрироваться</button>\n' +
'          </div>\n' +
'</div>\n\
        </form>\
'),
        schema: _.extend({}, Form.prototype.schema, {
            company_name: {
                type: "Text",
                title: "Название организации",
                editorClass: "input-text_biggest",
                validators: [{ type: 'required', message: 'Введите название организации' }]
            },
            company_inn: {
                type: "Text",
                title: "ИНН",
                editorClass: "input-text_200",
                editorAttrs: { "required": "required" },
                inline: true,
                validators: [{ type: 'required', message: 'Введите ИНН' }]
            },
            company_kpp: {
                type: "Text",
                title: "КПП",
                editorClass: "input-text_200",
                editorAttrs: { "required": "required" },
                inline: true,
                validators: [{ type: 'required', message: 'Введите КПП' }]
            },
            company_ogrn: {
                type: "Text",
                title: "ОГРН / ОГРНиП",
                editorClass: "input-text_200",
                editorAttrs: { "required": "required" },
                inline: true,
                validators: [{ type: 'required', message: 'Введите ОГРН' }]
            },
            company_country: {
                type: "Text",
                title: "Страна регистрации",
                editorClass: "input-text_200",
                editorAttrs: { "required": "required" },
                inline: true,
                validators: [{ type: 'required', message: 'Введите страну регистрации' }]
            },
            company_address: {
                type: "Text",
                title: "Юридический адрес",
                editorClass: "input-text_540",
                editorAttrs: { "required": "required" },
                inline: true,
                validators: [{ type: 'required' }]
            },
            company_director: {
                type: "Text",
                title: "ФИО руководителя организации",
                editorClass: "input-text_biggest",
                editorAttrs: { "required": "required" },
                validators: [{ type: 'required', message: 'Введите ФИО руководителя организации' }]
            },
            company_phone: {
                type: "InputPhone",
                title: "Номер телефона",
                editorClass: "input-text_200",
                editorAttrs: {
                    "required": "required",
                    "placeholder": "+7 (___) ___ - __ - __"
                },
                inline: true,
                validators: [{ type: 'required', message: 'Введите номер телефона организации' }]
            },
            company_email: {
                type: "Text",
                title: "Электронная почта",
                editorClass: "input-text_290",
                editorAttrs: {
                    "required": "required"
                },
                inline: true,
                validators: [
                  { type: 'required', message: 'Введите электронную почту организации' },
                  { type: 'email', message: 'Неверный адрес e-mail' }
                ]
            }
        })
    });
});