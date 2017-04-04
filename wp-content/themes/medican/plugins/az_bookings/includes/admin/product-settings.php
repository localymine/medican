<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}


$azb_settings = get_option('azb_settings');

$all_bookable = isset($azb_settings['azb_all_bookable']) ? $azb_settings['azb_all_bookable'] : '';
$global_booking_min = isset($azb_settings['azb_booking_min']) ? absint($azb_settings['azb_booking_min']) : '';
$global_booking_max = isset($azb_settings['azb_booking_max']) ? absint($azb_settings['azb_booking_max']) : '';
$global_first_available_date = isset($azb_settings['azb_first_available_date']) ? absint($azb_settings['azb_first_available_date']) : '';
$global_max_year = isset($azb_settings['azb_max_year']) ? absint($azb_settings['azb_max_year']) : 3;

add_action('product_type_options', 'azb_add_product_option_pricing');
add_action('woocommerce_variation_options', 'azb_set_variation_booking_option', 10, 3);
add_action('woocommerce_product_after_variable_attributes', 'azb_add_variation_booking_options', 10, 3);
add_filter('woocommerce_product_data_tabs', 'azb_add_booking_tab', 10, 1);
add_action('woocommerce_product_data_panels', 'azb_add_booking_data_panel');
add_filter('woocommerce_process_product_meta_simple', 'azb_save_booking_options');
add_filter('woocommerce_process_product_meta_grouped', 'azb_save_booking_options');
add_filter('woocommerce_process_product_meta_variable', 'azb_save_variable_booking_options');
add_action('woocommerce_save_product_variation', 'azb_save_variation_booking_options', 10, 2);

/**
 *
 * Adds a checkbox to the product admin page to set the product as bookable
 *
 * @param array $product_type_options
 * @return array $product_type_options
 *
 * */
function azb_add_product_option_pricing($product_type_options) {
    global $post;

// Backward compatibility
    $is_bookable = get_post_meta($post->ID, '_booking_option', true);

    $product_type_options['booking_option'] = array(
        'id' => '_booking_option',
        'wrapper_class' => 'show_if_simple show_if_variable show_if_grouped',
        'label' => __('Bookable', 'azb'),
        'description' => __('Bookable products can be rent or booked on a daily schedule', 'azb'),
        'default' => $is_bookable === 'yes' ? 'yes' : 'no'
    );

    return $product_type_options;
}

/**
 *
 * Adds a checkbox to the product variation to set it as bookable
 *
 * @param int $loop
 * @param array $variation_data
 * @param obj $variation
 *
 * */
function azb_set_variation_booking_option($loop, $variation_data, $variation) {
    global $post;

    $is_bookable = get_post_meta($variation->ID, '_booking_option', true);
    ?>

    <label class="show_if_bookable"><input type="checkbox" class="checkbox variable_is_bookable" name="_var_booking_option[<?php echo $loop; ?>]" <?php checked($is_bookable, 'yes') ?> /> <?php _e('Bookable', 'woocommerce'); ?></label>

    <?php
}

function azb_add_variation_booking_options($loop, $variation_data, $variation) {
    $variation_id = $variation->ID;
    $product = wc_get_product($variation_id);

    $booking_min = get_post_meta($variation_id, '_booking_min', true);
    $booking_max = get_post_meta($variation_id, '_booking_max', true);
    $first_available_date = get_post_meta($variation_id, '_first_available_date', true);
    $max_year = get_post_meta($variation_id, '_max_year', true);    

    include('views/variation-booking-options.php');
}

/**
 *
 * Adds a booking tab to the product admin page for booking options
 *
 * @param array $product_data_tabs
 * @return array $product_data_tabs
 *
 * */
function azb_add_booking_tab($product_data_tabs) {

    $product_data_tabs['AZB'] = array(
        'label' => __('Bookings', 'azb'),
        'target' => 'booking_product_data',
        'class' => array('show_if_simple show_if_variable show_if_grouped show_if_bookable'),
    );

    return $product_data_tabs;
}

/**
 *
 * Adds booking options in the booking tab
 *
 * */
function azb_add_booking_data_panel() {
    global $post;

    $product = wc_get_product($post->ID);
    include('views/product-booking-options.php');
}

