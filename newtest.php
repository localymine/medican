<?php
require_once("wp-load.php");


for($i=1; $i<=20; $i++)
{
$post_id = wp_insert_post( array(
'post_title' => 'Product '.$i,
'post_status' => 'publish',
'post_type' => 'product',
) );
wp_set_object_terms($post_id, 'simple', 'product_type'); 
wp_set_object_terms($post_id, 45, 'product_cat');
update_post_meta( $post_id, '_visibility', 'visible' );
update_post_meta( $post_id, '_stock_status', 'instock'); 
}


//$args = array(
//             'product_cat' => 'cardiologist',
//             'post_type' => 'product',
//             'posts_per_page' => '10000',
//           );
//



//$query = new WP_Query( $args );
//$i=0;
//while ( $query->have_posts() ) {
//	$query->the_post();
//	echo get_the_title().'<br>';
      /* $longitude =   get_post_meta(get_the_id(),'longitude',true);
      
        if($longitude)
        {
          echo  $id = get_the_id().'--------'.get_the_title().'<br>';   
//           add_post_meta( get_the_id(), '_post_like_count', '0', true );
        } */
//        print_r($query);
     
//       $abc = 0;

//echo $i;
//$i++;
//}

