<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

add_action('admin_enqueue_scripts', 'azb_enqueue_admin_scripts');

function azb_enqueue_admin_scripts() {
    global $post;
    $screen = get_current_screen();
    if (in_array($screen->id, array('product'))) {
        wp_enqueue_script('azb-admin-product', plugins_url('js/azb-admin-product.js', AZB_PLUGIN_FILE), array('jquery'), '1.0', true);
    }
}
