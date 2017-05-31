<?php

$atts = vc_map_get_attributes($this->getShortcode(), $atts);

$output = $el_class = '';
extract(shortcode_atts(array(
    'el_class' => '',
                ), $atts));
extract($atts);

$el_class = $this->getExtraClass($el_class);

$output = '<div class="woocommerce_widget_cart wpb_content_element' . esc_attr($el_class) . '">';
$type = 'WC_Widget_Cart';
$args = array();
global $wp_widget_factory;
// to avoid unwanted warnings let's check before using widget
if (is_object($wp_widget_factory) && isset($wp_widget_factory->widgets, $wp_widget_factory->widgets[$type])) {
    ob_start();
    the_widget($type, $atts, $args);
    $output .= ob_get_clean();

    $output .= '</div>' . $this->endBlockComment($this->getShortcode()) . "\n";

    print $output;
} else {
    print $this->endBlockComment('Widget ' . esc_attr($type) . 'Not found in : woocommerce_widget_cart');
}
