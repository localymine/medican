!function(a){"use strict";redux.field_objects=redux.field_objects||{},redux.field_objects.ace_editor=redux.field_objects.ace_editor||{},redux.field_objects.ace_editor.init=function(b){b||(b=a(document).find(".redux-group-tab:visible").find(".redux-container-ace_editor:visible")),a(b).each(function(){var b=a(this),c=b;b.hasClass("redux-field-container")||(c=b.parents(".redux-field-container:first")),c.is(":hidden")||c.hasClass("redux-field-init")&&(c.removeClass("redux-field-init"),b.find(".ace-editor").each(function(c,d){var e=d,f=JSON.parse(a(this).parent().find(".localize_data").val()),g=a(d).attr("data-editor"),h=ace.edit(g);h.setTheme("ace/theme/"+jQuery(d).attr("data-theme")),h.getSession().setMode("ace/mode/"+a(d).attr("data-mode"));var i="";i=b.hasClass("redux-field-container")?b.attr("data-id"):b.parents(".redux-field-container:first").attr("data-id"),h.setOptions(f),h.on("change",function(b){a("#"+e.id).val(h.getSession().getValue()),redux_change(a(d)),h.resize()})}))})}}(jQuery);