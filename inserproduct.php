<?php 
require(dirname(__FILE__) . '/wp-load.php');
$file_handle = fopen('newdomain.csv', 'r');
$data = array();
$data  = fgetcsv($file_handle);
$i=1;
while(! feof($file_handle))
{
$data = array();
$data  = fgetcsv($file_handle);
$news[] = $data;
//echo $i.'<pre>'; print_r($data);
//die('okk');
$storename = $data[0];
$stordomain= $data[1];
$storeaddress = $data[2];
$storeWard = $data[3];
$storeDistrict= $data[4];
$storeProvince = $data[5];
$storelongitude= $data[6];
$myloc = explode('&',$storelongitude);
$first  = $myloc[0];
$seconf = $myloc[1];
$lat = explode('=',$first); 
$lon = explode('=',$seconf);
$finallatitude =  $lat[1];
$finallongotude = $lon[1];
$post_id = wp_insert_post( array(
'post_title' => $storename,
'post_status' => 'publish',
'post_type' => 'product',
'post_author' => '',
'post_content' => $storename,
) );
wp_set_object_terms( $post_id, 'simple', 'product_type' ); 
update_post_meta( $post_id, '_visibility', 'visible' );
update_post_meta( $post_id, '_stock_status', 'instock');
add_post_meta( $post_id, 'store_address', $storeaddress, true ); 
add_post_meta( $post_id, 'store_domain',  $stordomain, true ); 
add_post_meta( $post_id, 'Website', $stordomain, true ); 
add_post_meta( $post_id, 'provineName', $storeProvince, true ); 
add_post_meta( $post_id, 'districtName', $storeDistrict, true ); 
add_post_meta( $post_id, 'wardName', $storeWard, true ); 
add_post_meta( $post_id, 'latitude', $finallatitude, true ); 
add_post_meta( $post_id, 'longitude', $finallongotude, true ); 
//
//$filename = 'wp-content/uploads/2017/02/iconloading.png';
//
//$filetype = wp_check_filetype( basename( $filename ), null );
////echo $wp_upload_dir['url'];
////die('s');
//// Get the path to the upload directory.
//$wp_upload_dir = wp_upload_dir();
////$bac  =  $wp_upload_dir['url'].'/' . basename($filename );
//
////echo $wp_upload_dir['url'];
////echo basename( $filename );
////die('oioii');
//// Prepare an array of post data for the attachment.
////echo  $wp_upload_dir['url'];
////die('okk');
//
//$attachment = array(
//	'guid'           => $wp_upload_dir['url'].'/' . basename($filename ), 
//	'post_mime_type' => $filetype['type'],
//	'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $filename ) ),
//	'post_content'   => '',
//	'post_status'    => 'inherit'
//);
//
//// Insert the attachment.
//$attach_id = wp_insert_attachment( $attachment, $filename, $post_id );
//set_post_thumbnail( $post_id, $attach_id );
//// Make sure that this file is included, as wp_generate_attachment_metadata() depends on it.
//require_once( 'wp-admin/includes/image.php' );
//// Generate the metadata for the attachment, and update the database record.
//$attach_data = wp_generate_attachment_metadata( $attach_id, $filename );
//wp_update_attachment_metadata( $attach_id, $attach_data );
$i++; 
echo $i;

}  

?>