jQuery(function(a){var b={init:function(){a("select#discount_type").on("change",this.type_options).change()},type_options:function(){var b=a(this).val();"fixed_product"===b||"percent_product"===b?a(".limit_usage_to_x_items_field").show():a(".limit_usage_to_x_items_field").hide()}};b.init()});