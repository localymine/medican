<?php

function azexo_tgmpa_register() {

    $plugins = array(
        array(
            'name' => 'Redux Framework',
            'slug' => 'redux-framework',
            'required' => true,
        ),
        array(
            'name' => 'WPBakery Visual Composer',
            'slug' => 'js_composer',
            'source' => get_template_directory() . '/plugins/js_composer.zip',
            'required' => true,
            'version' => '4.11.2',
            'external_url' => '',
        ),
        array(
            'name' => 'Visual Composer Widgets',
            'slug' => 'vc_widgets',
            'source' => get_template_directory() . '/plugins/vc_widgets.zip',
            'required' => true,
            'version' => AZEXO_THEME_VERSION,
        ),
        array(
            'name' => 'AZEXO Visual Composer elements',
            'slug' => 'azexo_vc_elements',
            'source' => get_template_directory() . '/plugins/azexo_vc_elements.zip',
            'required' => true,
            'version' => AZEXO_THEME_VERSION,
        ),
        array(
            'name' => 'Custom classes for page/post',
            'slug' => 'custom-classes',
            'source' => get_template_directory() . '/plugins/custom-classes.zip',
            'required' => true,
            'version' => '0.1',
        ),
        array(
            'name' => 'WP-LESS',
            'slug' => 'wp-less',
            'required' => true,
        ),
        array(
            'name' => 'Infinite scroll',
            'slug' => 'infinite-scroll',
        ),
        array(
            'name' => 'WordPress Importer',
            'slug' => 'wordpress-importer',
            'required' => true,
        ),
        array(
            'name' => 'Widget CSS Classes',
            'slug' => 'widget-css-classes',
        ),
        array(
            'name' => 'JP Widget Visibility',
            'slug' => 'jetpack-widget-visibility',
        ),
        array(
            'name' => 'Contact Form 7',
            'slug' => 'contact-form-7',
        ),
        array(
            'name' => 'Custom Sidebars',
            'slug' => 'custom-sidebars',
        ),
        array(
            'name' => 'Widget - Flickr Badge Widget',
            'slug' => 'flickr-badges-widget',
        ),
        array(
            'name' => 'WP Instagram Widget',
            'slug' => 'wp-instagram-widget',
        ),
    );
    tgmpa($plugins, array());

    $additional_plugins = array(
        'az_listings' => 'AZEXO Listings',
        'az_query_form' => 'AZEXO Query Form',
        'az_group_buying' => 'AZEXO Group Buying',
        'az_vouchers' => 'AZEXO Vouchers',
        'az_bookings' => 'AZEXO Bookings',
        'az_deals' => 'AZEXO Deals',
        'az_sport_club' => 'AZEXO Sport Club',
        'az_locations' => 'AZEXO Locations',
        'circular_countdown' => 'Circular CountDown',
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
                    'version' => AZEXO_THEME_VERSION,
                ),
            );
            tgmpa($plugin, array());
        }
    }
}

add_action('tgmpa_register', 'azexo_tgmpa_register');
