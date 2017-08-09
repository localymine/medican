<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}


$options = get_option('azb_settings');

// initialize options the first time
if (!$options) {

    $options = array('azb_calc_mode' => 'nights',
        'azb_all_bookable' => 'no',
        'azb_booking_min' => '',
        'azb_booking_max' => '',
        'azb_first_available_date' => '',
        'azb_max_year' => 3,
        'azb_info_text' => '',
        'azb_start_date_text' => __('Check In', 'azb'),
        'azb_end_date_text' => __('Check Out', 'azb'),
    );

    add_option('azb_settings', $options);
}

if (is_multisite()) {

    $global_settings = get_option('azb_global_settings');

    if (!$global_settings) {
        $global_settings = array();
        add_option('azb_global_settings', $global_settings);
    }
}


if (is_admin()) {

    add_action('admin_menu', 'azb_add_option_pages', 10);

    if (is_multisite()) {
        add_action('network_admin_menu', 'azb_add_multisite_option_pages');
    }

    add_action('admin_init', 'azb_admin_init');
}

function azb_add_multisite_option_pages() {
//    $hook = add_menu_page(
//            __('Bookings', 'azb'), __('Bookings', 'azb'), 'manage_options', 'azb', '', 'dashicons-calendar-alt', 58
//    );

    $option_page = add_submenu_page(
            'woocommerce', __('Global Bookings Settings', 'azb'), __('Global Bookings Settings', 'azb'), 'manage_options', 'azb', 'azb_global_option_page'
    );
}

function azb_add_option_pages() {
//    $hook = add_menu_page(
//            __('Bookings', 'azb'), __('Bookings', 'azb'), 'manage_options', 'azb', '', 'dashicons-calendar-alt', 58
//    );

    $option_page = add_submenu_page(
            'woocommerce', __('Bookings Settings', 'azb'), __('Bookings Settings', 'azb'), 'manage_options', 'azb', 'azb_option_page'
    );

    add_action('load-' . $option_page, 'azb_settings_save');
}

function azb_settings_save() {
    if (isset($_GET['settings-updated']) && $_GET['settings-updated']) {
        azb_apply_global_product_settings();
    }
}

function azb_admin_init() {

    register_setting(
            'azb_settings', 'azb_settings', 'azb_sanitize_values'
    );

    add_settings_section(
            'azb_main_settings', __('General settings', 'azb'), 'azb_section_general', 'azb_settings'
    );

    add_settings_field(
            'azb_calc_mode', __('Calculation mode', 'azb'), 'azb_calc_mode', 'azb_settings', 'azb_main_settings'
    );

    add_settings_field(
            'azb_all_bookable', __('Make all products bookable?', 'azb'), 'azb_all_bookable', 'azb_settings', 'azb_main_settings'
    );

    add_settings_field(
            'azb_booking_min', __('Minimum booking duration', 'azb'), 'azb_booking_min', 'azb_settings', 'azb_main_settings'
    );

    add_settings_field(
            'azb_booking_max', __('Maximum booking duration', 'azb'), 'azb_booking_max', 'azb_settings', 'azb_main_settings'
    );

    add_settings_field(
            'azb_first_available_date', __('First available date', 'azb'), 'azb_first_available_date', 'azb_settings', 'azb_main_settings'
    );

    add_settings_field(
            'azb_max_year', __('Booking limit', 'azb'), 'azb_max_year', 'azb_settings', 'azb_main_settings'
    );

    add_settings_section(
            'azb_main_text', __('Text settings', 'azb'), 'azb_section_text', 'azb_settings'
    );

    add_settings_field(
            'azb_info_text', __('Information text', 'azb'), 'azb_info', 'azb_settings', 'azb_main_text'
    );

    add_settings_field(
            'azb_start_date_text', __('First date title', 'azb'), 'azb_start_date', 'azb_settings', 'azb_main_text'
    );

    add_settings_field(
            'azb_end_date_text', __('Second date title', 'azb'), 'azb_end_date', 'azb_settings', 'azb_main_text'
    );


// Multisite settings
    if (is_multisite()) {

        register_setting(
                'azb_global_settings', 'azb_global_settings', 'azb_sanitize_values'
        );
    }
}

function azb_option_page() {
    ?><div class="wrap">

        <div id="azb-settings">

            <h2><?php _e('WooCommerce Bookings settings', 'azb'); ?></h2>

            <form method="post" action="options.php">

                <?php settings_fields('azb_settings'); ?>
                <?php do_settings_sections('azb_settings'); ?>

                <?php submit_button(); ?>

            </form>

        </div>

    </div>
    <?php
}

function azb_global_option_page() {
    ?><div class="wrap">

        <div id="azb-settings">

            <h2><?php _e('Network settings for WooCommerce Bookings', 'azb'); ?></h2>
            <form method="post" action="<?php echo admin_url(); ?>options.php">

                <?php settings_fields('azb_global_settings'); ?>
                <?php do_settings_sections('azb_global_settings'); ?>

                <?php submit_button(); ?>

            </form>

        </div>

    </div>
    <?php
}

function azb_section_general() {
    echo '';
}

function azb_calc_mode() {
    $options = get_option('azb_settings');
    $calc_mode = isset($options['azb_calc_mode']) ? $options['azb_calc_mode'] : 'nights';

    echo '<select id="calc_mode" name="azb_settings[azb_calc_mode]">
			<option value="days"' . selected($calc_mode, 'days', false) . '>' . __('Days', 'azb') . '</option>
			<option value="nights"' . selected($calc_mode, 'nights', false) . '>' . __('Nights', 'azb') . '</option>
		</select>
		<p class="description">' . __('Choose whether to calculate the final price depending on number of days or number of nights (i.e. 5 days = 4 nights).', 'azb') . '</p>';
}

