<?php
$parse_uri = explode( 'wp-content', $_SERVER['SCRIPT_FILENAME'] );
require_once( $parse_uri[0] . 'wp-load.php' );

$myadd = $_SERVER['DOCUMENT_ROOT'].'/wp-content/plugins/Custom-frontend-store-registration-form/ak_php_img_lib_1.0.php';
require_once($myadd);
if(isset($_POST['type']) && ($_POST['type']=='register'))
{//Register
  
    $storname =  $_POST['storname'];
    $sotorelicence =  $_POST['sotorelicence'];
    $storeaddress =  $_POST['storeaddress'];
    $provineName = $_POST['provineName'];
    $districtName =  $_POST['districtName'];
    $wardName = $_POST['wardName'];
    $storedomain = $_POST['storedomain'];
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];
    global $wpdb;
    $results = $wpdb->get_results('select * from `wp_usermeta` where meta_key = "storedomain" and meta_value = "'.$storedomain.'"');
    if($results)
      {
        $domainmessage = 'domain';  
        $error = 1;
        $customerror = 1;
      }
   
    $pic = $_FILES['file']['name'];
    $pic_loc = $_FILES['file']['tmp_name'];
    $folder  = $_SERVER['DOCUMENT_ROOT'].'/wp-content/plugins/Custom-frontend-store-registration-form/upload/';
    $timestamp = time(); 
    $path_parts = pathinfo($_FILES["file"]["name"]);
    $image_path = $path_parts['filename'].'_'.time().'.'.$path_parts['extension'];
    $check = getimagesize($_FILES['file']['tmp_name']);
    $gallerypath_parts = pathinfo($_FILES["gallery"]["name"]);
    $galleryimage_path = $gallerypath_parts['filename'].'_'.time().'.'.$gallerypath_parts['extension'];
    $gallerycheck = getimagesize($_FILES['gallery']['tmp_name']);
    $gallerypic_loc = $_FILES['gallery']['tmp_name'];   
    $fileExt = $path_parts['extension'];
    $gfileEx = $gallerypath_parts['extension'];

    
