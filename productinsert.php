<?php
require_once("wp-load.php");
ini_set('max_execution_time', 0);
$file_handle = fopen('JHskKUoH6jhK.csv', 'r');
$i=1;

	$new = array();
    while(! feof($file_handle))
		{
		$data = array();
		$data  = fgetcsv($file_handle);
		$new[] = strip_tags($data[0]);
		
		$args = array('s' => strip_tags($data[0]));
		$the_query = new WP_Query( $args );

		// The Loop
		if ( $the_query->have_posts() ) {
//echo 'no'.'<br>';
		} else {
//			$post_id = wp_insert_post( array(
//				   'post_title' => strip_tags($data[0]),
//				   'post_status' => 'publish',
//				   'post_type' => 'product',
//				   'post_content' =>  strip_tags($data[0]),
//			   ) );
//		wp_set_object_terms($post_id, 'simple', 'product_type'); 
//		wp_set_object_terms($post_id, 22, 'product_cat');
//		update_post_meta( $post_id, '_visibility', 'visible' );
//		update_post_meta( $post_id, '_stock_status', 'instock');
		echo  $i.'---'.strip_tags($data[0]).'<br>';
		$i++;
		}  
	
	
	}

