(function($) {

    "use strict";

    $(document).ready(function() {

        // namespace
        var importer = $('.azexo-import');

        // disable submit button
        $('.button', importer).attr('disabled', 'disabled');

        // select.import change
        $('select.import', importer).change(function() {

            var val = $(this).val();

            // submit button
            if (val) {
                $('.button', importer).removeAttr('disabled');
            } else {
                $('.button', importer).attr('disabled', 'disabled');
            }

            // attachments
            if (val == 'all' || val == 'demo') {
                $('.row-attachments', importer).show();
            } else {
                $('.row-attachments', importer).hide();
            }

            // clear wp
            if (val == 'demo') {
                $('.row-attachments, .row-clear-wp', importer).show();
            } else {
                $('.row-attachments, .row-clear-wp', importer).hide();
            }

            // content
            if (val == 'content') {
                $('.row-content', importer).show();
            } else {
                $('.row-content', importer).hide();
            }

            // demo
            if (val == 'demo' || val == 'configuration') {
                $('.row-demo', importer).show();
            } else {
                $('.row-demo', importer).hide();
            }
        });
        $('select.import', importer).trigger('change');

    });

})(jQuery);