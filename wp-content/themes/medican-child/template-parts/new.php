<?php
global $wpdb;
global $current_user;
 $author =  $current_user->ID;
   
    $product_query_args = array(
    'post_type'=>'product',
    'meta_key' => '_post_like_count',
    'meta_value' => '0',
    'meta_compare' => '!=',
    'posts_per_page' => 1000,
  );
 $product_query = new WP_Query( $product_query_args );
 $total_count = 0;
 while( $product_query->have_posts() )
    {
          $product_query->the_post();
          $productid = get_the_id();
          $data =  get_post_meta($productid,'_user_liked',true);
          $tmp = (array)$data;
         if(is_array($tmp))
          {
           
            if(in_array($author,$tmp))
             {
             $located = wc_locate_template('content-product.php');
             if (file_exists($located)) {
                               include( $located );
							   $total_count++;
                           }
             }
			
          }
    }
	
	if($total_count==0)
	{
		
		echo "No stores in this Category";
		echo "<script type='text/javascript'>jQuery(window).load(function(){jQuery('.template-part').css('text-align','center'); jQuery('.template-part').find('.owl-stage-outer').remove(); jQuery('.template-part').find('.owl-controls').remove();});</script>";
	}
?>

<?php wp_reset_postdata(); ?>