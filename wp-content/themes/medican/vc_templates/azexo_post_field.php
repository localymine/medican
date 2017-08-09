<?php

$output = $field = $post_id = $el_class = $css = '';
extract(shortcode_atts(array(
    'post_id' => '',
    'field' => '',
    'el_class' => '',
    'css' => '',
                ), $atts));

$css_class = $el_class;
if (function_exists('vc_shortcode_custom_css_class')) {
    $css_class = apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $el_class . vc_shortcode_custom_css_class($css, ' '), $this->settings['base'], $atts);
}

if (is_numeric($post_id)) {
    global $post;
    $original = $post;
    $post = get_post($post_id);
    setup_postdata($post);
    print '<div class="field ' . esc_attr($css_class) . '">';
    print azexo_entry_field($field);
    print '</div>';
    wp_reset_postdata();
    $post = $original;
} else {
    global $azexo_fields_post_types;
    $post_type = 'post';
    if (is_array($azexo_fields_post_types) && isset($azexo_fields_post_types[$field])) {
        $post_type = $azexo_fields_post_types[$field];
    } else {
        $post_types = get_post_types();
        $parts = explode('-', $field);
        if (count($parts) > 1) {
            if (in_array($parts[0], $post_types)) {
                $post_type = $parts[0];
            }
        }
    }

    global $post;
    if ($post->post_type == $post_type) {
        print '<div class="field ' . esc_attr($css_class) . '">';
        print azexo_entry_field($field);
        print '</div>';
    } else {
        if ($post_type == 'post' || $post_type == '') {
            $field_post = azexo_get_closest_current_post(array('vc_widget', 'azh_widget'), false);
        } else {
            $field_post = azexo_get_closest_current_post($post_type);
        }
        if ($field_post) {
            global $post;
            $original = $post;
            $post = $field_post;
            setup_postdata($field_post);
            print '<div class="field ' . esc_attr($css_class) . '">';
            print azexo_entry_field($field);
            print '</div>';
            wp_reset_postdata();
            $post = $original;
        }
    }
}
