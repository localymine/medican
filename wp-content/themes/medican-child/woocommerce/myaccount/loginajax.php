<?php
session_start();
$counter = 1;
$parse_uri = explode( 'wp-content', $_SERVER['SCRIPT_FILENAME'] );
require_once( $parse_uri[0] . 'wp-load.php' );
if(isset($_POST['loginphone']) || $_POST['captcha_code'])
{
  if(($_POST['loginphone']) && ($_POST['captcha_code']==''))
  {
        $loginphone = $_POST['loginphone'];
        $loginpass =  $_POST['loginpass'];
        $user_role =  $_POST['radioValue'];
        global $wpdb;
        $results = $wpdb->get_results('select  * from `wp_usermeta` where meta_key = "user_phone" and meta_value = "'.$loginphone.'"',ARRAY_A);
        $userid = $results[0]['user_id'];
        $custom_capability = get_user_meta( $userid, 'custom_capability', true );
         if( strpos( $custom_capability, $user_role ) !== false) {
               
        if($userid){
            wp_update_user( array( 'ID' => $userid, 'role' => $user_role ) );
            $passresults = $wpdb->get_results('select * from `wp_users` where  ID = "'.$userid.'"',ARRAY_A);  
            $username = $passresults[0]['user_login'];
            
            
            $user = wp_signon(array('user_login'=>$username,'user_password'=>$loginpass));
            $userdata = get_user_by('login', $username);
            if ($userdata && wp_check_password( $loginpass, $userdata->data->user_pass, $userdata->ID ) )
            {
              $error = 0;
              $message = 'User login';
              $_SESSION['counter_login']= 0;
            }
            else
            {
              $error = 1;
              $message = 'Enter wrong password.'; 
              if(!isset($_SESSION['counter_login']))
                   {
                     $_SESSION['counter_login'] = $counter; 
                   }
                   else
                   {
                     $_SESSION['counter_login'] = $_SESSION['counter_login']+1;   
                   }  
            }
            
        }
      }
        else {
             $message = 'User not exist';
             $error = 1;
              if(!isset($_SESSION['counter_login']))
                {
                  $_SESSION['counter_login'] = $counter; 
                }
                else
                {
                  $_SESSION['counter_login'] = $_SESSION['counter_login']+1;   
                }
        }
        
        
  } 
  else if(($_POST['loginphone']) && ($_POST['captcha_code']!=''))
  {
        $loginphone = $_POST['loginphone'];
        $loginpass = $_POST['loginpass'];
        $storecaptcha = $_POST['captcha_code'];
        $user_role =  $_POST['radioValue'];
        global $wpdb;
        $results = $wpdb->get_results('select  * from `wp_usermeta` where meta_key = "user_phone" and meta_value = "'.$loginphone.'"',ARRAY_A);
        $userid = $results[0]['user_id'];
        $custom_capability = get_user_meta( $userid, 'custom_capability', true );
      if( strpos( $custom_capability, $user_role ) !== false) {
        if($userid){
             wp_update_user( array( 'ID' => $userid, 'role' => $user_role ) );
             if(empty($_SESSION['captcha_code'] ) || strcasecmp($_SESSION['captcha_code'], $_POST['captcha_code']) != 0)
                {
               $error = 1;
               $message = 'Wrong captcha'; 
                }  
            else
            {
                $passresults = $wpdb->get_results('select * from `wp_users` where  ID = "'.$userid.'"',ARRAY_A);  
                $username = $passresults[0]['user_login'];
                $user = wp_signon(array('user_login'=>$username,'user_password'=>$loginpass));
                $userdata = get_user_by('login', $username);
                if ($userdata && wp_check_password( $loginpass, $userdata->data->user_pass, $userdata->ID ) )
                {
                  $error = 0;
                  $message = 'User login';
                  $_SESSION['counter_login']= 0;
                }
                else
                {
                  $error = 1;
                  $message = 'Enter wrong password.'; 
                  if(!isset($_SESSION['counter_login']))
                       {
                         $_SESSION['counter_login'] = $counter; 
                       }
                       else
                       {
                         $_SESSION['counter_login'] = $_SESSION['counter_login']+1;   
                       }  
                }   
            }
         }
      }
        else {
             $message = 'user not exist';
             $error = 1;
        }
      
      
  }
 $codearray = array("counter"=>$_SESSION['counter_login'],"message"=>$message,"error"=>$error);
 echo json_encode($codearray);   
}

?>

