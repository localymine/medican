<?php

add_filter('azqf_geolocation_render', 'azqf_geolocation_render', 10, 2);

function azqf_geolocation_render($output, $field) {
    if (function_exists('azl_google_maps_js')) {
        azl_google_maps_js();
    } else {
        $gmap_api_key = '';
        if (function_exists('cmb2_get_option')) {
            $gmap_api_key = cmb2_get_option('azqf_options', 'gmap_api_key');
        }
        wp_enqueue_script('google-maps', (is_ssl() ? 'https' : 'http') . '://maps.google.com/maps/api/js?sensor=false&libraries=places&key=' . $gmap_api_key);
    }
    wp_enqueue_script('jquery-ui-slider');

    $output = '<div class="geo-location">';

    if (isset($field['label'])) {
        $output .= '<label>' . esc_html($field['label']) . '</label>';
    }

    $output .= '<div class="location">';
    $output .= '<input id="mylocationsearch"  required = "true" name="location" type="text" placeholder="' . (isset($field['placeholder']) ? esc_attr($field['placeholder']) : '') . '" value="' . (isset($_GET['location']) ? sanitize_text_field($_GET['location']) : '') . '">';
	 $output .='<span class="location_error" style="display:none;"><h1>Please input location.</h1></span>';
    $output .= '<input name="latitude" type="hidden" value="' . (isset($_GET[$field['lat_meta_key']]) ? sanitize_text_field($_GET[$field['lat_meta_key']]) : '') . '">';
    $output .= '<input name="longitude" type="hidden" value="' . (isset($_GET[$field['lng_meta_key']]) ? sanitize_text_field($_GET[$field['lng_meta_key']]) : '') . '">';
    $output .= '</div>';


    $default_radius = isset($field['default_radius']) ? $field['default_radius'] : '300';
    $units = isset($field['units']) ? $field['units'] : 'mi';
    $output .= '<div class="radius">';
    $id = 'glr-' . rand(0, 99999999);
    $output .= '<input id="' . $id . '" type="checkbox" name="use_radius" checked="checked">';
    $output .= '<label for="' . $id . '">';
    $output .= esc_attr__('Radius:', 'azqf');
    $output .= '<span class="radius">' . (isset($_GET['radius']) ? sanitize_text_field($_GET['radius']) : esc_html($default_radius)) . '</span> <span class="units">(' . esc_html($units) . ')</span>';
    $output .= '</label>';

    $output .= '<div class="slider customradiousclass" id="customradiousclass">';
    $output .= '<input id="customradiusclass" pattern="[0-10]{1}"  id ="customradiusclass" oninvalid="setCustomValidity(\'Please enter a number (1-10) only\')"  oninput="setCustomValidity(\'\')" name="radius" min="1" max="10" type="number" placeholder="Radius:(km)" value="' . (isset($_GET['radius']) ? sanitize_text_field($_GET['radius']) : '') . '">';
    $output .= '</div>';

    $output .= '</div>';
 

    $output .= '</div>';
    return $output;
}

add_action('azqf_geolocation_process', 'azqf_geolocation_process', 10, 2);

