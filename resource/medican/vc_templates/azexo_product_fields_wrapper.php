<?php

$output = $el_class = $css = '';
extract(shortcode_atts(array(
    'el_class' => '',
    'css' => '',
                ), $atts));

$el_class = $this->getExtraClass($el_class);
$css_class = apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $el_class . vc_shortcode_custom_css_class($css, ' '), $this->settings['base'], $atts);

$product = azexo_get_closest_current_post('product');

if ($product) {

    global $post;
    $original = $post;
    $post = $product;
    setup_postdata($product);

    print '<div class="entry product ' . esc_attr($css_class) . '" itemscope itemtype="' . woocommerce_get_product_schema() . '">';
    print '<meta itemprop="url" content="' . esc_url( apply_filters( 'the_permalink', get_permalink() ) ) . '" />';
    print '<meta itemprop="image" content="' . esc_url(wp_get_attachment_url(get_post_thumbnail_id($product->ID))) . '" />';
    print wpb_js_remove_wpautop($content);
    print '</div>';

    wp_reset_postdata();
    $post = $original;
}