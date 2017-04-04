<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

add_filter('woocommerce_loop_add_to_cart_link', 'azb_custom_loop_add_to_cart', 10, 2);

function azb_custom_loop_add_to_cart($content, $product) {
    global $post, $product;

    if (!$product)
        return;

    // Is product bookable ?
    $is_bookable = get_post_meta($product->id, '_booking_option', true);

    // If product is bookable
    if (isset($is_bookable) && $is_bookable === 'yes') {
        $link = get_permalink($product->id);
        $label = __('Select dates', 'azb');
        return '<a href="' . esc_url($link) . '" rel="nofollow" class="product_type_variable button">' . esc_html($label) . '</a>';
    } else {
        return $content;
    }
}
