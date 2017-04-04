<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}


add_action('woocommerce_add_order_item_meta', 'azb_add_order_meta', 10, 2);
add_filter('woocommerce_hidden_order_itemmeta', 'azb_hide_formatted_date', 10, 1);

/**
 *
 * Adds booked dates to the order item
 *
 * @param int $item_id
 * @param array $values - 
 *
 * */
function azb_add_order_meta($item_id, $values) {

    $options = get_option('azb_settings');
    $start_text = !empty($options['azb_start_date_text']) ? $options['azb_start_date_text'] : __('Check In', 'azb');
    $end_text = !empty($options['azb_end_date_text']) ? $options['azb_end_date_text'] : __('Check Out', 'azb');

    if (!empty($values['_start_date'])) {
        wc_add_order_item_meta($item_id, $start_text, date_i18n(wc_date_format(), strtotime(sanitize_text_field($values['_start_date']))));
        wc_add_order_item_meta($item_id, '_start_date', $values['_start_date']);
    }

    if (!empty($values['_end_date'])) {
        wc_add_order_item_meta($item_id, $end_text, date_i18n(wc_date_format(), strtotime(sanitize_text_field($values['_end_date']))));
        wc_add_order_item_meta($item_id, '_end_date', $values['_end_date']);
    }
}

/**
 *
 * Hides dates on the order page (to display a custom form instead)
 *
 * @param array $item_meta - Hidden values
 * @return array $item_meta
 *
 * */
function azb_hide_formatted_date($item_meta) {
    $options = get_option('azb_settings');
    $start_text = !empty($options['azb_start_date_text']) ? $options['azb_start_date_text'] : __('Start', 'azb');
    $end_text = !empty($options['azb_end_date_text']) ? $options['azb_end_date_text'] : __('End', 'azb');

    $item_meta[] = '_start_date';
    $item_meta[] = '_end_date';

    return $item_meta;
}

add_action('woocommerce_payment_complete', 'azb_payment_complete');
add_action('woocommerce_order_status_processing', 'azb_payment_complete');
add_action('woocommerce_order_status_completed', 'azb_payment_complete');

function azb_payment_complete($id) {
    $order = new WC_Order($id);
    $items = $order->get_items();
    foreach ($items as $item_id => $item) {
        if ($item['type'] == 'line_item') {
            if (isset($item['item_meta']['_start_date']) && isset($item['item_meta']['_end_date'])) {
                $is_bookable = get_post_meta($item['product_id'], '_booking_option', true);
                if (isset($is_bookable) && $is_bookable === 'yes') {
                    $prev_availability = get_post_meta($item['product_id'], 'availability', true);
                    if (empty($prev_availability)) {
                        $availability = array();
                    } else {
                        $availability = json_decode($prev_availability, true);
                    }

                    $begin = new DateTime(reset($item['item_meta']['_start_date']));
                    $end = new DateTime(reset($item['item_meta']['_end_date']));
                    $end = $end->modify('+1 day');

                    $interval = DateInterval::createFromDateString('1 day');
                    $period = new DatePeriod($begin, $interval, $end);

                    delete_post_meta($item['product_id'], 'availability-days', false, true);
                    foreach ($period as $dt) {
                        $date = $dt->format("Y-m-d");
                        $availability[$date] = array('notes' => __('Order ID: ', 'azb') . $id);
                        add_post_meta($item['product_id'], 'availability-days', $date);
                    }
                    update_post_meta($item['product_id'], 'availability', json_encode($availability, JSON_FORCE_OBJECT), $prev_availability);
                }
            }
        }
    }
}
