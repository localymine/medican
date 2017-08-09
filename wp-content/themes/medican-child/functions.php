<?php
/*add_action( 'wp_enqueue_scripts', 'enqueue_parent_styles' );
function enqueue_parent_styles() {
    wp_enqueue_style( 'style.css', get_stylesheet_directory_uri().'/style.css' );
}*/

function prefix_add_footer_styles() {
    wp_enqueue_style( 'style.css', get_stylesheet_directory_uri().'/style.css' );
};
add_action( 'get_footer', 'prefix_add_footer_styles' );

function wpdocs_theme_name_scripts() {
   wp_enqueue_style( 'demo-page.css', get_stylesheet_directory_uri().'/css/demo-page.css' );
   wp_enqueue_style( 'hover.css', get_stylesheet_directory_uri().'/css/hover.css' );
}
add_action( 'wp_enqueue_scripts', 'wpdocs_theme_name_scripts');

function remove_my_action(){
 remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
 add_action( 'woocommerce_before_shop_loop', 'woocommerce_pagination', 20 );
 add_action( 'woocommerce_after_shop_loop', 'woocommerce_pagination', 20 );
}
add_action( 'woocommerce_before_shop_loop', 'remove_my_action');

//Search by product domain name
add_action('pre_get_posts', 'jc_woo_search_pre_get_posts');
function jc_woo_search_pre_get_posts($q){
 
    if ( is_search() ) {
        add_filter( 'posts_join', 'jc_search_post_join' );
        add_filter( 'posts_where', 'jc_search_post_excerpt' );
    }
}
function jc_search_post_join($join = ''){
 
    global $wp_the_query;
   if ( empty( $wp_the_query->query_vars['wc_query'] ) || empty( $wp_the_query->query_vars['s'] ) )
            return $join;
 
    $join .= "INNER JOIN wp_postmeta AS jcmt1 ON (wp_posts.ID = jcmt1.post_id)";
    return $join;
}