function azb_all_bookable() {
    $options = get_option('azb_settings');
    $all_bookable = isset($options['azb_all_bookable']) ? $options['azb_all_bookable'] : '';
    echo '<input type="checkbox" id="azb_all_bookable" name="azb_settings[azb_all_bookable]"' . checked($all_bookable, 'on', false) . '/>
		<p class="description">' . __('Check to make all your products bookable. Any new or modified product will be automatically bookable.', 'azb') . '</p>';
}

function azb_booking_min() {
    $options = get_option('azb_settings');
    $min_booking = isset($options['azb_booking_min']) ? absint($options['azb_booking_min']) : '';
    echo '<input type="number" name="azb_settings[azb_booking_min]" size="40" min="0" step="1" value="' . $min_booking . '">
		<p class="description">' . __('Set a minimum booking duration for all your products. You can individually change it on your product settings. Leave 0 or empty to set no duration limit.', 'azb') . '</p>';
}

function azb_booking_max() {
    $options = get_option('azb_settings');
    $max_booking = isset($options['azb_booking_max']) ? absint($options['azb_booking_max']) : '';
    echo '<input type="number" name="azb_settings[azb_booking_max]" size="40" min="0" step="1" value="' . $max_booking . '">
		<p class="description">' . __('Set a maximum booking duration for all your products. You can individually change it on your product settings. Leave 0 or empty to set no duration limit.', 'azb') . '</p>';
}

function azb_first_available_date() {
    $options = get_option('azb_settings');
    $first_date = isset($options['azb_first_available_date']) ? absint($options['azb_first_available_date']) : '';
    echo '<input type="number" name="azb_settings[azb_first_available_date]" size="40" min="0" step="1" value="' . $first_date . '">
		<p class="description">' . __('Set the first available date for all your products, relative to the current day. You can individually change it on your product settings. Leave 0 or empty to keep the current day.', 'azb') . '</p>';
}

function azb_max_year() {
    $max_year = isset($options['azb_max_year']) ? $options['azb_max_year'] : 3;

    echo '<input type="number" name="azb_settings[azb_max_year]" size="40" min="0" step="1" value="' . $max_year . '" required>
		<p class="description">' . __('Set the maximum year limit to allow bookings, relative to the current year.', 'azb') . '</p>';
}

function azb_section_text() {
    echo '<p>' . __('Make this plugin yours by choosing the different texts you want to display !', 'azb') . '</p>';
}

function azb_info() {
    $options = get_option('azb_settings');
    echo '<textarea id="azb_text_info" name="azb_settings[azb_info_text]" rows="4" cols="50" />' . $options['azb_info_text'] . '</textarea>
		<p class="description">' . __('Displays an information text before date inputs. Leave empty if you don\'t want the information text.', 'azb') . '</p>';
}

function azb_start_date() {
    $options = get_option('azb_settings');
    echo '<input id="azb_start_date_text" name="azb_settings[azb_start_date_text]" size="40" type="text" value="' . $options['azb_start_date_text'] . '" />
		<p class="description">' . __('Text displayed before the first date', 'azb') . '</p>';
}

function azb_end_date() {
    $options = get_option('azb_settings');
    echo '<input id="azb_end_date_text" name="azb_settings[azb_end_date_text]" size="40" type="text" value="' . $options['azb_end_date_text'] . '" />
		<p class="description">' . __('Text displayed before the second date', 'azb') . '</p>';
}

function azb_apply_global_product_settings() {
    $options = get_option('azb_settings');
    $all_bookable = isset($options['azb_all_bookable']) ? $options['azb_all_bookable'] : '';
    $min_booking = isset($options['azb_booking_min']) ? absint($options['azb_booking_min']) : '';
    $max_booking = isset($options['azb_booking_max']) ? absint($options['azb_booking_max']) : '';
    $first_date = isset($options['azb_first_available_date']) ? absint($options['azb_first_available_date']) : '';

    $args = array(
        'post_type' => array('product', 'product_variation'),
        'posts_per_page' => -1,
        'post_status' => 'publish'
    );

    $query = new WP_Query($args);

    if ($query)
        while ($query->have_posts()) : $query->the_post();
            global $post;

            $post_id = $post->ID;

            if (!empty($all_bookable))
                update_post_meta($post_id, '_booking_option', 'yes');

            if (!empty($min_booking)) {
                $min_booking_set = get_post_meta($post_id, '_booking_min', true);

                if (empty($min_booking_set) && $min_booking_set != '0')
                    update_post_meta($post_id, '_booking_min', $min_booking);
            }

            if (!empty($max_booking)) {
                $max_booking_set = get_post_meta($post_id, '_booking_max', true);

                if (empty($max_booking_set) && $max_booking_set != '0')
                    update_post_meta($post_id, '_booking_max', $max_booking);
            }

            if (!empty($first_date)) {
                $first_date_set = get_post_meta($post_id, '_first_available_date', true);

                if (empty($first_date_set) && $first_date_set != '0')
                    update_post_meta($post_id, '_first_available_date', $first_date);
            }

        endwhile;
}

function azb_sanitize_values($settings) {

    foreach ($settings as $key => $value) {
        $settings[$key] = esc_html($value);
    }

    return $settings;
}
