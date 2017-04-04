<?php
require_once("wp-load.php");
//wp_update_post(
//            array (
//                'ID'        => 2765,
//                'post_name' => 'eee'
//            )
//        );

$args = array(
            'product_cat' => 'vendorstore',
            'post_type' => 'product',
             'posts_per_page' => '10000',
           );
$query = new WP_Query( $args );
$i=0;
while ( $query->have_posts() ) {
	$query->the_post();
        $id = get_the_id();
       echo $domain = get_post_meta($id,'store_domain',true).'<br>';
wp_update_post(
            array (
                'ID'        => $id,
                'post_name' => $domain,
            )
        );
echo $i;
$i++;
}