function jc_search_post_excerpt($where = ''){
global $wp_the_query;
      if ( empty( $wp_the_query->query_vars['wc_query'] ) || empty( $wp_the_query->query_vars['s'] ) )
            return $where;
 
    $where = preg_replace("/post_title LIKE ('%[^%]+%')/", "post_title LIKE $1)
                 OR  (jcmt1.meta_key = 'store_domain' AND CAST(jcmt1.meta_value AS CHAR) LIKE $1 ", $where);
 
    return $where;
}

//Change the order number 

//add_filter( 'woocommerce_order_number', 'prefix_woocommerce_order_number', 1, 2 );
//function prefix_woocommerce_order_number( $oldnumber, $order ) {
//$ordertype = get_post_meta($order->id, 'order_type', true );
//    if($ordertype=='input')
//    {
//      return 'A000' . $order->id;  
//    }
//    else if($ordertype=='upload')
//    {
//     return 'B000' . $order->id;   
//    }
//    else
//    {
//     return $order->id;      
//    }   
//}
//end of Change the order number 



//end of Search by product domain name meta key.

//add_filter( 'woocommerce_order_number', 'webendev_woocommerce_order_number', 1, 2 );
//function webendev_woocommerce_order_number( $oldnumber, $order ) {
////    $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
////    $charactersLength = strlen($characters);
////    $randomString = '';
////    for ($i = 0; $i < 3; $i++) {
////        $randomString .= $characters[rand(0, $charactersLength - 1)];
////    }
//    return 'B'. $order->id;
//}




//product sort filter on seach page.
//function my_woocommerce_catalog_orderby( $orderby ) {
//    unset($orderby["longitude-desc"]);
//    return $orderby;
//}
//add_filter( "woocommerce_catalog_orderby", "my_woocommerce_catalog_orderby", 20 );  
//
function skyverge_change_default_sorting_name( $catalog_orderby ) {
    $catalog_orderby = str_replace("Default sorting", "Sort by Nearest Distance", $catalog_orderby);
    return $catalog_orderby;
}
add_filter( 'woocommerce_catalog_orderby', 'skyverge_change_default_sorting_name' );
add_filter( 'woocommerce_default_catalog_orderby_options', 'skyverge_change_default_sorting_name' );
//
//function longitude_change_default_sorting_name( $catalog_orderby ) {
//    $catalog_orderby = str_replace("Sort by longitude: low to high", "Nearest Distance", $catalog_orderby);
//    return $catalog_orderby;
//}
//add_filter( 'woocommerce_catalog_orderby', 'longitude_change_default_sorting_name' );
//add_filter( 'woocommerce_default_catalog_orderby_options', 'longitude_change_default_sorting_name' );
//
//function popular_change_default_sorting_name( $catalog_orderby ) {
//    $catalog_orderby = str_replace("Sort by popularity", "Most Favorited", $catalog_orderby);
//    return $catalog_orderby;
//}
//add_filter( 'woocommerce_catalog_orderby', 'popular_change_default_sorting_name' );
//add_filter( 'woocommerce_default_catalog_orderby_options', 'popular_change_default_sorting_name' );
//
//



//function theme_modify_the_main_query( $query ) {
//	// Order by: sale
//	if ( isset( $_GET['orderby'] ) && $_GET['orderby'] == 'menu_order' ) {
//           
//		// Modify the main query
////		if( ! is_admin() && $query->is_main_query() ) {	
//// 
////			// Get from Cars category
////	        $tax_query = array(
////		        array(
////		            'taxonomy' => 'product_cat',
////		            'field' => 'slug',
////		            'terms' => 'cars' // Cars category (term) slug
////		        )
////		    );
//// 
////		    $query->set( 'tax_query', $tax_query );
//// 
////	        $query->set( 'meta_key', '_sale_price' );
////	        $query->set( 'orderby', 'meta_value_num' );
////	        $query->set( 'order', 'DESC' );
////        }
//	}
//}
//add_action( 'pre_get_posts', 'theme_modify_the_main_query' );



//function sip_alphabetical_shop_ordering( $sort_args ) {
//$orderby_value = isset( $_GET['orderby'] ) ? woocommerce_clean( $_GET['orderby'] ) : apply_filters( 'woocommerce_default_catalog_orderby', get_option( 'woocommerce_default_catalog_orderby' ) );
//if ( 'alphabetical' == $orderby_value ) {
//$sort_args['orderby'] = 'title';
//$sort_args['order'] = 'asc';
//$sort_args['meta_key'] = '';
//}
//return $sort_args;
//}
//add_filter( 'woocommerce_get_catalog_ordering_args', 'sip_alphabetical_shop_ordering' );
//
//function sip_custom_wc_catalog_orderby( $sortby ) {
//$sortby['alphabetical'] = 'Sort by Name: Alphabetical';
//return $sortby;
//}
//add_filter( 'woocommerce_default_catalog_orderby_options', 'sip_custom_wc_catalog_orderby' );
//add_filter( 'woocommerce_catalog_orderby', 'sip_custom_wc_catalog_orderby' );


function sip_custom_wc_catalog_orderby( $sortby ) {
$sortby['alphabetical'] = 'Sort by Store Name (A-Z)';
return $sortby;
}
add_filter( 'woocommerce_default_catalog_orderby_options', 'sip_custom_wc_catalog_orderby',999 );
add_filter( 'woocommerce_catalog_orderby', 'sip_custom_wc_catalog_orderby',999 );

//Adding Alphabetical sorting option to shop and product settings pages
function sip_alphabetical_shop_ordering( $sort_args ) {
$orderby_value = isset( $_GET['orderby'] ) ? woocommerce_clean( $_GET['orderby'] ) : apply_filters( 'woocommerce_default_catalog_orderby', get_option( 'woocommerce_default_catalog_orderby' ) );
if ( 'alphabetical' == $orderby_value ) {
$sort_args['orderby'] = 'title';
$sort_args['order'] = 'asc';
$sort_args['meta_key'] = '';
}
return $sort_args;
}
add_filter( 'woocommerce_get_catalog_ordering_args', 'sip_alphabetical_shop_ordering' );


function fev_custom_wc_catalog_orderby( $sortby ) {
$sortby['fev'] = 'Sort by Most Favorited';
return $sortby;
}
add_filter( 'woocommerce_default_catalog_orderby_options', 'fev_custom_wc_catalog_orderby',999 );
add_filter( 'woocommerce_catalog_orderby', 'fev_custom_wc_catalog_orderby',999 );

//Adding Alphabetical sorting option to shop and product settings pages
function custom_fev_shop_ordering( $sort_args ) {
$orderby_value = isset( $_GET['orderby'] ) ? woocommerce_clean( $_GET['orderby'] ) : apply_filters( 'woocommerce_default_catalog_orderby', get_option( 'woocommerce_default_catalog_orderby' ) );
if ( 'fev' == $orderby_value ) {
$sort_args['orderby'] = 'meta_value_num';
$sort_args['order'] = 'desc';
$sort_args['meta_key'] = '_post_like_count';
}
return $sort_args;
}
add_filter( 'woocommerce_get_catalog_ordering_args', 'custom_fev_shop_ordering' );

