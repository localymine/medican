<?php

ob_start();

/*
  Plugin Name:Custom  Frontend  Store Registration Form
  Plugin URI: http://dropsofvisions.com/
  Description: Simple Frontend store Login  Registration Form This plugin creates a user interface for adding custom registration forms to any post or page on your WordPress website. Simply navigate to the edit page for  post .login form Shortcode: [cflrf_custom_login_form] >Registration Form Shortcode: [cflrf_registration_form]
  

  Version: 1.01
  Author: Anil Shah
  Author URI: http://dropsofvisions.com/
 */
 class store_registration_form
{  // form properties
    private $username;
    private $email;
    private $password;
  
function __construct()
            {
                add_shortcode('store_registration_form', array($this, 'shortcode'));	
                add_action('wp_enqueue_scripts', array($this, 'cflrf_flat_ui_kit'));
              
            }
            
public function registration_form()
    {
    unset($_SESSION['counter_value']);

    ?>

<script defer  src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBS-ylPA0_B76BlvUiYZ71KWjad9j12ssI&libraries=places&callback=initStoreAutocomplete"> </script>
<script>
 window.onload = function() {
  initMap();
};
      function initMap() {
        var uluru = {lat: 14.0583, lng: 108.2772};
        var map = new google.maps.Map(document.getElementById('storemap'), {
          zoom: 13,
          center: uluru,
          scrollwheel: false,
          scaleControl: true,
        });
      
         var image = "<?php echo get_stylesheet_directory_uri().'/images/iconlocation1.png'; ?>";
         var marker = new google.maps.Marker({
          draggable:true,
          position: uluru,
          map: map,
          icon: image,
          
        });
       addYourLocationButton(map, marker);
      }
function addYourLocationButton(map, marker) 
{
	var controlDiv = document.createElement('div');
        controlDiv.setAttribute("id", "customlocation");
	var firstChild = document.createElement('button');
	firstChild.style.backgroundColor = '#fff';
	firstChild.style.border = 'none';
	firstChild.style.outline = 'none';
	firstChild.style.width = '28px';
	firstChild.style.height = '28px';
	firstChild.style.borderRadius = '2px';
	firstChild.style.boxShadow = '0 1px 4px rgba(0,0,0,0.3)';
	firstChild.style.cursor = 'pointer';
	firstChild.style.marginRight = '10px';
	firstChild.style.padding = '0px';
	firstChild.title = 'Your Location';
	controlDiv.appendChild(firstChild);
	var secondChild = document.createElement('div');
	secondChild.style.margin = '5px';
	secondChild.style.width = '18px';
	secondChild.style.height = '18px';
	secondChild.style.backgroundImage = 'url(https://maps.gstatic.com/tactile/mylocation/mylocation-sprite-1x.png)';
	secondChild.style.backgroundSize = '180px 18px';
	secondChild.style.backgroundPosition = '0px 0px';
	secondChild.style.backgroundRepeat = 'no-repeat';
	secondChild.id = 'you_location_img';
	firstChild.appendChild(secondChild);
	
	google.maps.event.addListener(map, 'dragend', function() {
		jQuery('#you_location_img').css('background-position', '0px 0px');
	});

	firstChild.addEventListener('click', function() {
		var imgX = '0';
		var animationInterval = setInterval(function(){
			if(imgX == '-18') imgX = '0';
			else imgX = '-18';
			jQuery('#you_location_img').css('background-position', imgX+'px 0px');
		}, 500);
		if(navigator.geolocation) {
			navigator.geolocation.getCurrentPosition(function(position) {
				var latlng = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
				marker.setPosition(latlng);
				map.setCenter(latlng);
				clearInterval(animationInterval);
				jQuery('#you_location_img').css('background-position', '-90px 0px');
			});
		}
		else{
			clearInterval(animationInterval);
			jQuery('#you_location_img').css('background-position', '0px 0px');
		}
	});
	
	controlDiv.index = 1;
	map.controls[google.maps.ControlPosition.RIGHT_BOTTOM].push(controlDiv);
}
</script>

<script type="text/javascript">
 function initStoreAutocomplete(address){
   
    var geocoder;
    var map;
    var address = address;
   console.log(address);
    geocoder = new google.maps.Geocoder();
    var latlng = new google.maps.LatLng(14.0583, 108.2772);
    var myOptions = {
    zoom: 15,
    scrollwheel: false,
    scaleControl: true,
    center: latlng,
    mapTypeControl: true,
    mapTypeControlOptions: {style: google.maps.MapTypeControlStyle.DROPDOWN_MENU},
    navigationControl: true,
    mapTypeId: google.maps.MapTypeId.ROADMAP
    };
    map = new google.maps.Map(document.getElementById("storemap"), myOptions);
    if (geocoder) {
      geocoder.geocode( { 'address': address}, function(results, status) {
        if (status == google.maps.GeocoderStatus.OK) {
          if (status != google.maps.GeocoderStatus.ZERO_RESULTS) {
              
       var latitude =  results[0].geometry.location.lat();
       var longitude = results[0].geometry.location.lng();   
       
       jQuery(".latitude").val(latitude);    
       jQuery(".longitude").val(longitude);  
  
          
          map.setCenter(results[0].geometry.location);
            var image = "<?php echo get_stylesheet_directory_uri().'/images/iconlocation1.png'; ?>";
            var marker = new google.maps.Marker({
                position: results[0].geometry.location,
                draggable:true,
                icon: image,
                map: map, 
                title:address
            }); 
             addYourLocationButton(map, marker);

          } else {

          }
        }
      });
    }  
}
</script>

<script type='text/javascript'>
   
function storeajax()
{
  jQuery("#preloader").css('display','block'); 
  jQuery("#status").css('display','block'); 
  var storname = jQuery('#storename').val();
  var sotorelicence = jQuery('#sotorelicence').val();
  var storeaddress = jQuery('#newstoreaddress').val();
  var storedomain = jQuery('#storedomain').val();
  var provineName = jQuery('#select_Provine').find(":selected").text();
  var districtName = jQuery('#select_district').find(":selected").text();
  var wardName = jQuery('#select_ward').find(":selected").text(); 
  var file_data = jQuery("#store_upload").prop("files")[0]; // Getting the properties of file from file field
  var galleryfiledata = jQuery("#StoreLicense").prop("files")[0]; 
  var latitude = jQuery('.latitude').val();
  var longitude = jQuery('.longitude').val();
   var form_data = new FormData(); // Creating object of FormData class
   form_data.append("file", file_data)
   form_data.append("gallery", galleryfiledata)
   form_data.append("type", 'register')
   form_data.append("storname", storname)
   form_data.append("sotorelicence", sotorelicence)
   form_data.append("storeaddress", storeaddress)
   form_data.append("provineName", provineName)
   form_data.append("districtName", districtName)
   form_data.append("wardName", wardName)
   form_data.append("storedomain", storedomain)
   form_data.append("latitude", latitude)
   form_data.append("longitude", longitude)
   
   
   

  
 jQuery.ajax({
        type: 'POST',               
        processData: false, // important
        contentType: false, // important
        data: form_data,
       
        url: '<?php echo plugins_url().'/Custom-frontend-store-registration-form/storeajax.php' ?>',
        dataType : 'json',
             success: function(jsonData){
              jQuery("#preloader").css('display','none');
              jQuery("#status").css('display','none'); 
              if(jsonData['error'] && jsonData['error']=='1')
                    {
                        if(jsonData['domainmessage']=='domain')
                        {
                         jQuery(".domain_error").html('Domain already exist.');   
                        }
                        if(jsonData['storemessage']=='storeimage')
                        {
                         jQuery(".orderimageerror").html('Upload Photo File only.');     
                        }
                        if(jsonData['licancemessage']=='licanceimage')
                        {
                         jQuery(".orderimageerrornext").html('Upload Photo File only.');     
                        }
//                     jQuery(".domain_error").html(jsonData['message']);
                    }
                else
                    {
                        jQuery(window).scrollTop(0);
                        jQuery(".domain_error").html('');
                        jQuery(".tab-switcher").removeClass("active"); 
                        jQuery('.tab2').addClass('active');
                        jQuery(".tab-container").hide();
                        jQuery('.left_tab').show();
                        jQuery(".successfull").css("display","block"); 
                        jQuery(".total_content").css("display","none"); 
                        document.getElementById("regitserform").reset();
                        jQuery(".orderimageerrornext").html('');
                        jQuery(".orderimageerror").html('');
                    }
                 
              },
          
       
   });

}  
   
 jQuery(document).ready(function () {
 jQuery.validator.addMethod("chekcdomain", function(value, element) {
  // allow any non-whitespace characters as the host part
  return this.optional( element ) || /^[a-zA-Z0-9- ]*$/.test( value );
}, 'Special Characters not Allowed.');

 jQuery.validator.addMethod("noSpace", function(value, element) { 
  return value.indexOf(" ") < 0 ; 
}, "Blank Space not Allowed.");


   jQuery('.storeregitserform').validate({ // initialize the plugin
//                    onfocusout: function(element) {
//                        this.element(element);
//                    },
//                    errorLabelContainer: "#message_box", wrapper: "li",
                    onkeyup: function(element) {jQuery(element).valid()},
                      rules: {
                            storename: {
                                required: true,
                                maxlength: 100
                            },
//                            sotorelicence: {
//                              required: true,
//                              maxlength: 100
//                            },
                            newstoreaddress: {
                             required: true, 
                             maxlength: 100
                            },
                            storeupload: {
                            required: true,
                            extension: "jpg|jpeg|png|gif",
                              },
                           StoreLicense: {
                            required: true, 
                            extension: "jpg|jpeg|png|gif",
                              },     
                           select_Provine: {
                             required: true 
                                    },
                           select_district: {
                             required: true 
                                    },
                           select_ward: {
                             required: true 
                                    },
                           storedomain: {
                            required: true,
                            maxlength: 100,
                            chekcdomain:true,
                            noSpace: true,
                                    },
                           },
                          messages: {
                                  storeupload:{
                                    extension: ""
                                },  
                                StoreLicense:{
                                    extension: ""
                                },  

                             },    
                                    
                        submitHandler: function (form) { // for demo
                           jQuery(".domain_error").html('');
                           storeajax('register');
                           
                        }
                    });
       
  
 });
     
</script>

<!--script for the signup-->
<script type='text/javascript'>
   
jQuery(document).ready(function () {
     
//   var x_timer;    
//   jQuery("#storedomain").keyup(function (e){
//       clearTimeout(x_timer);
//       var domain_name = jQuery(this).val();
//       x_timer = setTimeout(function(){
//           domainval(domain_name);
//       }, 1000);
//   });
//function  domainval(abc)
//  {
//     jQuery.ajax({
//        type: 'POST',               
//        data: {type:'domain',value:abc},
//        url: '<?php // echo plugins_url().'/Custom-frontend-store-registration-form/storeajax.php' ?>',
//        dataType : 'json',
//             success: function(result){
//              if(result['error'] && result['error']=='1')
//              {
//                  jQuery('.domain_error').html(result['message']);
//              }
//            else
//            {
//                jQuery('.domain_error').html('');
//            }
//    },
//   });
//  }     
     
     
     
  jQuery('#select_Provine').on('change',function(){
  jQuery( "#select_district" ).prop( "disabled", true).css("background","#eee");
  jQuery( "#select_ward" ).prop( "disabled", true ).css("background","#eee");
   
      var provineName = jQuery('#select_Provine').find(":selected").text();
      var finalprovince = provineName+',Vietnam'; 
      initStoreAutocomplete(finalprovince);
      var Province = jQuery(this).val();
        if(Province){
            jQuery.ajax({
                type:'POST',
                url:'<?php echo plugins_url().'/Custom-frontend-store-registration-form/storeaddress.php' ?>',
                data:'Province='+Province,
                success:function(html){
//                    initialize();
                    jQuery( "#select_district" ).prop( "disabled", false).css("background","white");
                    jQuery('#select_district').html(html);
                    /* jQuery('#select_ward').html('<option value="">Select District first</option>');  */
                    jQuery('.province_select').html('');
                    jQuery('.district_select').html('');
                }
            }); 
        }else{
            jQuery('#select_district').html('<option value="">District*</option>');
            jQuery('#select_ward').html('<option value="">Ward*</option>');
            /* jQuery('.province_select').html('Please select Province first'); */
            /* jQuery('.district_select').html('Please select District first'); */
            
            
        }
    });
      jQuery('#select_district').on('change',function(){
       jQuery( "#select_ward" ).prop( "disabled", true ).css("background","#eee");
        var provineName = jQuery('#select_Provine').find(":selected").text();
        var districtName = jQuery('#select_district').find(":selected").text();
        var finaldist = provineName+','+districtName+',Vietnam'; 
        initStoreAutocomplete(finaldist);
        var district_id = jQuery(this).val();
        if(district_id){
            jQuery.ajax({
                type:'POST',
                url:'<?php echo plugins_url().'/Custom-frontend-store-registration-form/storeaddress.php' ?>',
                data:'district_id='+district_id,
                success:function(html){
//                    initialize(); 
                    jQuery( "#select_ward" ).prop( "disabled", false ).css("background","white");
                    jQuery('#select_ward').html(html);
                    jQuery('.district_select').html('');
                }
            }); 
        }else{
            jQuery('#select_ward').html('<option value="">Ward*</option>');
            /* jQuery('.district_select').html('Please select District first'); */
        }
    });
     jQuery('#select_ward').on('change',function(){
     var provineName = jQuery('#select_Provine').find(":selected").text();
     var districtName = jQuery('#select_district').find(":selected").text();
     var wardName = jQuery('#select_ward').find(":selected").text();
     var finalward = provineName+','+districtName+','+wardName+',Vietnam'; 
    
     initStoreAutocomplete(finalward);
   });
        
     
     
     
 jQuery('.storecustomclass').on('input', function() {
     jQuery(".storeerror").html('');
     jQuery(".domain_error").html('');   
  });
        
        
//    jQuery('#store_upload').click(function()
//        {
//        jQuery('[for="store_upload"]').remove();
//
//        });   
//    jQuery('#StoreLicense').click(function()
//        {
//         jQuery('[for="StoreLicense"]').remove();
//
//        }); 
    
 jQuery("#regitserform")[0].reset();
});


  function readURL(input) {
  jQuery("label[for^='store_upload']").remove();
   var ext = jQuery(input).val().split('.').pop().toLowerCase();
            if(jQuery.inArray(ext, ['gif','png','jpg','jpeg']) == -1) {
            jQuery('.orderimageerror').html('Upload Photo File only.'); 
            }
            else
            {
            jQuery('.orderimageerror').html(''); 
            }
        if (input.files && input.files[0]) {
         var reader = new FileReader();

         reader.onload = function (e) {
             jQuery('#storeuploadimg').css('display','block');
             jQuery('#storeuploadimg')
                 .attr('src', e.target.result)
                 .width(40)
                 .height(40);
        
         };

         reader.readAsDataURL(input.files[0]);
     }
     
 } 

  function readURLnext(input) {
    jQuery("label[for^='StoreLicense']").remove();
        var ext = jQuery(input).val().split('.').pop().toLowerCase();
            if(jQuery.inArray(ext, ['gif','png','jpg','jpeg']) == -1) {
            jQuery('.orderimageerrornext').html('Upload Photo File only.'); 
            }
            else
            {
            jQuery('.orderimageerrornext').html(''); 
            }
         
      if (input.files && input.files[0]) {
         var reader = new FileReader();

         reader.onload = function (e) {
             jQuery('#StoreLicenseimg').css('display','block');
             jQuery('#StoreLicenseimg')
                 .attr('src', e.target.result)
                 .width(40)
                 .height(40);
        
         };

         reader.readAsDataURL(input.files[0]);
     }
     
     
 }



</script>

 <div class='successfull' style='display: none'>
            <div class='top top_formcontent'>
            <div class='top_bottom'>
                <img src='<?php echo get_stylesheet_directory_uri().'/images/icontick.png'?>' class='successimage'>
                <h2>Successful</h2>
             </div>
             <div class='bottom'>Thanks for signing up</div>
             <a href="<?php echo get_home_url(); ?>" class='asuccess registerlink'>Back to Homepage</a>
            </div>
        </div>

 <div class='total_content'>
     
    <div class="top_formcontent" >
 <!--first step-->
         <div class="right_tab tab-container" data-tab-index="0">
             <h3>Sign up as a Store account </h3>
        <div class="login-form">  
         <form  class='storeregitserform'id='regitserform' method="post" action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>">
             <input type='hidden' name='latitude' class='latitude'>
             <input type='hidden' name='longitude' class='longitude'>
                 <div class="form-group">
                     <lable>Store Name*</lable>
                     <input placeholder="Store Name*" name="storename" type="text" class="form-control login-field" value="" id="storename">
                </div>
<!--                <div class="form-group">
                     <lable>Store License*</lable>
                    <input placeholder="Store License*" name="sotorelicence" type="text" class="form-control login-field" value="<?php // if(isset($_POST['reg_email'])){ echo $_POST['reg_email'];} ?>"  id="sotorelicence">
                </div>-->

               <div class="form-group">
                    <lable>Store Address*</lable>
                    <input placeholder="Store Address*" name="newstoreaddress" type="text" class="form-control login-field" value="" id="newstoreaddress">
                   
                </div>
                <div class="select_top"> 
                        <div class="form-group">
                            <select name='select_Provine' id='select_Provine'>
                                <option value="" class="Provine">Province*</option>
                                <?php 
                                  $workingdir = getcwd().'/dbconn.php';
                                  include($workingdir);
                                 $query = $conn->query("SELECT DISTINCT Province, Code1 FROM wp_store_address ORDER BY Province ASC");
                                 $rowCount = $query->num_rows;
                                if($rowCount > 0){
                                       while($row = $query->fetch_assoc()){ 
                                           echo '<option value="'.$row['Code1'].'">'.$row['Province'].'</option>';
                                       }
                                   }
                                ?>
                            </select> 
                        </div>
                         <div class="form-group">    
                             <select name='select_district' id='select_district'>
                              <option class="Provine" value="">District*</option>
                             </select> 
                             <div class="province_select"></div>
                         </div>
                         <div class="form-group storeward"> 
                            <select name='select_ward' id='select_ward' class='ward'>
                             <option class="Provine" value="">Ward*</option>
                            </select> 
                             <div class="district_select"></div>
                        </div>
            </div>
                <div class="form-group StoreLocation">
                   <lable>Store Location</lable>
                   <span class="note">Notes</span>
                   <div id="storemap_outer"><div id="storemap"></div></div> 
                </div>
             
             
               <div class="form-group StorePhoto">
                    <img id="storeuploadimg" src="#" style="display:none"/>
                    <lable class="photo">Store Photo*</lable>
                    <input id="store_upload" type="file" name='storeupload' onchange='readURL(this);' > 
                    
                    <!--<img id="storeuploadimg" class="storeuploadimg">-->
                    <div class='orderimageerror storecreateerror'></div> 
               </div>
             
              <div class="form-group StoreLicense">
                     <img id="StoreLicenseimg" src="#" style="display:none"/>
                    <lable class="photo">Store License*</lable>
                    <input id="StoreLicense" type="file" name='StoreLicense' onchange='readURLnext(this);'> 
                    
                    <!--<img id="StoreLicenseimg" class="StoreLicenseimg storeuploadimg">-->
                    <div class='orderimageerrornext storecreateerror'></div> 
               </div>
            
             <div class="form-group">
                     <lable>Domain*</lable>
                     <span class="note">Notes</span>
                      <div class="form-group">
                           <span class='storedomainspan'>http://webnhathuoc.vn/</span> <input name="storedomain" type="text" class="form-control login-field storecustomclass"
                           value=""
                           placeholder="store-domain*" id="storedomain">
                          
                      </div> 
              </div>
             
            <input class="btn btn-primary registerlink" type="submit" name="reg_submit" value="Sign Up"/>
			 <span class="domain_error"></span>
            <span class="domain_erorr"></span>
            <span class="storeerrorfile"></span>
          </form>
        </div>
        </div>
 </div>
</div>
        
    <?php
    }

	function registration()
            {
           if (is_wp_error($this->validation())) {
                    echo '<div style="margin-bottom: 6px" class="btn btn-block btn-lg btn-danger">';
                    echo '<strong>' . $this->validation()->get_error_message() . '</strong>';
                    echo '</div>';
                } else {?>
                     <?php
                  }

            }
function shortcode()
    {
 
        ob_start();
         if ($_REQUEST['reg_submit']) {
            $this->username = $_REQUEST['reg_name'];
            $this->email = $_REQUEST['reg_email'];
            $this->password = $_REQUEST['reg_password'];
     
 
            $this->validation();
           
                session_start();
                
                $_SESSION['start'] = time();
                
                if(!isset($_SESSION['code_value'])){
                    $_SESSION['code_value'] = $_SESSION['start'] + (30) ; 
                }
                $now = time(); 
                if($now > $_SESSION['code_value'])
                {
                    unset($_SESSION['code_value']);

                }
           $this->registration();
       }
         $this->registration_form();   
         return ob_get_clean();
    }
	function cflrf_flat_ui_kit()
        {

            wp_enqueue_style('flat-ui-kit', plugins_url('css/flat-ui.css', __FILE__), false, false, 'screen');

        }
}
new store_registration_form;

