<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

add_filter('woocommerce_add_to_cart_validation', 'azb_check_dates_before_add_to_cart', 20, 4);
add_filter('woocommerce_add_cart_item_data', 'azb_add_cart_item_data', 10, 3);
add_filter('woocommerce_get_item_data', 'azb_get_item_data', 10, 2);
add_filter('woocommerce_add_cart_item', 'azb_add_cart_item', 10);
add_filter('woocommerce_get_cart_item_from_session', 'azb_get_cart_item_from_session', 10, 2);

/**
 *
 * Checks if two dates are set before adding to cart
 *
 * @param bool $passed
 * @param int $product_id
 * @return bool $passed
 *
 * */
function azb_check_dates_before_add_to_cart($passed = true, $product_id, $quantity, $variation_id = '') {
    $id = empty($variation_id) ? $product_id : $variation_id;
    $is_bookable = get_post_meta($id, '_booking_option', true);

    if (!$passed)
        return $passed;

    // If product is bookable
    if (isset($is_bookable) && $is_bookable === 'yes') {

        if (isset($_REQUEST['start_date']) && isset($_REQUEST['end_date'])) {
            $passed = true;
        } else {
            wc_add_notice(__('Please select dates interval', 'azb'), 'error');
            $passed = false;
        }
    }

    return $passed;
}

/**
 *
 * Adds session data to cart item
 *
 * @param array $cart_item_meta
 * @param int $product_id
 * @return array $cart_item_meta
 *
 * */
function azb_add_cart_item_data($cart_item_meta, $product_id, $variation_id) {

    if (isset($_REQUEST['start_date'])) {
        $cart_item_meta['_start_date'] = sanitize_text_field($_REQUEST['start_date']);
    }
    if (isset($_REQUEST['end_date'])) {
        $cart_item_meta['_end_date'] = sanitize_text_field($_REQUEST['end_date']);
    }

    return $cart_item_meta;
}

/**
 *
 * Adds formatted dates to the cart item
 *
 * @param array $other_data
 * @param array $cart_item
 * @return array $other_data
 *
 * */
function azb_get_item_data($other_data, $cart_item) {
    $options = get_option('azb_settings');
    $start_text = !empty($options['azb_start_date_text']) ? $options['azb_start_date_text'] : __('Check In', 'azb');
    $end_text = !empty($options['azb_end_date_text']) ? $options['azb_end_date_text'] : __('Check Out', 'azb');

    if (isset($cart_item['_start_date']) && !empty($cart_item['_start_date']))
        $other_data[] = array('name' => $start_text, 'value' => date_i18n(wc_date_format(), strtotime(sanitize_text_field($cart_item['_start_date']))));

    if (isset($cart_item['_end_date']) && !empty($cart_item['_end_date']))
        $other_data[] = array('name' => $end_text, 'value' => date_i18n(wc_date_format(), strtotime(sanitize_text_field($cart_item['_end_date']))));

    return $other_data;
}

function azb_get_cart_item_from_session($cart_item, $values) {

    if (isset($values['_start_date'])) {
        $cart_item['_start_date'] = $values['_start_date'];
    }

    if (isset($values['_end_date'])) {
        $cart_item['_end_date'] = $values['_end_date'];
    }

    azb_add_cart_item($cart_item);

    return $cart_item;
}

/**
 *
 * Sets custom price to the cart item
 *
 * @param array $cart_item
 * @return array $cart_item
 *
 * */
function azb_add_cart_item($cart_item) {

    if (isset($cart_item['_start_date']) && isset($cart_item['_end_date'])) {
        $booking_price = azb_get_booking_price($cart_item['product_id'], $cart_item['variation_id'], $cart_item['_start_date'], $cart_item['_end_date']);
        $cart_item['data']->set_price($booking_price);
    }

    return $cart_item;
}
