// // Essa função verifica se o parâmetro foi passado
// window.isset = function (e) {
//   return (typeof e === "null" || typeof e === "undefined") ? false : true;
// };
//
// // Essa função é identica a 'setElement' em core.js
// // isso é para não haver dependência do core.js
// window.validaField = function (e, def) {
//   var obj = e;
//   if(!isset(e)) {
//     obj = jQuery(def);
//   } else if(typeof e === 'string') {
//     obj = jQuery(e);
//   }
//   return obj;
// };
//
// // FIELDS -> classes dos campos customizados
// var fieldsetEmbed	= ".fieldset-embed";
// var btnToggleStatus	= ".toggle-status .btn, .btn.toggle-status";
// var field_noDrop	= ".no-drop input, input.no-drop";
// var field_noPaste	= ".no-paste input, input.no-paste";
// var field_setFocus 	= ".set-focus input, input.set-focus";
// var field_upper 	= ".upper input, input.upper";
// var field_lower 	= ".lower input, input.lower";
// var field_cpf 		= ".field-cpf input, input.field-cpf";
// var field_cnpj 		= ".field-cnpj input, input.field-cnpj";
// var field_selectAutoTab	= ".auto-tab select, select.auto-tab";
// var field_checkAutoTab 	= ".auto-tab input:checkbox, input:checkbox.auto-tab, .auto-tab input:radio, input:radio.auto-tab";
// var field_setBtnAction = ".set-btn-action input, input.set-btn-action";
// var field_setPhone 	= ".field-phone input, input.field-phone,.field-mobile input, input.field-mobile";
// var field_cep 		= ".field-cep input, input.field-cep";
// var field_fixPaste	= ".field-cpf input, input.field-cpf, .field-cnpj input, input.field-cnpj, .field-cep input, input.field-cep";
// var field_date 		= ".field-date input, input.field-date";
// var field_time 		= ".field-time input, input.field-time";
// var field_date_time = ".field-date-time input, input.field-date-time";
// var field_price		= ".field-price input, input.field-price";
// var field_noNumber	= ".field-nonumber input, input.field-nonumber";
// var field_noSpecialCharacter = ".no-special-character input, input.no-special-character";
// var field_noBlankSpace	= ".no-blank-space input, input.no-blank-space";
// var field_noAccents	= ".no-accents input, input.no-accents";
// var field_integer	= ".field-integer input, input.field-integer";
// var field_float		= ".field-float input, input.field-float";
// var field_number	= ".field-number input, input.field-number";
// // AUTO COMPLETE
// var field_state		= '.field-state input, input.field-state';
// var field_country	= '.field-country input, input.field-country';
// var field_uf		= ".field-uf select, select.field-uf";
// var field_city		= ".field-city input, input.field-city";
// var field_cidade	= ".field-cidade input, input.field-cidade";
// var field_searchCep	= ".field-cep.field-search input, input.field-cep.field-search";
// var field_address	= ".field-address input, input.field-address";
// var field_address_number = ".field-address-number input, input.field-address-number";
// var field_district	= ".field-district input, input.field-district";
//
// // ----------------------------------------------------------------------
//
//
// // ----------------------------------
// setDate();
