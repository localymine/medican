!function(a){"use strict";redux.field_objects=redux.field_objects||{},redux.field_objects.spacing=redux.field_objects.spacing||{},a(document).ready(function(){}),redux.field_objects.spacing.init=function(b){b||(b=a(document).find(".redux-group-tab:visible").find(".redux-container-spacing:visible")),a(b).each(function(){var b=a(this),c=b;if(b.hasClass("redux-field-container")||(c=b.parents(".redux-field-container:first")),!c.is(":hidden")&&c.hasClass("redux-field-init")){c.removeClass("redux-field-init");var d={width:"resolve",triggerChange:!0,allowClear:!0},e=b.find(".select2_params");if(e.size()>0){var f=e.val();f=JSON.parse(f),d=a.extend({},d,f)}b.find(".redux-spacing-units").select2(d),b.find(".redux-spacing-input").on("change",function(){var b=a(this).parents(".redux-field:first").find(".field-units").val();0!==a(this).parents(".redux-field:first").find(".redux-spacing-units").length&&(b=a(this).parents(".redux-field:first").find(".redux-spacing-units option:selected").val());var c=a(this).val();"undefined"!=typeof b&&c&&(c+=b),a(this).hasClass("redux-spacing-all")?a(this).parents(".redux-field:first").find(".redux-spacing-value").each(function(){a(this).val(c)}):a("#"+a(this).attr("rel")).val(c)}),b.find(".redux-spacing-units").on("change",function(){a(this).parents(".redux-field:first").find(".redux-spacing-input").change()})}})}}(jQuery);