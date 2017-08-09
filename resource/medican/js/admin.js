(function($) {
    "use strict";

    $(function() {
        setTimeout(function() {
            if ('redux' in $ && 'ajax_save' in $.redux) {
                var redux_ajax_save = $.redux.ajax_save;
                $.redux.ajax_save = function(button) {
                    $('fieldset.redux-container-media input.upload-height, fieldset.redux-container-media input.upload-width, fieldset.redux-container-media input.upload-thumbnail').remove();
                    redux_ajax_save(button);
                };
            }
            $('.redux-group-tab-link-a').on('click', function() {
                if ($(this).find('.group_title').text() == 'Templates configuration' ||
                        $(this).find('.group_title').text() == 'Fields configuration' ||
                        $(this).find('.group_title').text() == 'Post types settings' ||
                        $(this).find('.group_title').text() == 'WooCommerce templates configuration')
                {
                    alert('Theme not provide CSS styles for any configuration in this section');
                }
            });
            $(document).on('click', function(event) {
                if ($(event.target).closest('.vc_control-btn-edit, [data-vc-control="edit"]').length) {
                    $(event.target).closest('[data-model-id]').each(function() {
                        if ($(this).is('[data-element_type*="azexo"]')) {
                            alert('Theme not provide CSS styles for any settings of this element');
                        }
                    });
                }
            });
        }, 0);
    });
})(jQuery);