/**
 *
 * Saves checkbox value and booking options for the product
 *
 * @param int $post_id
 *
 * */
function azb_save_booking_options($post_id) {
    $is_bookable = isset($_POST['_booking_option']) ? 'yes' : '';

    if (!empty($all_bookable))
        $is_bookable = 'yes';

    $data = array(
        'booking_min' => $_POST['_booking_min'],
        'booking_max' => $_POST['_booking_max'],
        'first_available_date' => $_POST['_first_available_date'],
        'max_year' => $_POST['_max_year'],        
    );

    foreach ($data as $name => $value) {
        $global = 'global_' . $name;

        switch ($value) {
            case '' :
                $$name = !empty(${$global}) ? ${$global} : '';
                break;

            case 0 :
                $$name = '0';
                break;

            default :
                $$name = absint($value);
                break;
        }
    }

    if ($booking_min != 0 && $booking_max != 0 && $booking_min > $booking_max) {
        WC_Admin_Meta_Boxes::add_error(__('Minimum booking duration must be inferior to maximum booking duration', 'azb'));
    } else {
        update_post_meta($post_id, '_booking_min', $booking_min);
        update_post_meta($post_id, '_booking_max', $booking_max);
    }

    update_post_meta($post_id, '_first_available_date', $first_available_date);
    update_post_meta($post_id, '_max_year', $max_year);    
    update_post_meta($post_id, '_booking_option', $is_bookable);
}

function azb_save_variable_booking_options($post_id) {
    $is_bookable = isset($_POST['_booking_option']) ? 'yes' : '';

    if (!empty($all_bookable))
        $is_bookable = 'yes';

    $manage_bookings = isset($_POST['_manage_bookings']) ? 'yes' : '';

    $data = array(
        'booking_min' => $_POST['_booking_min'],
        'booking_max' => $_POST['_booking_max'],
        'first_available_date' => $_POST['_first_available_date'],
        'max_year' => $_POST['_max_year'],
    );

    foreach ($data as $name => $value) {
        $global = 'global_' . $name;

        switch ($value) {
            case '' :
                $$name = !empty(${$global}) ? ${$global} : '';
                break;

            case 0 :
                $$name = '0';
                break;

            default :
                $$name = absint($value);
                break;
        }
    }

    if ($booking_min != 0 && $booking_max != 0 && $booking_min > $booking_max) {
        WC_Admin_Meta_Boxes::add_error(__('Minimum booking duration must be inferior to maximum booking duration', 'azb'));
    } else {
        update_post_meta($post_id, '_booking_min', $booking_min);
        update_post_meta($post_id, '_booking_max', $booking_max);
    }

    update_post_meta($post_id, '_first_available_date', $first_available_date);
    update_post_meta($post_id, '_max_year', $max_year);
    update_post_meta($post_id, '_booking_option', $is_bookable);
    update_post_meta($post_id, '_manage_bookings', $manage_bookings);
}

function azb_save_variation_booking_options($variation_id, $i) {
    $is_bookable = isset($_POST['_var_booking_option'][$i]) ? 'yes' : '';

    if (!empty($all_bookable))
        $is_bookable = 'yes';

    $data = array(
        'booking_min' => $_POST['_var_booking_min'][$i],
        'booking_max' => $_POST['_var_booking_max'][$i],
        'first_available_date' => $_POST['_var_first_available_date'][$i],
        'max_year' => $_POST['_var_max_year'][$i],
    );

    foreach ($data as $name => $value) {

        switch ($value) {
            case '' :
                $$name = '';
                break;

            case 0 :
                $$name = '0';
                break;

            default :
                $$name = absint($value);
                break;
        }
    }

    if ($booking_min != 0 && $booking_max != 0 && $booking_min > $booking_max) {
        WC_Admin_Meta_Boxes::add_error(__('Minimum booking duration must be inferior to maximum booking duration', 'azb'));
    } else {
        update_post_meta($variation_id, '_booking_min', $booking_min);
        update_post_meta($variation_id, '_booking_max', $booking_max);
    }

    update_post_meta($variation_id, '_first_available_date', $first_available_date);
    update_post_meta($variation_id, '_max_year', $max_year);    
    update_post_meta($variation_id, '_booking_option', $is_bookable);
}
