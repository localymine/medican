<?php

function azexo_tgmpa_register() {

    $plugins = array(
        array(
            'name' => esc_html__('Redux Framework', 'medican'),
            'slug' => 'redux-framework',
            'required' => true,
        ),
        array(
            'name' => esc_html__('Custom classes for page/post', 'medican'),
            'slug' => 'custom-classes',
            'source' => get_template_directory() . '/plugins/custom-classes.zip',
            'required' => true,
            'version' => '0.1',
        ),
        array(
            'name' => esc_html__('WordPress Importer', 'medican'),
            'slug' => 'wordpress-importer',
            'required' => true,
        ),
        array(
            'name' => esc_html__('WP-LESS', 'medican'),
            'slug' => 'wp-less',
        ),
        array(
            'name' => esc_html__('Infinite scroll', 'medican'),
            'slug' => 'infinite-scroll',
        ),
        array(
            'name' => esc_html__('Widget CSS Classes', 'medican'),
            'slug' => 'widget-css-classes',
        ),
        array(
            'name' => esc_html__('JP Widget Visibility', 'medican'),
            'slug' => 'jetpack-widget-visibility',
        ),
        array(
            'name' => esc_html__('Contact Form 7', 'medican'),
            'slug' => 'contact-form-7',
        ),
        array(
            'name' => esc_html__('Custom Sidebars', 'medican'),
            'slug' => 'custom-sidebars',
        ),
    );
    $plugin_path = get_template_directory() . '/plugins/js_composer.zip';
    if (file_exists($plugin_path)) {
        $plugins[] = array(
            'name' => esc_html__('WPBakery Visual Composer', 'medican'),
            'slug' => 'js_composer',
            'source' => get_template_directory() . '/plugins/js_composer.zip',
            'required' => true,
            'version' => '4.12.1',
            'external_url' => '',
        );
    }
    tgmpa($plugins, array());


    $additional_plugins = array(
        'vc_widgets' => esc_html__('Visual Composer Widgets', 'medican'),
        'azexo_vc_elements' => esc_html__('AZEXO Visual Composer elements', 'medican'),
        'az_social_login' => esc_html__('AZEXO Social Login', 'medican'),
        'az_email_verification' => esc_html__('AZEXO Email Verification', 'medican'),
        'az_likes' => esc_html__('AZEXO Post/Comments likes', 'medican'),
        'azexo_html' => esc_html__('AZEXO HTML cusomizer', 'medican'),
        'az_listings' => esc_html__('AZEXO Listings', 'medican'),
        'az_query_form' => esc_html__('AZEXO Query Form', 'medican'),
        'az_group_buying' => esc_html__('AZEXO Group Buying', 'medican'),
        'az_vouchers' => esc_html__('AZEXO Vouchers', 'medican'),
        'az_bookings' => esc_html__('AZEXO Bookings', 'medican'),
        'az_deals' => esc_html__('AZEXO Deals', 'medican'),
        'az_sport_club' => esc_html__('AZEXO Sport Club', 'medican'),
        'az_locations' => esc_html__('AZEXO Locations', 'medican'),
        'circular_countdown' => esc_html__('Circular CountDown', 'medican'),
    );

    foreach ($additional_plugins as $additional_plugin_slug => $additional_plugin_name) {
        $plugin_path = get_template_directory() . '/plugins/' . $additional_plugin_slug . '.zip';
        if (file_exists($plugin_path)) {
            $plugin = array(
                array(
                    'name' => $additional_plugin_name,
                    'slug' => $additional_plugin_slug,
                    'source' => $plugin_path,
                    'required' => true,
                    'version' => AZEXO_FRAMEWORK_VERSION,
                ),
            );
            tgmpa($plugin, array(
//                'is_automatic' => true,
            ));
        }
    }
}

add_action('tgmpa_register', 'azexo_tgmpa_register');
