<?php

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

get_template_part('template-parts/general', 'title');

if ($original->ID != $post->ID) {
    wp_reset_postdata();
    $post = $original;
}