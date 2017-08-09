<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

$product_type = $product->product_type;
?>

<div id="booking_product_data" class="panel woocommerce_options_panel">

    <div class="options_group show_if_variable">
        <?php
        woocommerce_wp_checkbox(array(
            'id' => '_manage_bookings',
            'label' => __('Manage bookings?', 'azb'),
            'description' => __('Check this box to manage bookings at product level.'),
            'value' => $product_type === 'variable' ? $post->_manage_bookings : 'yes'
        ));
        ?>

    </div>

    <div class="options_group show_if_simple show_if_grouped show_if_manage_bookings">

        <?php
        woocommerce_wp_text_input(array(
            'id' => '_booking_min',
            'label' => __('Minimum booking duration', 'azb'),
            'desc_tip' => 'true',
            'description' => __('Leave zero to set no duration limit. Leave empty to use the global settings.', 'azb'),
            'value' => isset($post->_booking_min) ? $post->_booking_min : '',
            'type' => 'number',
            'custom_attributes' => array(
                'step' => '1',
                'min' => '0'
        )));

        woocommerce_wp_text_input(array(
            'id' => '_booking_max',
            'label' => __('Maximum booking duration', 'azb'),
            'desc_tip' => 'true',
            'description' => __('Leave zero to set no duration limit. Leave empty to use the global settings.', 'azb'),
            'value' => isset($post->_booking_max) ? $post->_booking_max : '',
            'type' => 'number',
            'custom_attributes' => array(
                'step' => '1',
                'min' => '0'
        )));

        woocommerce_wp_text_input(array(
            'id' => '_first_available_date',
            'label' => __('First available date', 'azb'),
            'desc_tip' => 'true',
            'description' => __('First available date, relative to the current day. I.e. : today + 5 days. Leave zero for the current day. Leave empty to use the global settings.', 'azb'),
            'value' => isset($post->_first_available_date) ? $post->_first_available_date : '',
            'type' => 'number',
            'custom_attributes' => array(
                'step' => '1',
                'min' => '0'
        )));
        
        woocommerce_wp_text_input(array(
            'id' => '_max_year',
            'label' => __('Maximum year', 'azb'),
            'desc_tip' => 'true',
            'description' => __('Set the maximum year limit to allow bookings, relative to the current year. Leave zero for the current year. Leave empty to use the global settings.', 'azb'),
            'value' => isset($post->_max_year) ? $post->_max_year : '',
            'type' => 'number',
            'custom_attributes' => array(
                'step' => '1',
                'min' => '0'
        )));
        
        ?>

    </div>

    <?php do_action('azb_after_booking_options', $product); ?>
    <?php do_action('azb_after_' . $product_type . '_booking_options', $product); ?>

</div>