<?php
add_action('admin_menu', 'azh_admin_menu');

function azh_admin_menu() {
    add_menu_page(__('HTML Customizer', 'azh'), __('HTML Customizer', 'azh'), 'manage_options', 'azh-settings', 'azh_settings_page');
}

function azh_settings_page() {
    ?>

    <div class="wrap">
        <?php screen_icon(); ?>
        <h2><?php _e('AZEXO HTML Customizer Settings', 'azh'); ?></h2>

        <form method="post" action="options.php" class="azh-form">
            <?php
            settings_errors();
            settings_fields('azh-settings');
            do_settings_sections('azh-settings');
            submit_button('Save Settings');
            ?>
        </form>
    </div>

    <?php
}

function azh_general_options_callback() {
    
}

function azh_license_callback() {
    
}

function azh_active_license_callback() {
    ?>
        <p><?php echo esc_html_e('Active license', 'azh'); ?></p>
    <?php
}

add_action('admin_init', 'azh_general_options');

function azh_general_options() {
    register_setting('azh-settings', 'azh-settings');

    add_settings_section(
            'azh_license_section', // Section ID
            esc_html__('Product license', 'azh'), // Title above settings section
            'azh_license_callback', // Name of function that renders a description of the settings section
            'azh-settings'                     // Page to show on
    );
    if (azexo_is_activated()) {
        add_settings_field(
                'oauth_keys', // Field ID
                esc_html__('Status'), // Label to the left
                'azh_active_license_callback', // Name of function that renders options on the page
                'azh-settings', // Page to show on
                'azh_license_section' // Associate with which settings section?
        );
    } else {
        add_settings_field(
                'oauth_keys', // Field ID
                esc_html__('Login with Envato to activate'), // Label to the left
                'azexo_oauth_login_callback', // Name of function that renders options on the page
                'azh-settings', // Page to show on
                'azh_license_section' // Associate with which settings section?
        );
    }


//    add_settings_section(
//            'azh_general_options_section', // Section ID
//            esc_html__('General options', 'azh'), // Title above settings section
//            'azh_general_options_callback', // Name of function that renders a description of the settings section
//            'azh-settings'                     // Page to show on
//    );
//    add_settings_field(
//            'envato_username', // Field ID
//            esc_html__('Envato username', 'azh'), // Label to the left
//            'azh_textfield', // Name of function that renders options on the page
//            'azh-settings', // Page to show on
//            'azh_general_options_section', // Associate with which settings section?
//            array(
//        'id' => 'envato_username'
//            )
//    );
//    add_settings_field(
//            'envato_api_key', // Field ID
//            esc_html__('Envato api key', 'azh'), // Label to the left
//            'azh_textfield', // Name of function that renders options on the page
//            'azh-settings', // Page to show on
//            'azh_general_options_section', // Associate with which settings section?
//            array(
//        'id' => 'envato_api_key'
//            )
//    );
//    add_settings_field(
//            'azh_purchase_code', // Field ID
//            esc_html__('AZEXO HTML Customizer purchase code', 'azh'), // Label to the left
//            'azh_textfield', // Name of function that renders options on the page
//            'azh-settings', // Page to show on
//            'azh_general_options_section', // Associate with which settings section?
//            array(
//        'id' => 'azh_purchase_code'
//            )
//    );
}

function azh_textfield($args) {
    extract($args);
    $options = get_option('azh-settings');
    ?>
    <input type="text" name="azh-settings[<?php print esc_attr($id); ?>]" value="<?php print esc_attr($options[$id]); ?>">
    <?php
}
