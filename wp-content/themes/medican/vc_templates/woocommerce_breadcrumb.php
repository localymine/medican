<?php

$output = '<div class="azwoo-breadcrumb">';
global $post;
$original = $post;
if (azexo_get_closest_current_post('page')) {
    $post = azexo_get_closest_current_post('page');
} else if (azexo_get_closest_current_post('vc_widget', false)) {
    $post = azexo_get_closest_current_post('vc_widget', false);
}
if ($original->ID != $post->ID) {
    setup_postdata($post);
}
ob_start();
woocommerce_breadcrumb(array(
    'delimiter' => ' <span class="delimiter">/</span> '
));
if ($original->ID != $post->ID) {
    wp_reset_postdata();
    $post = $original;
}
$output .= ob_get_clean();
$output .= '</div>';
print $output;
