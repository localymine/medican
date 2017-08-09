<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}


add_filter('woocommerce_available_variation', 'azb_add_variation_bookable_attribute', 10, 3);
add_action('woocommerce_before_add_to_cart_button', 'azb_before_add_to_cart_button', 20);
add_filter('woocommerce_get_price_html', 'azb_add_price_html', 10, 2);
add_filter('woocommerce_get_variation_price_html', 'azb_add_price_html', 10, 2);

function azb_add_variation_bookable_attribute($available_variations, $product, $variation) {
    $is_bookable = get_post_meta($variation->variation_id, '_booking_option', true);

    if (empty($is_bookable)) {
        $is_bookable = false;
    }
    $available_variations['is_bookable'] = $is_bookable;
    if (isset($is_bookable) && $is_bookable === 'yes') {

        $availability = get_post_meta($variation->variation_id, 'availability', true);
        if (empty($availability)) {
            $availability = array();
        } else {
            $availability = json_decode($availability, true);
            $availability = array_combine(array_keys($availability), array_keys($availability));
        }
        $available_variations['availability'] = $availability;

        $azb_settings = get_option('azb_settings');
        $global_booking_min = isset($azb_settings['azb_booking_min']) ? absint($azb_settings['azb_booking_min']) : '';
        $global_booking_max = isset($azb_settings['azb_booking_max']) ? absint($azb_settings['azb_booking_max']) : '';
        $global_first_available_date = isset($azb_settings['azb_first_available_date']) ? absint($azb_settings['azb_first_available_date']) : '';
        $global_max_year = isset($azb_settings['azb_max_year']) ? absint($azb_settings['azb_max_year']) : 3;

        $manage_bookings = get_post_meta($product->id, '_manage_bookings', true);
        if ($manage_bookings == 'yes') {
            $parent_booking_min = get_post_meta($product->id, '_booking_min', true);
            $parent_booking_max = get_post_meta($product->id, '_booking_max', true);
            $parent_first_available_date = get_post_meta($product->id, '_first_available_date', true);
            $parent_max_year = get_post_meta($product->id, '_max_year', true);
        }

        $booking_min = get_post_meta($variation->variation_id, '_booking_min', true);
        $booking_max = get_post_meta($variation->variation_id, '_booking_max', true);
        $first_available_date = get_post_meta($variation->variation_id, '_first_available_date', true);
        $max_year = get_post_meta($variation->variation_id, '_max_year', true);

        if ($booking_min === false || $booking_min === '') {
            if ($manage_bookings != 'yes' || $parent_booking_min === false || $parent_booking_min === '') {
                if ($global_booking_min === false || $global_booking_min === '') {
                    $booking_min = 0;
                } else {
                    $booking_min = $global_booking_min;
                }
            } else {
                $booking_min = $parent_booking_min;
            }
        }

        if ($booking_max === false || $booking_max === '') {
            if ($manage_bookings != 'yes' || $parent_booking_max === false || $parent_booking_max === '') {
                if ($global_booking_max === false || $global_booking_max === '') {
                    $booking_max = 0;
                } else {
                    $booking_max = $global_booking_max;
                }
            } else {
                $booking_max = $parent_booking_max;
            }
        }

        if ($first_available_date === false || $first_available_date === '') {
            if ($manage_bookings != 'yes' || $parent_first_available_date === false || $parent_first_available_date === '') {
                if ($global_first_available_date === false || $global_first_available_date === '') {
                    $first_available_date = 0;
                } else {
                    $first_available_date = $global_first_available_date;
                }
            } else {
                $first_available_date = $parent_first_available_date;
            }
        }

        if ($max_year === false || $max_year === '') {
            if ($manage_bookings != 'yes' || $parent_max_year === false || $parent_max_year === '') {
                if ($global_max_year === false || $global_max_year === '') {
                    $max_year = 0;
                } else {
                    $max_year = $global_max_year;
                }
            } else {
                $max_year = $parent_max_year;
            }
        }

        $available_variations['booking_min'] = absint($booking_min);
        $available_variations['booking_max'] = absint($booking_max);
        $available_variations['first_available_date'] = absint($first_available_date);
        $available_variations['max_year'] = absint($max_year);
    }

    return $available_variations;
}