//   product main image
       if($customerror!=1)
        {
        $upload_dir       = wp_upload_dir();
        $unique_file_name = wp_unique_filename( $upload_dir['path'], $image_path ); // Generate unique name
        $filename         = basename( $unique_file_name ); // Create image file name

       if( wp_mkdir_p( $upload_dir['path'] ) ){
           $file = $upload_dir['path'] . '/resized_' . $filename;
       }else{
           $file = $upload_dir['basedir'] . '/resized_'.$filename;
            }
        move_uploaded_file($pic_loc,$folder.$filename);  
        
        if($_FILES["file"]["size"] > 150000)
        {
            $target_file = $folder.$filename;
            $resized_file = $file;
            $wmax = 400;
            $hmax = 400;  
            ak_img_resize($target_file, $resized_file, $wmax, $hmax, $fileExt);
        }
        else {
            copy($folder.$filename, $file);
        }
        
//    gallery images
        $galleryunique_file_name = wp_unique_filename( $upload_dir['path'], $galleryimage_path ); // Generate unique name
        $galleryfilename         = basename( $galleryunique_file_name ); // Create image file name

       if( wp_mkdir_p( $upload_dir['path'] ) ){
           $gfile = $upload_dir['path'] . '/resized_' . $galleryfilename;
       }else{
           $gfile = $upload_dir['basedir'] . '/resized_' . $galleryfilename;
            }
        move_uploaded_file($gallerypic_loc,$folder.$galleryfilename);    
        
        if($_FILES["gallery"]["size"] > 150000)
        {
           $gtarget_file = $folder.$galleryfilename;
           $gresized_file = $gfile;
           $gwmax = 400;
           $ghmax = 400;  
           ak_img_resize($gtarget_file, $gresized_file, $gwmax, $ghmax, $gfileEx);
        }
        else {
            copy($folder.$galleryfilename, $gfile);
        }
       $currentuerid = get_current_user_id();
       $phoneno = get_user_meta($currentuerid, 'user_phone' , true );
       $customcap = 'customer,vendor';
        update_usermeta($currentuerid,'storename',$storname);
        update_usermeta($currentuerid,'sotorelicence',$sotorelicence);
        update_usermeta($currentuerid,'storeaddress',$storeaddress);
        update_usermeta($currentuerid,'provineName',$provineName);
        update_usermeta($currentuerid,'districtName',$districtName);
        update_usermeta($currentuerid,'wardName',$wardName);
        update_usermeta($currentuerid,'storedomain',$storedomain);
        update_usermeta($currentuerid,'storephoto',$photoname);
        update_usermeta($currentuerid,'custom_capability',$customcap);
        $completeaddress = $wardName.', '.$districtName.', '.$provineName;
//          Creating the woocommerce product
                $post_id = wp_insert_post( array(
                       'post_title' => $storname,
                       'post_status' => 'publish',
                       'post_type' => 'product',
                       'post_author' => $currentuerid,
                       'post_content' => '',
                   ) );
                   wp_set_object_terms($post_id, 'simple', 'product_type'); 
                   wp_set_object_terms($post_id, 44, 'product_cat');
                   update_post_meta( $post_id, '_visibility', 'visible' );
                   update_post_meta( $post_id, '_stock_status', 'instock');
                   add_post_meta( $post_id, 'store_address', $storeaddress, true ); 
                   add_post_meta( $post_id, 'store_domain',  $storedomain, true ); 
                   add_post_meta( $post_id, 'provineName', $provineName, true ); 
                   add_post_meta( $post_id, 'districtName', $districtName, true ); 
                   add_post_meta( $post_id, 'wardName', $wardName, true ); 
                   add_post_meta( $post_id, 'wardName', $wardName, true ); 
                   add_post_meta( $post_id, 'longitude', $longitude, true ); 
                   add_post_meta( $post_id, 'latitude', $latitude, true ); 
                   add_post_meta( $post_id, 'completeaddress', $completeaddress, true ); 
                   add_post_meta( $post_id, 'phone', $phoneno, true ); 
                   add_post_meta( $post_id, '_post_like_count', '0', true );
                    wp_update_post( array (
                         'ID'        => $post_id,
                         'post_name' => $storedomain,
                                )
                            );
                   
                   
//  insert product main image                
                   $wp_filetype = wp_check_filetype( $filename, null );
                   $attachment = array(
                       'post_mime_type' => $wp_filetype['type'],
                       'post_title'     => sanitize_file_name( $filename ),
                       'post_content'   => '',
                       'post_status'    => 'inherit'
                       );
                       $attach_id = wp_insert_attachment( $attachment, $file, $post_id );
                       require_once(ABSPATH . 'wp-admin/includes/image.php');
       
                       $attach_data = wp_generate_attachment_metadata( $attach_id, $file );
                       
                       wp_update_attachment_metadata( $attach_id, $attach_data );
                       set_post_thumbnail( $post_id, $attach_id );
                       
// inser produt gallery images      
                       $gallerywp_filetype = wp_check_filetype( $galleryfilename, null );
                       $attachments = array(
                       'post_mime_type' => $gallerywp_filetype['type'],
                       'post_title'     => sanitize_file_name( $galleryfilename ),
                       'post_content'   => '',
                       'post_status'    => 'inherit'
                       );
                       $gattach_id = wp_insert_attachment( $attachments, $gfile, $post_id );
                       require_once(ABSPATH . 'wp-admin/includes/image.php');
                       update_post_meta($post_id,'_product_image_gallery',$gattach_id);
                    
                   $message ='User Successfully Register';
                   $error = 0;

          }
 $errorarray = array("error"=>$error, "domainmessage"=> $domainmessage, "storemessage"=>$storemessage, 'licancemessage'=>$licancemessage, "type"=>'signup',"filename"=>$filename,"uploadfolder"=>$file);
 echo json_encode($errorarray);
}


?>