<?php

ob_start();

/*
  Plugin Name:Custom frontend rightsidebar
  Plugin URI: http://dropsofvisions.com/
  Description: Simple Frontend store Login  Registration Form This plugin creates a user interface for adding custom registration forms to any post or page on your WordPress website. Simply navigate to the edit page for  post .login form Shortcode: [cflrf_custom_login_form] >Registration Form Shortcode: [cflrf_registration_form]
  

  Version: 1.01
  Author: Anil Shah
  Author URI: http://dropsofvisions.com/
 */
 class Customfrontendrightsidebar
{  // form properties

function __construct()
            {
                add_shortcode('Customfrontendrightsidebar', array($this, 'shortcode'));	
                
            }
            
public function registration_form()
{?>
<!--<script src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js"></script>-->
<script type="text/javascript">
   jQuery(document).ready(function(){  
       
         jQuery('#searchregitserform').validate({ // initialize the plugin
                       rules: {
                       
                           searchstorename: {
                               required: true,
                           },
                           regsearchstoreaddress: {
                             required: true,
                           },
                          
                          },
                       submitHandler: function (form) { // for demo
                        
                       }
                   });
       
   });
  </script>



<div class="bottom_sidebar">
        <div class="bottom_sidebar_inner">
            
            <h3>Recommended Stores</h3>
            <p>Paragraph Paragraph Paragraph Paragraph Paragraph.</p>
            <form id="searchregitserform" action="" method="post">
                <div class="form-group">
                  <input id="search_store_name" maxlength="100" class="form-control login-field" type="text" placeholder="Store Name*" value="" name="searchstorename">
               </div>
                <div class="form-group">
                    <textarea id="regsearch_store_address"  maxlength="500" class="form-control login-field" name="regsearchstoreaddress" placeholder="Address*" rows="4" cols="50"></textarea>
                
               </div>
                <input type="submit" value="Send" class="sidebarsubmit" >
            </form>
        </div>
    </div>




<?php }
function shortcode()
    {
        ob_start();
        $this->registration_form();
        return ob_get_clean();
    }

}
new Customfrontendrightsidebar;

   



  