function azqf_geolocation_process($query, $field) {
    $orderbyname = "";
    if(isset($query->query['orderby']))
        $orderbyname =  $query->query['orderby'];
        
    $use_radius = isset($_GET['use_radius']) && 'on' == $_GET['use_radius'];
    $lat = isset($_GET[$field['lat_meta_key']]) ? (float) $_GET[$field['lat_meta_key']] : false;
    $lng = isset($_GET[$field['lng_meta_key']]) ? (float) $_GET[$field['lng_meta_key']] : false;
    $radius = isset($_GET['radius']) ? (int) $_GET['radius'] : false;

    if ($use_radius && $lat && $lng && $radius) {
        global $wpdb;
        $r = '3959';
        if (isset($field['units'])) {
            if ($field['units'] == 'mi') {
                $r = '3959';
            }
            if ($field['units'] == 'km') {
                $r = '6371';
            }
        }
        if(($orderbyname=='alphabetical')||($orderbyname=='fev'))
        {
			if(($orderbyname=='alphabetical'))
			{
            $sql = $wpdb->prepare("
                   SELECT $wpdb->posts.ID, 
                       ( " . $r . " * acos( 
                           cos( radians(%s) ) * 
                           cos( radians( latitude.meta_value ) ) * 
                           cos( radians( longitude.meta_value ) - radians(%s) ) + 
                           sin( radians(%s) ) * 
                           sin( radians( latitude.meta_value ) ) 
                       ) ) 
                       AS distance, latitude.meta_value AS latitude, longitude.meta_value AS longitude
                       FROM $wpdb->posts
                       INNER JOIN $wpdb->postmeta 
                           AS latitude 
                           ON $wpdb->posts.ID = latitude.post_id
                       INNER JOIN $wpdb->postmeta 
                           AS longitude 
                           ON $wpdb->posts.ID = longitude.post_id
                       WHERE 1=1
                           AND ($wpdb->posts.post_status = 'publish' ) 
                           AND latitude.meta_key = %s
                           AND longitude.meta_key = %s
                       HAVING distance < %s

                      ORDER BY $wpdb->posts.post_title ASC, distance ASC", $lat, $lng, $lat, $field['lat_meta_key'], $field['lng_meta_key'], $radius

               );
			}
			else
			{
				
				  $sql = $wpdb->prepare("
                   SELECT $wpdb->posts.ID, 
                       ( " . $r . " * acos( 
                           cos( radians(%s) ) * 
                           cos( radians( latitude.meta_value ) ) * 
                           cos( radians( longitude.meta_value ) - radians(%s) ) + 
                           sin( radians(%s) ) * 
                           sin( radians( latitude.meta_value ) ) 
                       ) ) 
                       AS distance, latitude.meta_value AS latitude, longitude.meta_value AS longitude, _post_like_count.meta_value AS _post_like_count
                       FROM $wpdb->posts
                       INNER JOIN $wpdb->postmeta 
                           AS latitude 
                           ON $wpdb->posts.ID = latitude.post_id
                       INNER JOIN $wpdb->postmeta 
                           AS longitude 
                           ON $wpdb->posts.ID = longitude.post_id
						INNER JOIN $wpdb->postmeta 
                           AS _post_like_count 
                           ON $wpdb->posts.ID = _post_like_count.post_id   
                       WHERE 1=1
                           AND ($wpdb->posts.post_status = 'publish' ) 
                           AND latitude.meta_key = %s
                           AND longitude.meta_key = %s
						   AND _post_like_count.meta_key = '_post_like_count'
                       HAVING distance < %s

                      ORDER BY _post_like_count.meta_value DESC", $lat, $lng, $lat, $field['lat_meta_key'], $field['lng_meta_key'], $radius

               );
			  
			}
	
        }
        else {

                $sql = $wpdb->prepare("
                   SELECT $wpdb->posts.ID, 
                       ( " . $r . " * acos( 
                           cos( radians(%s) ) * 
                           cos( radians( latitude.meta_value ) ) * 
                           cos( radians( longitude.meta_value ) - radians(%s) ) + 
                           sin( radians(%s) ) * 
                           sin( radians( latitude.meta_value ) ) 
                       ) ) 
                       AS distance, latitude.meta_value AS latitude, longitude.meta_value AS longitude
                       FROM $wpdb->posts
                       INNER JOIN $wpdb->postmeta 
                           AS latitude 
                           ON $wpdb->posts.ID = latitude.post_id
                       INNER JOIN $wpdb->postmeta 
                           AS longitude 
                           ON $wpdb->posts.ID = longitude.post_id
                       WHERE 1=1
                           AND ($wpdb->posts.post_status = 'publish' ) 
                           AND latitude.meta_key = %s
                           AND longitude.meta_key = %s
                       HAVING distance < %s

                      ORDER BY $wpdb->posts.menu_order ASC, distance ASC", $lat, $lng, $lat, $field['lat_meta_key'], $field['lng_meta_key'], $radius

               );
        }
        $post_ids = $wpdb->get_results($sql, OBJECT_K);
        if (is_array($post_ids)) {
            
            if (empty($post_ids)) {
                $query->set('post__in', array(0));
            } else {
                $query->set('post__in', array_keys($post_ids));
                $query->set('orderby', 'post__in');
                $query->set('order', 'asc');
            }
        }
    }
}

add_action('azqf_geolocation_short', 'azqf_geolocation_short', 10, 3);

function azqf_geolocation_short($output, $field, $params) {
    $name = esc_html__('Location', 'azqf');
    if (isset($field['label'])) {
        $name = $field['label'];
    } else {
        if (isset($field['placeholder'])) {
            $name = $field['placeholder'];
        }
    }
    $location = isset($params['location']) ? $params['location'] : false;
    $lat = isset($params[$field['lat_meta_key']]) ? $params[$field['lat_meta_key']] : false;
    $lng = isset($params[$field['lng_meta_key']]) ? $params[$field['lng_meta_key']] : false;
    $radius = isset($params['radius']) ? (int) $params['radius'] : '';
    $units = isset($params['units']) ? (int) $params['units'] : 'mi';
    if ($location) {
        $value = $location . ' - ' . $radius . $units;
        $output .= '<dt>' . $name . '</dt>';
        $output .= '<dd>' . $value . '</dd>';
    } else {
        if (is_numeric($lat) && is_numeric($lng)) {
            $value = '(' . $lat . ', ' . $lng . ') - ' . $radius . $units;
            $output .= '<dt>' . $name . '</dt>';
            $output .= '<dd>' . $value . '</dd>';
        }
    }

    return $output;
}
