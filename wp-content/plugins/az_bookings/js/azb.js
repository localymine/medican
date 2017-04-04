(function($) {
    "use strict";
    $(function() {
        function get_date_string(date) {
            return date.getFullYear() + '-' + ('0' + (date.getMonth() + 1)).slice(-2) + '-' + ('0' + date.getDate()).slice(-2);
        }
        $('.azb-picker').each(function() {
            function get_date_string(date) {
                if (date)
                    return date.getFullYear() + '-' + ('0' + (date.getMonth() + 1)).slice(-2) + '-' + ('0' + date.getDate()).slice(-2);
                else
                    '';
            }
            function input_days_data() {
                for (var d = new Date(from_date); d <= to_date; d.setDate(d.getDate() + 1)) {
                    if (get_date_string(d) in $(picker).data('azb').availability) {
                        from_date = null;
                        to_date = null;
                        $(datepicker).datepicker('refresh');
                        break;
                    }
                }
                $(picker).find('[name="start_date"]').val(get_date_string(from_date));
                $(picker).find('[name="end_date"]').val(get_date_string(to_date));
            }
            var picker = this;
            $(picker).data('azb', window.azb);
            var from_date = null;
            var to_date = null;
            var datepicker = $(picker).find('.datepicker');
            $(datepicker).datepicker({
                minDate: 0,
                maxDate: '+' + $(picker).data('azb').max_year + 'y',
                numberOfMonths: [$(datepicker).data('months-number'), 1],
                beforeShowDay: function(date) {
                    var currentTime = new Date();
                    if ((get_date_string(date) == get_date_string(currentTime)) || (date.getTime() > currentTime.getTime()) && ((date.getTime() - currentTime.getTime()) / 1000 / 60 / 60 / 24) < $(picker).data('azb').first_available_date) {
                        return [true, "reserved", ''];
                    }
                    for (var d in $(picker).data('azb').availability) {
                        if (d == get_date_string(date)) {
                            return [true, "reserved", ''];
                        }
                    }
                    return [true, from_date && ((date.getTime() == from_date.getTime()) || (to_date && date >= from_date && date <= to_date)) ? "highlight" : ""];
                },
                onSelect: function(dateText, inst) {
                    var selectedDate = $.datepicker.parseDate($.datepicker._defaults.dateFormat, dateText);
                    var currentTime = new Date();
                    if (((selectedDate.getTime() - currentTime.getTime()) / 1000 / 60 / 60 / 24) < $(picker).data('azb').first_available_date) {
                        return;
                    }

                    if (!from_date || to_date) {
                        from_date = selectedDate;
                        to_date = '';
                        $(this).datepicker();
                    } else if (selectedDate < from_date) {
                        if (((from_date.getTime() - selectedDate.getTime()) / 1000 / 60 / 60 / 24) >= $(picker).data('azb').booking_min) {
                            if ($(picker).data('azb').booking_max == 0 || ((from_date.getTime() - selectedDate.getTime()) / 1000 / 60 / 60 / 24) < $(picker).data('azb').booking_max) {
                                to_date = from_date;
                                from_date = selectedDate;
                                $(datepicker).datepicker('refresh');
                                input_days_data();
                            } else {
                                alert(azb.booking_max_alert.replace(/%d/g, $(picker).data('azb').booking_max));
                            }
                        } else {
                            alert(azb.booking_min_alert.replace(/%d/g, $(picker).data('azb').booking_min));
                        }
                    } else {
                        if (((selectedDate.getTime() - from_date.getTime()) / 1000 / 60 / 60 / 24) >= $(picker).data('azb').booking_min) {
                            if ($(picker).data('azb').booking_max == 0 || ((selectedDate.getTime() - from_date.getTime()) / 1000 / 60 / 60 / 24) < $(picker).data('azb').booking_max) {
                                to_date = selectedDate;
                                $(datepicker).datepicker('refresh');
                                input_days_data();
                            } else {
                                alert(azb.booking_max_alert.replace(/%d/g, $(picker).data('azb').booking_max));
                            }
                        } else {
                            alert(azb.booking_min_alert.replace(/%d/g, $(picker).data('azb').booking_min));
                        }
                    }
                }
            });
        });
        setTimeout(function() {
            $('.variations_form').each(function() {
                var picker = $(this).find('.azb-picker');
                $(picker).hide();
                $(this).on('found_variation', function(event, variation) {
                    variation.is_bookable ? $(picker).slideDown(200) : $(picker).slideUp(200);
                    if (variation.is_bookable) {
                        var azb = $(picker).data('azb');
                        for (var key in window.azb) {
                            if (key in variation) {
                                azb[key] = variation[key];
                            }
                        }
                        $(picker).data('azb', azb);
                        $(picker).find('.datepicker').datepicker('option', 'maxDate', '+' + azb.max_year + 'y');
                        $(picker).find('.datepicker').datepicker('refresh');
                    }
                });
            });
        }, 0);
        azb.imageMapPosts = {};
        azb.imageMapSetDate = function(date) {
            $.post(azb.ajaxurl, {
                'action': 'azb_get_unavailable',
                'ids': Object.keys(azb.imageMapPosts),
                'date': get_date_string(date)
            }, function(response) {
                if (response && response != '') {
                    $('.imp-shape-container svg > *').removeClass('available').removeClass('unavailable');
                    for (var post_id in azb.imageMapPosts) {
                        if (response.indexOf(post_id) >= 0) {
                            $('#' + azb.imageMapPosts[post_id]).addClass('unavailable');
                        } else {
                            $('#' + azb.imageMapPosts[post_id]).addClass('available');
                        }
                    }
                }
            });
        }
        $.imageMapProInitialized = function(imageMapName) {
            $('.imp-initialized').each(function() {
                var imageMapPro = $('.imp-initialized').data('plugin_imageMapPro');
                if (imageMapPro) {
                    for (var i = 0; i < imageMapPro.settings.spots.length; i++) {
                        var area_id = imageMapPro.settings.spots[i].id;
                        var post_id = parseInt(imageMapPro.settings.spots[i].actions.link, 10);
                        azb.imageMapPosts[post_id] = area_id;
                    }
                }
            });
            for (var post_id in azb.imageMapPosts) {
                var area_id = azb.imageMapPosts[post_id];
                (function(post_id, area_id) {
                    $('#' + area_id).off('click.azb').on('click.azb', function() {
                        window.location = azb.homeurl + '?p=' + post_id;
                        return false;
                    });
                })(post_id, area_id);
            }
            azb.imageMapSetDate(new Date);
        }
    });
})(jQuery);