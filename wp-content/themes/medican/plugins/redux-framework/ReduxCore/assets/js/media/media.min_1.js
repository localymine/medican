!function(a){"use strict";redux.field_objects=redux.field_objects||{},redux.field_objects.media=redux.field_objects.media||{};var b;a(document).ready(function(){}),redux.field_objects.media.init=function(c){c||(c=a(document).find(".redux-group-tab:visible").find(".redux-container-media:visible")),a(c).each(function(){var c=a(this),d=c;c.hasClass("redux-field-container")||(d=c.parents(".redux-field-container:first")),d.is(":hidden")||d.hasClass("redux-field-init")&&(d.removeClass("redux-field-init"),b=!1,c.find(".remove-image, .remove-file").unbind("click").on("click",function(){redux.field_objects.media.removeFile(a(this).parents("fieldset.redux-field:first"))}),c.find(".media_upload_button").unbind().on("click",function(b){redux.field_objects.media.addFile(b,a(this).parents("fieldset.redux-field:first"))}))})},redux.field_objects.media.addFile=function(c,d){c.preventDefault();var e,f,g=a(this);if(e)return void e.open();var h=a(d).find(".library-filter").data("lib-filter");void 0!==h&&""!==h&&(f=[],b=!0,h=decodeURIComponent(h),h=JSON.parse(h),a.each(h,function(a,b){f.push(b)})),e=wp.media({multiple:!1,library:{type:f},title:g.data("choose"),button:{text:g.data("update")}}),e.on("select",function(){var c=e.state().get("selection").first();e.close();var f=a(d).find(".data").data();if(("undefined"==typeof redux.field_objects.media||void 0===typeof redux.field_objects.media)&&(redux.field_objects.media={}),(void 0===f||"undefined"===f.mode)&&(f={},f.mode="image"),b===!0&&(f.mode=0),0===f.mode);else if(f.mode!==!1&&c.attributes.type!==f.mode&&c.attributes.subtype!==f.mode)return;d.find(".upload").val(c.attributes.url),d.find(".upload-id").val(c.attributes.id),d.find(".upload-height").val(c.attributes.height),d.find(".upload-width").val(c.attributes.width),redux_change(a(d).find(".upload-id"));var g=c.attributes.url;if("undefined"!=typeof c.attributes.sizes&&"undefined"!=typeof c.attributes.sizes.thumbnail)g=c.attributes.sizes.thumbnail.url;else if("undefined"!=typeof c.attributes.sizes){var h=c.attributes.height;for(var i in c.attributes.sizes){var j=c.attributes.sizes[i];j.height<h&&(h=j.height,g=j.url)}}else g=c.attributes.icon;d.find(".upload-thumbnail").val(g),d.find(".upload").hasClass("noPreview")||d.find(".screenshot").empty().hide().append('<img class="redux-option-image" src="'+g+'">').slideDown("fast"),d.find(".remove-image").removeClass("hide"),d.find(".redux-background-properties").slideDown()}),e.open()},redux.field_objects.media.removeFile=function(b){if(b.find(".remove-image").addClass("hide")){b.find(".remove-image").addClass("hide"),b.find(".upload").val(""),b.find(".upload-id").val(""),b.find(".upload-height").val(""),b.find(".upload-width").val(""),b.find(".upload-thumbnail").val(""),redux_change(a(b).find(".upload-id")),b.find(".redux-background-properties").hide();var c=b.find(".screenshot");c.slideUp(),b.find(".remove-file").unbind(),b.find(".section-upload .upload-notice").length>0&&b.find(".media_upload_button").remove()}}}(jQuery);