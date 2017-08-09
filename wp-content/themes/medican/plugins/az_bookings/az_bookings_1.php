<?php

/*
  Plugin Name: AZEXO Bookings
  Plugin URI: http://azexo.com
  Description: AZEXO Bookings
  Text Domain: azb
  Domain Path: /languages
  Version: 1.22
  Author: azexo
  Author URI: http://azexo.com
  License: GNU General Public License version 3.0
 */

define('AZB_URL', plugins_url('', __FILE__));
define('AZB_DIR', trailingslashit(dirname(__FILE__)) . '/');
define('AZB_PLUGIN_FILE', __FILE__);

add_action('plugins_loaded', 'azb_plugins_loaded');

function azb_plugins_loaded() {
    load_plugin_textdomain('azb', FALSE, basename(dirname(__FILE__)) . '/languages/');

    include_once( 'includes/checkout.php' );

    if (is_admin()) {
        include_once( 'includes/admin/settings.php' );
        include_once( 'includes/admin/product-settings.php' );
        include_once( 'includes/admin/admin-assets.php' );
    } else {
        include_once( 'includes/product-archive-view.php' );
        include_once( 'includes/product-view.php' );
        include_once( 'includes/cart.php' );
    }
}

add_action('wp_enqueue_scripts', 'azb_enqueue_scripts');

function azb_enqueue_scripts() {
    wp_enqueue_script('azb', plugins_url('js/azb.js', AZB_PLUGIN_FILE), array('jquery-ui-datepicker'), '1.0', true);
    wp_localize_script('azb', 'azb', array(
        'homeurl' => home_url('/'),
        'ajaxurl' => admin_url('admin-ajax.php'),
    ));
}

add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'azb_add_settings_link');

function azb_add_settings_link($links) {
    $settings_link = '<a href="admin.php?page=azb">' . __('Settings', 'azb') . '</a>';
    array_push($links, $settings_link);

    return $links;
}

function azb_get_booking_price($product_id, $variation_id, $start_date, $end_date) {
    $id = !empty($variation_id) ? $variation_id : $product_id; // Product or variation id

    $product = wc_get_product($product_id); // Product object
    $_product = wc_get_product($id); // Product or variation object

    if ($product->is_type('variable') && empty($variation_id)) { // If no variation was selected 
        return false;
    }

    $price = azb_get_days_interval_price($_product, $start_date, $end_date);
    return wc_format_decimal($price);
}

function azb_get_days_interval_price($product, $start_date, $end_date) {
    $options = get_option('azb_settings');
    $calc_mode = $options['azb_calc_mode'];

    $begin = new DateTime($start_date);
    $end = new DateTime($end_date);
    if ($calc_mode === 'days') {
        $end = $end->modify('+1 day');
    }

    $interval = DateInterval::createFromDateString('1 day');
    $period = new DatePeriod($begin, $interval, $end);

    $price = 0;
    foreach ($period as $dt) {
        $price += azb_get_day_price($product, $dt->format("Y-m-d"));
    }
    return $price;
}

function azb_get_day_price($product, $day) {
    $price_by_days = get_post_meta($product->id, 'price_by_days', true);
    if (!empty($price_by_days)) {
        $price_by_days = json_decode($price_by_days, true);
        if (is_array($price_by_days)) {
            foreach ($price_by_days as $d => $data) {
                if ($day == $d) {
                    return $data['price'];
                }
            }
        }
    }
    return $product->get_price();
}

add_action('wp_ajax_azb_get_unavailable', 'azb_get_unavailable_callback');
add_action('wp_ajax_nopriv_azb_get_unavailable', 'azb_get_unavailable_callback');

function azb_get_unavailable_callback() {
    if (isset($_POST['ids']) && isset($_POST['date'])) {
        if (empty($_POST['ids'])) {
            print json_encode(array());
        } else {
            $args = array(
                'fields' => 'ids',
                'post_type' => 'any',
                'post_status' => 'any',
                'posts_per_page' => -1,
                'nopaging' => true,
                'post__in' => $_POST['ids'],
                'meta_query' => array(
                    array(
                        'key' => 'availability-days',
                        'value' => sanitize_text_field($_POST['date']),
                    )
                )
            );
            $posts = get_posts($args);
            print json_encode($posts);
        }
    }
    wp_die();
}
