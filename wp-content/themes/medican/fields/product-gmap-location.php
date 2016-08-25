<?php
/*
  Field Name: Product GMAP Location
 */
?>

<?php
azl_google_map_scripts();
global $product;
$post_cats = wp_get_post_terms($product->id, 'product_cat', array('parent' => 0));
$cat_image = '';
if (!empty($post_cats)) {
    $post_cat = reset($post_cats);
    $cat_thumbnail_id = get_woocommerce_term_meta($post_cat->term_id, 'thumbnail_id', true);
    if ($cat_thumbnail_id) {
        $cat_image = wp_get_attachment_url($cat_thumbnail_id);
    }
}
$latitude = trim(get_post_meta($product->id, 'latitude', true));
$longitude = trim(get_post_meta($product->id, 'longitude', true));

if (!empty($latitude) && !empty($longitude)):
    
    ?>
    <div class="azl-map-wrapper single" 
         data-latitude="<?php print esc_attr(get_post_meta($product->id, 'latitude', true)) ?>" 
         data-longitude="<?php print esc_attr(get_post_meta($product->id, 'longitude', true)) ?>"
         data-category="<?php print esc_attr($cat_image) ?>"
         >
        <div class="controls">
            <div class="locate"></div>
            <div class="zoom-in"></div>
            <div class="zoom-out"></div>
            <a href="https://www.google.com/maps/dir/Current+Location/<?php print esc_attr($latitude) ?>,<?php print esc_attr($longitude) ?>" class="directions" target="_blank"></a>        
        </div>    
        <div class="azl-map"></div>
    </div>
    <?php

endif;