function azb_get_product_params($product) {
    $is_bookable = get_post_meta($product->id, '_booking_option', true);
    if (isset($is_bookable) && $is_bookable === 'yes') {
        $azb_settings = get_option('azb_settings');
        $global_booking_min = isset($azb_settings['azb_booking_min']) ? absint($azb_settings['azb_booking_min']) : '';
        $global_booking_max = isset($azb_settings['azb_booking_max']) ? absint($azb_settings['azb_booking_max']) : '';
        $global_first_available_date = isset($azb_settings['azb_first_available_date']) ? absint($azb_settings['azb_first_available_date']) : '';
        $global_max_year = isset($azb_settings['azb_max_year']) ? absint($azb_settings['azb_max_year']) : 3;

        $booking_min = get_post_meta($product->id, '_booking_min', true);
        $booking_max = get_post_meta($product->id, '_booking_max', true);
        $first_available_date = get_post_meta($product->id, '_first_available_date', true);
        $max_year = get_post_meta($product->id, '_max_year', true);

        if ($booking_min === false || $booking_min === '') {
            if ($global_booking_min === false || $global_booking_min === '') {
                $booking_min = 0;
            } else {
                $booking_min = $global_booking_min;
            }
        }

        if ($booking_max === false || $booking_max === '') {
            if ($global_booking_max === false || $global_booking_max === '') {
                $booking_max = 0;
            } else {
                $booking_max = $global_booking_max;
            }
        }

        if ($first_available_date === false || $first_available_date === '') {
            if ($global_first_available_date === false || $global_first_available_date === '') {
                $first_available_date = 0;
            } else {
                $first_available_date = $global_first_available_date;
            }
        }

        if ($max_year === false || $max_year === '') {
            if ($global_max_year === false || $global_max_year === '') {
                $max_year = 0;
            } else {
                $max_year = $global_max_year;
            }
        }

        $params['booking_min'] = absint($booking_min);
        $params['booking_max'] = absint($booking_max);
        $params['first_available_date'] = absint($first_available_date);
        $params['max_year'] = absint($max_year);
        return $params;
    }
}

/**
 *
 * Adds a custom form to the product page.
 *
 * */
function azb_before_add_to_cart_button() {
    global $post, $product;

    $product = wc_get_product($product->id);

    // Is product bookable ?
    $is_bookable = get_post_meta($product->id, '_booking_option', true);
    $options = get_option('azb_settings');
    $info_text = $options['azb_info_text'];
    $start_date_text = $options['azb_start_date_text'];
    $end_date_text = $options['azb_end_date_text'];

    $tax_display_mode = get_option('woocommerce_tax_display_shop');
    $product_price = $tax_display_mode === 'incl' ? $product->get_price_including_tax() : $product->get_price_excluding_tax(); // Product price (Regular or sale)
    // Product is bookable
    if (isset($is_bookable) && $is_bookable === 'yes') {
        
        $params = azb_get_product_params($product);
        $availability = get_post_meta($product->id, 'availability', true);
        if (empty($availability)) {
            $availability = array();
        } else {
            $availability = json_decode($availability, true);
            $availability = array_combine(array_keys($availability), array_keys($availability));
        }
        $params['availability'] = $availability;        
        $params['booking_min_alert'] = esc_attr__('Interval must be more than %d days', 'azb');
        $params['booking_max_alert'] = esc_attr__('Interval must be less than %d days', 'azb');
        wp_localize_script('azb', 'azb', $params);

        // Display info text
        if (isset($info_text) && !empty($info_text)) {
            echo '<div class="woocommerce-info">' . wpautop(esc_textarea($info_text)) . '</div>';
        }

        echo '<div class="azb-errors">' . wc_print_notices() . '</div>';


        echo '<div class="azb-picker">'
        . '<input type="hidden" name="variation_id" data-product_id="' . absint($product->id) . '" value="">'
        . '<input type="hidden" name="start_date" value="">'
        . '<input type="hidden" name="end_date" value="">';
        echo '<label>' . esc_html($start_date_text) . ' - ' . esc_html($end_date_text) . '</label>'
        . '<div class="datepicker" data-months-number="1"></div>';
        echo '</div>';
    }
}

function azb_add_price_html($content, $product) {
    $is_bookable = get_post_meta(isset($product->variation_id) ? $product->variation_id : $product->id, '_booking_option', true); // Is it bookable ?

    if (isset($is_bookable) && $is_bookable === 'yes') {
        $options = get_option('azb_settings');
        $calc_mode = $options['azb_calc_mode'];

        if ($calc_mode === 'nights') {
            $price_text = __(' / night', 'azb');
        } else {
            $price_text = __(' / day', 'azb');
        }

        return $content . '<span class = "azb-price-mode">' . $price_text . '</span>';
    } else {
        return $content;
    }
}
