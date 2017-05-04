<?php
/**
 * The Template for displaying all single products.
 *
 * Override this template by copying it to yourtheme/woocommerce/single-product.php
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     10.0.0
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
$customselectjs =  get_stylesheet_directory_uri().'/src/jquery-customselect.js';
$customselectcss =  get_stylesheet_directory_uri().'/src/jquery-customselect.css';
$copycustomselectjs =  get_stylesheet_directory_uri().'/src/jquery-copycustomselect.js';
$copycustomselectcss =  get_stylesheet_directory_uri().'/src/jquery-copycustomselect.css';
$options = get_option(AZEXO_FRAMEWORK);
if (!isset($show_sidebar)) {
    $show_sidebar = isset($options[get_post_type() . '_show_sidebar']) ? $options[get_post_type() . '_show_sidebar'] : 'right';
    if ($show_sidebar == 'hidden') {
        $show_sidebar = false;
    }
}
$additional_sidebar = isset($options[$post_type . '_additional_sidebar']) ? (array) $options[$post_type . '_additional_sidebar'] : array();
get_header('shop');

?>

<div class="order_history" style='display: none'>
<div class="order_innerhistory">
        <div class="successorderlist">
            <div class="top_bottom">
                <img class="successimage" src="<?php echo get_stylesheet_directory_uri().'/images/icontick.png' ?>">
                <h2>Successful</h2>
            </div>
            <div class="bottommtext">
        <?php  $productid =  get_the_ID();
        $product_phone = get_post_meta( $productid, 'phone', true ); ?>
                <ul>
                    <li>
                       Paragraph Paragraph Paragraph Paragraph:<b class='userorder_id'>  </b>
                    </li>
                    <li>
                        Paragraph Paragraph Paragraph Paragraph:<b class="useremail_id"></b>  
                    </li>
                    <li>
                        -Paragraph Paragraph Paragraph Paragraph:<b><?php echo $product_phone;?></b>  
                    </li>
                    <li>
                        -Paragraph Paragraph Paragraph Paragraph.
                    </li>
                    <li class="storeback">
                        <div class='leftstore'>
							<a href="">
								<div class='commonimage'>
									<i class="fa fa-long-arrow-left" aria-hidden="true"></i>
								</div>
							</a>
							<a class="storepage common" href="" >Back to Store Page</a>
						</div>
						<div class='rightstore'>
							<a class="dashboardpage common" href="">Go to Dashboard Page</a>
							<a href="">
								<div class='commonimage'>
									<i class="fa fa-long-arrow-right" aria-hidden="true"></i>
								</div>
							</a>
						</div>
                    </li> 
                </ul>
        </div> 
        </div>
        </div>
 </div>
<div class='complete_div'>
    <div class="search_top_outer productpagetop">
        <div class="search_top_inner container ">
        <div class="bottom_sidebar_inner">
        <?php 
        $productid =  get_the_ID();
        $producttitle =  get_the_title();
        $product_domain = get_post_meta( $productid, 'store_domain', true );

        ?>
        <div class="name"> <h1><?php echo $producttitle; ?></h1></div>
        <div class="domain"> <?php echo 'www.webnhathuoc.vn/'.$product_domain; ?> </div>
        </div>
        </div>
    </div>
<div class="<?php print ((isset($options['content_fullwidth']) && $options['content_fullwidth']) ? '' : 'container'); ?> <?php print (is_active_sidebar('shop') && $show_sidebar ? 'active-sidebar ' . esc_attr($show_sidebar) : ''); ?> <?php print (in_array('single', $additional_sidebar) ? 'additional-sidebar' : ''); ?>">
   


 <div class="outerproduct">
 <?php
  get_sidebar('additional');
?>
<script src="<?php echo $customselectjs; ?>"></script>
<script src="<?php echo $copycustomselectjs; ?>"></script>
<link rel='stylesheet'  href='<?php echo $customselectcss; ?>?' type='text/css' media='all' />
<link rel='stylesheet'  href='<?php echo $copycustomselectcss; ?>?' type='text/css' media='all' />
<div id="primary" class="content-area">
<div class="innerprimary">
         <div class='tab_heading'>
            <ul>
                <li  class="tab-switcher active tab1" data-tab-index="0">Upload an Order</li>
                <li  class="tab-switcher tab2" data-tab-index="1">Input an Order</li>
            </ul>
            
         </div> 
<div class="productleftbar">
    <form  id='orderupload' method="post" action="">

        <label for="order_photo" class='imagelabel hvr-icon-pulse'>
           Upload*
        </label>

    <input class="commonorder" id="order_photo" type="file" name='orderphoto' onchange='readURL(this);' style="display:none;"> 
    <img id="blah" src="#" style="display:none"/>
     <div class='orderimageerror'></div> 
    <div class="copy_html">
    <div class='bottomli commonorder'>
        <div class='newselectlist form_row_0'>
            <div class="inner_proclass">
                <?php 
                               $args = array(
                                            'posts_per_page' => -1,
                                            'product_cat' => 'uploadorder',
                                            'post_type' => 'product',
                                            'orderby' => 'title',
                                            'order' => 'asc',
                                    );


                            $query = new WP_Query( $args );

                               ?>
                
            <select name='productid[]' class='custom-select proname proname_0' id='proid_0'>
               <option value=''>Chose your product on uploaded photo*</option>
               
                <?php while ( $query->have_posts() ) {
                                            $query->the_post();
            ?>
            <option value='<?php echo get_the_id(); ?>'><?php echo get_the_title(); ?></option>
                                    <?php  }  wp_reset_postdata();  ?>
                 
            </select>
            </div>
            <div class='toplist'>
                <select name='productqty[]' class="selectqty proqty proqty_0 copy-custom-select" id='proqty_0'>

                                              <option value="" class="">Q.ty*</option>
                                              <?php  for($qty=1;$qty<=100;$qty++){?>
                                              <option value='<?php echo $qty ?>'><?php echo $qty; ?></option>
                                              <?php } ?>
                 </select>
                <div class='selectlistcss'></div>
            </div>

            <div class='toplist unittoplist'>
                <select name='productunit[]' class='productunit prounit prounit_0 copy-custom-select' id='prounit_0'>
                    <option value="" class="">Unit*</option>
                                            <option value="Piece" class="">Piece</option>
                                            <option value="Box" class="">Box</option>
                                            <option value="Botttle" class="">Botttle</option>

               </select>
            </div>
         <input class="firstminus minusbutton remove_field" type="button" value="-" style="visibility:hidden">      
    </div>
    </div>
    </div>
         <div class="insert_order">
           <input type="button"  value="+" class="plusbutton minusbutton" id="plusbutton">
         </div>

 <div class="bottomdelivery_outer">

        <div class="bottomdelivery_inner ">
        <div class='bottomradio commondata'>
        <span>Permission</span>
        <div class="permisstion">
             <input  checked id="orderradiosecond" class="orderradiosecond orderradiofirst" type="radio" name="permisstion" value="Do not do alternative"><span class="orderradio"><u class="text-click">Do not do alternative</u></span><br>
                                                                        <input id="orderradiofirst" class="orderradiofirst" type="radio" name="permisstion" value="Do alternative(Blue)" > <span class="orderradio"><u class="text-click">Do alternative </u><u class="text-hover"><a href="<?php echo get_site_url();?>/customfaq/#category3" target="_blank">(Blue)</a></u></span><br>
                                                                       
                                                                   </div>
    </div>
        <div class='delivery_time commondata'>
            <span>Delivery Time:</span> 
            <div class='deliverydeatails'></div>
            <div class='imagecalender'><input type='hidden' id='imagecalenderinput'></div>
            <input type='hidden' class='deliverydeatails' name='deliverydeatails' id='deliverydeatails'>
            <div class="customdatepicker"><input type="hidden" id="datepicker" name='datepicker'></div>
        </div>
        <div class='delivery_time commondata storedelivery'>
            <span>Delivery at:</span>  
            <div class="delivery"><?php global $product;
            echo get_the_title(); ?><input type="hidden" name="myproduct" value="<?php echo  get_the_title(); ?>">
			<input type="hidden" name="myproductID" value="<?php echo  get_the_ID(); ?>">
			</div>
        </div>
        <div class="ordersdata">
           <p>paragraph1 paragraph1 paragraph1 paragraph1 paragraph1 paragraph1 paragraph1 paragraph1 paragraph1 paragraph1 paragraph1 paragraph1 </p>
        </div> 
            <textarea name="ordercomment" maxlength="500" class="form-control" rows="5" id="ordercomment" placeholder="Notes"></textarea> 
        
                
                
        <?php 
      if ( is_user_logged_in() ) {
            $userid = get_current_user_id();
            $caps = get_user_meta($userid, 'wp_capabilities', true);
            $roles = array_keys((array)$caps);
            if (in_array("customer", $roles)) {
            ?>
            <div class="ordersdata">   
                <input id="ordersubmit" type="submit" name="ordersubmit" value="Send the Order" class="ordersubmit">
            </div>
            <?php  }
                else {?>
              <div class="ordersdata loginbutton">   
               <input id="ordervendor" type="button" value="Sign in as a Consumer Account" class="ordersubmit loginbutton">
             </div>
                <?php }
            } 
        else {?>
              <div class="ordersdata loginbutton">   
               <input id="orderloginbutton" type="button" value="Sign in as a Consumer Account" class="ordersubmit loginbutton">
             </div>
        <?php } ?>
		<?php
		if ( is_user_logged_in() ){
		?>
			<div class='requiredfield_error' style='display: none;'>Please fill the all required fields.</div>
		<?php
		}
		else{
		?>
			<div class='requiredfield_error' style='display: none; height:0px;'></div>
		<?php
		}
        ?>
        <div class='order_detail' style='display: none;'>You can Orders 7 Order Per day.</div>
    </div>
    </div>
    </form>

</div>
    
    
    <!--seconforder process-->
    <div class="productrightbar" style="display:none">
    <form  id='inputorder' method="post" action="">
     
    <div class="copy_html">
       <div class='newbottomli commonorder'>
        <div class='inputnewselectlist form_row_0'>
        <input maxlength="100" type="text"  id="inputproid_0" name="inputproductid[]" class="inputproductid" placeholder='Type product name*'/> 
        <div class='toplist'>
            <select name='inputproductqty[]' class="selectqty proqty proqty_0 copy-custom-select" id='inputproqty_0'>
                                          <option value="" class="">Q.ty*</option>
                                          <?php  for($qty=1;$qty<=100;$qty++){?>
                                          <option value='<?php echo $qty ?>'><?php echo $qty; ?></option>
                                          <?php } ?>
             </select>
            <div class='selectlistcss'></div>
        </div>
   <div class='toplist unittoplist'>
        <select name='inputproductunit[]' class='productunit prounit prounit_0 copy-custom-select' id='inputprounit_0'>
            <option value="" class="">Unit*</option>
                                    <option value="Piece" class="">Piece</option>
                                    <option value="Box" class="">Box</option>
                                    <option value="Botttle" class="">Botttle</option>

       </select>
    </div>
        <input class="minusbutton inputremove_field secondminus" type="button" value="-" style="visibility:hidden">
    </div>
    </div>
    </div>
    <div class="insert_order">
        <input type="button"  value="+" class="newplusbutton minusbutton" id="newplusbutton">
    </div>
<div class="bottomdelivery_outer">

        <div class="bottomdelivery_inner ">
        <div class='bottomradio commondata'>
        <span>Permission</span>
        <div class="permisstion">
             <input  checked id="inputorderradiosecond" class="orderradiosecond orderradiofirst" type="radio" name="inputpermisstion" value="Do not do alternative"><span class="orderradio"><u class="text-click">Do not do alternative</u></span><br>
                                                                        <input id="inputorderradiofirst" class="orderradiofirst" type="radio" name="inputpermisstion" value="Do alternative(Blue)" > <span class="orderradio"><u class="text-click">Do alternative</u> <u class="text-hover"><a href="<?php echo get_site_url();?>/customfaq/#category3" target="_blank">(Blue)</a></u></span><br>
                                                                       
 </div>
    </div>
        <div class='delivery_time commondata'>
            <span>Delivery Time:</span> 
            <div class='inputdeliverydeatails'></div>
            <div class='inputimagecalender'><input type='hidden' id='inputimagecalender'></div>
            <input type='hidden' class='inputdeliverydeatails' name='inputdeliverydeatails' id='inputdeliverydeatails'>
            <div class="inputcustomdatepicker"><input type="hidden" id="inputdatepicker" name='inputdatepicker'></div>
        </div>
        <div class='delivery_time commondata storedelivery'>
            <span>Delivery at:</span>  
            <div class="delivery"><?php global $product;
            echo get_the_title(); ?><input type="hidden" name="myproduct" value="<?php echo  get_the_title(); ?>">
			<input type="hidden" name="myproductID" value="<?php echo  get_the_ID(); ?>">
			</div>
        </div>
        <div class="ordersdata">
           <p>paragraph1 paragraph1 paragraph1 paragraph1 paragraph1 paragraph1 paragraph1 paragraph1 paragraph1 paragraph1 paragraph1 paragraph1 </p>
        </div> 
        <textarea  name="ordercomment" maxlength="500" class="form-control" rows="5" id="ordercomment" placeholder="Notes"></textarea> 
       
        <?php 
      if ( is_user_logged_in() ) {
            $userid = get_current_user_id();
            $caps = get_user_meta($userid, 'wp_capabilities', true);
            $roles = array_keys((array)$caps);
            if (in_array("customer", $roles)) {
            ?>
            <div class="ordersdata">   
                <input id="inputordersubmit" type="submit" name="inputordersubmit" value="Send the Order" class="ordersubmit">
                
            </div> 
            <?php  }
                else {?>
              <div class="ordersdata loginbutton">   
               <input id="inputordervendor" type="button" value="Sign in as a Consumer Account" class="ordersubmit loginbutton">
             </div>
                <?php }
            } 
        else {?>
             <div class="ordersdata loginbutton">   
               <input id="inputorderloginbutton" type="button" value="Sign in as a Consumer Account" class="ordersubmit loginbutton">
             </div>
        <?php } ?>
		<?php
		if ( is_user_logged_in() ){
		?>
			<div class='inputrequiredfield_error' style='display: none;'>Please fill the all required fields.</div>
		<?php
		}
		else{
		?>
			<div class='inputrequiredfield_error' style='display: none; height:0px;'></div>
		<?php
		}
        ?>
        <div class='order_detail' style='display: none;'>You can Orders 7 Order Per day.</div>
 </div>
</div>

</form>
</div>  
</div>
    
    
    
        
 </div><!-- #primary -->
 </div>
    <?php
    /**
     * woocommerce_sidebar hook
     *
     * @hooked woocommerce_get_sidebar - 10
     */
    if ($show_sidebar == 'right') {
        do_action('woocommerce_sidebar');
    } else {
        if (in_array('single', $additional_sidebar)) {
            get_sidebar('additional');
        }
    }
    ?>
</div>
</div>

<a href="myaccount/form-login.php"></a>



<?php get_footer('shop'); ?>

<?php
// Fetch all products

$a_json = array();
$a_json_row = array();
$args = array(
              'product_cat' => 'Cardiologist',
              'post_type' => 'product',
              'orderby' => 'title',
			  'posts_per_page' => -1
      );

$query = new WP_Query( $args );
    while ( $query->have_posts() ) {
      $query->the_post();
      $post_title =  htmlentities(stripslashes( get_the_title()));
      $code = htmlentities(stripslashes(get_the_id()));
      $a_json_row["id"] = $code;
      $a_json_row["label"] = $post_title;
      $a_json_row["value"] = $post_title;
      array_push($a_json, $a_json_row);
    }
?>

<script type="text/javascript">
//jQuery.noConflict();

function validatecalender()
{
	var appandpicker = jQuery('#datepicker').val();
    var ext = jQuery('#order_photo').val().split('.').pop().toLowerCase();
	 if((jQuery('.newselectlist div, .newselectlist input').hasClass("required_error")) || appandpicker == '' || ext == '')
	   {
			jQuery('.requiredfield_error').css('display','block');
	   }
	   else{
			jQuery('.requiredfield_error').css('display','none');
	   }
  var x = document.forms["datepickerform"]["time"].value;
    if (x == "") {
        jQuery('.datepickernotification').html('Please select the Time.');
        return false;
    }
    else
    {
    jQuery('.customdatepicker').css('border','none');     
    var visible = jQuery("#datepicker").datepicker("widget").is(":visible");
    jQuery("#datepicker").datepicker(visible ? "hide" : "show");
    jQuery('.datepickernotification').html('');
    var datedata =  jQuery('#datepicker').val();
    var radioValue = jQuery("input[name='time']:checked").val();
    jQuery('.deliverydeatails').html(radioValue+',  '+datedata);
    var calendervalue =  jQuery('.deliverydeatails').val(radioValue+',  '+datedata);
    if(calendervalue)
    {
     
     jQuery('#orderupload .ui-datepicker-trigger').addClass('newimageclass');
    }

 }
}
jQuery(document).ready(function($)
{ 

	


$('#ui-datepicker-div').css('visibility','block');

$(".custom-select").customselect();
$(".copy-custom-select").copycustomselect();
jQuery(document).keydown(function(e) {
		// ESCAPE key pressed close the popup
		if (e.keyCode == 27) {
		   if(jQuery("#datepicker").datepicker( "widget" ).is(":visible"))
		   {
			   jQuery("#datepicker").datepicker( "hide" );
		   }
		   if(jQuery("#inputdatepicker").datepicker( "widget" ).is(":visible"))
		   {
			   jQuery("#inputdatepicker").datepicker( "hide" );
		   }
		   if(jQuery(".piclocation").is(":visible"))
		   {
			   jQuery(".crossimage").trigger('click');
		   }
		   if(jQuery(".vendor-overlay").is(":visible"))
		   {
			   jQuery(".vendor-overlay").trigger('click');
		   }
		}
		 
	});
	
jQuery(".overlay").click(function(){
	jQuery(".crossimage").trigger('click');
});

$('#datepicker').datepicker({ 
    minDate: 0,
    showOn: "button",
    buttonText: "Select*",
    autoclose: false,
    dateFormat: 'dd/mm/yy',
  
   beforeShow: function () {
    setTimeout(appendsomething, 0);
    $('.overlay').css('display','block');
	$(".site-header .header-main").css('z-index','1');
    },
    onSelect: function() {
      setTimeout(appendsomething, 0);
     $(this).data('datepicker').inline = true;
    },
    onClose: function() {
        $(this).data('datepicker').inline = false;
        $('.overlay').css('display','none');
		$(".site-header .header-main").css('z-index','5');
    }
});
$( "#datepicker" ).datepicker('setDate', 'today');
$(document).unbind('mousedown', $.datepicker._checkExternalClick);
$(".overlay").bind('mousedown', $.datepicker._checkExternalClick);

var appendsomething = function () {
$("#ui-datepicker-div").append("<div class='timediv'><h2 class='time'>Time:</h2><div class='timemenu'><form name='datepickerform' method='post' action='' class='datepickerform' id='datepickerform'><ul><li><input type='radio' value='morning' name='time'><span>morning</span></li><li><input type='radio' value='noon' name='time'><span>noon</span></li><li><input type='radio' value='evening' name='time'><span>evening</span></li><li><input type='radio' value='night' name='time'><span>night</span></li></ul><input type='button'  onclick='validatecalender();' class='allbutton dataajax' value='Select'><span class='datepickernotification'></span></form></div></div>");
$("#ui-datepicker-div").prepend('<div class="topheading"><h2>Date:</h2></div>');
}
$(document).on('click', '.ui-datepicker-next', function () {
appendsomething();

});
$(document).on('click', '.ui-datepicker-prev', function () {
  
   if (!$(this).hasClass("ui-state-disabled")) {
   appendsomething();
}
});


 <?php 
 
 
 
$productname = '<div class="newselectlist"><div class="inner_proclass"><select name="productid[]" class="custom-select newproductlist  proname proname_0"><option value="">Chose your product on uploaded photo*</option>';

$args = array(
                'posts_per_page' => -1,
                'product_cat' => 'uploadorder',
                'post_type' => 'product',
                'orderby' => 'title',
                'order' => 'asc',
        );
$query = new WP_Query( $args );

while ( $query->have_posts() ) {
	$query->the_post();

$productname .='<option value="'.get_the_id().'">'.get_the_title().'</option>';  

}
 wp_reset_postdata(); 

$productname .='</select></div> <div class="toplist"><select class="selectqty proqty proqty_0 copy-custom-select" name="productqty[]" class="selectqty proqty proqty_0"><option value="">Q.ty*</option>';
for($qty=1;$qty<=100;$qty++){
 $productname .= '<option value="'.$qty.'">'.$qty.'</option>';    
}
$productname .='</select></div>';
$productname .='</select> <div class="toplist unittoplist"><select name="productunit[]" class="selectunit prounit prounit_0 copy-custom-select"><option value="">Unit*</option><option value="Piece">Piece</option><option value="Box">Box</option><option value="Botttle">Botttle</option></select></div><input class="minusbutton remove_field" type="button" value="-"></div>';

?>

var max_fields      = 14; //maximum input boxes allowed
var wrapper         = $(".bottomli"); //Fields wrapper
var add_button      = $(".plusbutton"); //Add button ID

var x = 1; //initlal text box count
$(add_button).click(function(e){ //on add input button click
e.preventDefault();

if($(".newselectlist" ).length < max_fields){
    var olddata = '<?php echo $productname;?>';
    $(wrapper).append(olddata); //add input box
    $(".newselectlist" ).last().addClass( "form_row_"+x );
    $( ".form_row_"+x).find("select[name='productid[]']").attr('id','proid_'+x);
    $( ".form_row_"+x).find("select[name='productqty[]']").attr('id','proqty_'+x);
    $( ".form_row_"+x).find("select[name='productunit[]']").attr('id','prounit_'+x);
    $(".custom-select").customselect();
    $(".copy-custom-select").copycustomselect();
    jQuery('.newselectlist .minusbutton').css("visibility","unset");
    x++;
} else {
    jQuery('#plusbutton').css('visibility','hidden'); 
}

});
   
$(wrapper).on("click",".remove_field", function(e){ //user click on remove text
    $('#plusbutton').css('visibility','unset');
    var remove_id  = $(this).parent().find('.proname option:selected').val();
    var remove_title  = $(this).parent().find('.proname option:selected').text();
    e.preventDefault(); $(this).parent('div').remove();// x--;

     checklist('',remove_id,remove_title);
    
    if(jQuery(".newselectlist" ).length <=1){
     jQuery('.newselectlist .minusbutton').css("visibility","hidden");
    }
});

    $(document).on('click', '.custom-select a', function () {
		
    var current_selectid =  $(this).parent().find('select').attr("id");
    checklist(current_selectid);
    });

});

function checklist(current_selectid,remove_id,remove_title,type,onchnageid)
{
 jQuery('.newselectlist .custom-select div ul li').removeClass('option-hover'); 
    jQuery(".newselectlist .custom-select").each(function()
        {
            
            var id_array = [];
            var title_array = [];
            jQuery(jQuery(this)).find('select').each(function()
              { 
                var appand_id =  jQuery( "#"+jQuery(this).attr('id')+" option:selected" ).val();
                var appand_title =  jQuery( "#"+jQuery(this).attr('id')+" option:selected" ).text();
                if (jQuery.trim(appand_id).length > 0) {
                  id_array.push(appand_id);
                  title_array.push(appand_title);
                }
              });
              
               var lidata = [];
                    jQuery(this).find('ul li').each(function() {
                    lidata.push(jQuery(this).text());

                   });
            
            
//            remove  button functionality
             if(remove_id)
               {

                    if(jQuery.inArray(remove_title,lidata) == -1){
                    jQuery('.newselectlist .inner_proclass ul li[data-value="'+remove_id+'"]').show();
                       };
                }
                
//            add button functionality
             if(current_selectid )
                {
//                 console.log(title_array);
                   var j;
                     if (id_array.length >=1) 
                        {
                           for(j=0; j<id_array.length;j++)
                            {
                             jQuery("#"+current_selectid).parent().find('ul li:contains("'+title_array[j]+'")').hide();

                            } 
                        }
                }
        });
//             onchange event
                if(type)
                {
                           <?php $args = array(
                                'posts_per_page' => -1,
                                'product_cat' => 'uploadorder',
                                'post_type' => 'product',
                                'orderby' => 'title',
                                'order' => 'asc',
                            );
                            $query = new WP_Query( $args );
                            $counter_array = array();
                            while ( $query->have_posts() ) {
                                    $query->the_post();
                                     $counter_array[] = get_the_id();
                            

                            } wp_reset_postdata(); ?>
                                    
                    var w;
                    var counter_array = [];
                    var counter_array = <?php echo json_encode($counter_array); ?>;   
                    
                    var difference = [];
                    var newli_array = [];
                    var final_array_diff = [];
                    var id_array = [];
                    var title_array = [];
                    jQuery(".newselectlist .custom-select select").each(function()
                     {
                         
                            var appand_id =  jQuery( "#"+jQuery(this).attr('id')+" option:selected" ).val();
                            var appand_title =  jQuery( "#"+jQuery(this).attr('id')+" option:selected" ).text();
                            if (jQuery.trim(appand_id).length > 0) {
                              id_array.push(appand_id);
                              title_array.push(appand_title);
                            }
                         
                           
                    });
                     jQuery('#'+onchnageid).parent().find('ul li').each(function()
                                {
                                    if ( jQuery(this).css('display') == 'block')
                                        {
                                           newli_array.push(jQuery(this).attr('data-value'));
                                        }

                                });
                     
//                      console.log(newli_array);
                     
                    jQuery.grep(counter_array, function(el) {
                              if (jQuery.inArray(el, id_array) == -1) difference.push(el);
                      });


                     

                       jQuery.grep(difference, function(el) {
                              if (jQuery.inArray(el, newli_array) == -1) final_array_diff.push(el);
                      });

                     var p;
                      if (final_array_diff.length >=1) 
                          {
                              for(p=0; p<final_array_diff.length;p++)
                               {
                                jQuery('.newselectlist .inner_proclass ul li[data-value="'+final_array_diff[p]+'"]').show();
    
                               }

                        }

                }
         

             jQuery(jQuery(this).find('ul li')).sort(function( a, b ) {
                  return jQuery( a ).text() > jQuery( b ).text(); 

          }).appendTo(jQuery(this).find('ul'));
  
 }

</script>
<script>
    function readURL(input) {
        var ext = jQuery('#order_photo').val().split('.').pop().toLowerCase();
            if(jQuery.inArray(ext, ['gif','png','jpg','jpeg']) == -1) {
            jQuery('.orderimageerror').html('Upload Photo File only.'); 
            jQuery('.imagelabel').css('border','3px solid red');
			var appandpicker = jQuery('#datepicker').val()
				if ((!jQuery('.newselectlist div').hasClass("required_error")) && appandpicker!='') {
				jQuery('.requiredfield_error').css('display','none'); 
				}
			}
            else
            {
            jQuery('.orderimageerror').html(''); 
            jQuery('.imagelabel').css('border','1px solid #dcdff1');
            }
     if (input.files && input.files[0]) {
         var reader = new FileReader();

         reader.onload = function (e) {
             jQuery('#blah').css('display','block');
             jQuery('#blah')
                 .attr('src', e.target.result)
                 .width(150)
                 .height(200);
        
         };

         reader.readAsDataURL(input.files[0]);
        
     }
 }

   jQuery('#orderupload .inner_proclass select[name^="productid[]"]').live('change', (function () {
        var type = "onchange";
       var onchnageid = jQuery(this).attr('id');
       
        checklist('','','',type,onchnageid);

  })
 );
 
 jQuery("#orderupload").on('change','select',function () {
     
  if(jQuery(this).val() == '') {
             jQuery('#'+jQuery(this).attr('id')).parent().closest('div').addClass('required_error');
             //jQuery('.requiredfield_error').css('display','block');
             errorFlag = true;
            }
            else
            {
              jQuery('#'+jQuery(this).attr('id')).parent().closest('div').removeClass('required_error');
			   var ext = jQuery('#order_photo').val().split('.').pop().toLowerCase();
			   var appandpicker = jQuery('#datepicker').val()
			   
			   /*if((ext=='')|| appandpicker == '')
			   {
				    jQuery('.requiredfield_error').css('display','block');
			   }
			   else{
				    jQuery('.requiredfield_error').css('display','none');
			   }*/
            }
			
			/*if(jQuery("#orderupload .copy_html div.required_error").length>0){
				jQuery('.requiredfield_error').css('display','block');
			}
			else{
				jQuery('.requiredfield_error').css('display','none');
			}*/
    });
jQuery("#inputorder").on('change','select',function () { 
        if(jQuery(this).val() == '') {
            jQuery('#'+jQuery(this).attr('id')).parent().closest('div').addClass('required_error');
            //jQuery('.inputrequiredfield_error').css('display','block');
             errorFlag = true;
            }
            else
            {
              jQuery('#'+jQuery(this).attr('id')).parent().closest('div').removeClass('required_error');
			  
			  
			   var appandpicker = jQuery('#inputdatepicker').val();
			   
			   /*if((jQuery('.inputnewselectlist input').hasClass("required_error"))|| appandpicker == '')
			   {
				    jQuery('.inputrequiredfield_error').css('display','block');
			   }
			   else{
				    jQuery('.inputrequiredfield_error').css('display','none');
			   }*/
            }
			
			/*if(jQuery("#inputorder .copy_html div.required_error").length>0){
				jQuery('.inputrequiredfield_error').css('display','block');
			}
			else{
				jQuery('.inputrequiredfield_error').css('display','none');
			}*/
    });
    
jQuery('#inputorder').on('change', 'input:text', function () {
if(jQuery(this).val() == '') {
     jQuery('#'+jQuery(this).attr('id')).addClass('required_error');
     errorFlag = true;
	 //jQuery('.inputrequiredfield_error').css('display','block');
    }
    else
    {
      jQuery('#'+jQuery(this).attr('id')).removeClass('required_error');
	   var appandpicker = jQuery('#inputdatepicker').val();
	  
	   /*if((jQuery('.inputnewselectlist div').hasClass("required_error"))|| appandpicker == '')
	   {
			jQuery('.inputrequiredfield_error').css('display','block');
	   }
	   else{
			jQuery('.inputrequiredfield_error').css('display','none');
	   }*/
    }
});    
  

 
 jQuery(document).ready(function () {

    jQuery('#orderupload').submit(function()
    {
      var fromid = jQuery(this).attr('id');
      var errorFlag = false;
        jQuery('#orderupload select').each(function () {           
            if(jQuery(this).val() == '') {
             jQuery('#'+jQuery(this).attr('id')).parent().closest('div').addClass('required_error');
             errorFlag = true;
            }
            else
            {
              jQuery('#'+jQuery(this).attr('id')).parent().closest('div').removeClass('required_error');
            }
        });

        /*var checkcalendar = jQuery('#datepicker').val();
        if(checkcalendar == '')
        {
          jQuery('.customdatepicker').css('border','3px solid red');  
          errorFlag = true;
        }
        else
        {
         jQuery('.customdatepicker').css('border','none');    
        }*/
		
		var checkcalendar = jQuery('div.deliverydeatails').html();
        if(checkcalendar == '')
        {
          jQuery('.customdatepicker').css('border','3px solid red');  
          errorFlag = true;
        }
        else
        {
         jQuery('.customdatepicker').css('border','none');    
        }
		
        var ext = jQuery('#order_photo').val().split('.').pop().toLowerCase();
		
			if(ext==''){
				 jQuery('.imagelabel').css('border','3px solid red');
				 errorFlag = true;
			}
//        set flag error message
        if(errorFlag == true) {
            jQuery('.requiredfield_error').css('display','block');
            return false;
        } 
		else{
          jQuery('.requiredfield_error').css('display','none');  
		  var ext = jQuery('#order_photo').val().split('.').pop().toLowerCase();
		  if(ext)
			{
				if(jQuery.inArray(ext, ['gif','png','jpg','jpeg']) == -1) {
					jQuery('.orderimageerror').html('Upload Photo File only.'); 
					jQuery('.imagelabel').css('border','3px solid red');
					
		        }
				else{
					ordercreateajax(fromid);
				}
			}
          
          return false;
       }
        
    });
               
});
                 
function ordercreateajax(fromid)
{ 
jQuery("#preloader").css('display','block'); 
jQuery("#status").css('display','block'); 
var file_data = jQuery("#order_photo").prop("files")[0];
var permission = jQuery("input[name='permisstion']:checked").val();
var orderdate = jQuery('.deliverydeatails').val();
var ordercomment = jQuery('#ordercomment').val();
var productname = jQuery('#standard').find(":selected").val();
var form_data = new FormData(); // Creating object of FormData class
form_data.append("file", file_data)
form_data.append("productname", jQuery('#orderupload').serialize())
form_data.append("permission", permission)
form_data.append("orderdate", orderdate)
form_data.append("ordercomment", ordercomment)
form_data.append("fromid", fromid)
form_data.append("inputorderdata", jQuery('#inputorder').serialize())
jQuery.ajax({
            type: 'POST',               
            processData: false, // important
            contentType: false, // important
            data: form_data,
            url: '<?php echo get_stylesheet_directory_uri().'/woocommerce/ajax/ordercreateajax.php' ?>',
            dataType : 'json',
         success: function(result){
                jQuery("#preloader").css('display','none'); 
                jQuery("#status").css('display','none'); 
                if(result['error'] && result['error']=='1')
                      {
                     jQuery('.order_detail').css('display','block');   
                      }
                else
                {
                 jQuery('.order_detail').css('display','none'); 
                 var id = result['finalorder_id'];
                 var email = result['consumeremail'];
                 jQuery('.complete_div').css('display','none');
                 jQuery('.order_history').css('display','block');
                 jQuery('.useremail_id').html(id);
                 jQuery('.userorder_id').html(email);
				 jQuery(window).scrollTop(0);
//                 window.location.assign("<?php // get_permalink(2415); ?>?orderid="+id+"&email="+email);
                }
                          
               
           },
 });
}
</script>


<!--second order process-->

<link rel="stylesheet" type="text/css"  href="<?php echo get_stylesheet_directory_uri().'/css/jquery-ui.css' ?>" />
<script src="<?php echo get_stylesheet_directory_uri().'/js/jquery-ui.js'?>" ></script>

<script>
    
function newvalidatecalender()
{
	 var appandpicker = jQuery('#inputdatepicker').val();
	 if((jQuery('.inputnewselectlist div, .inputnewselectlist input').hasClass("required_error"))|| appandpicker == '')
	   {
			jQuery('.inputrequiredfield_error').css('display','block');
	   }
	   else{
			jQuery('.inputrequiredfield_error').css('display','none');
	   }
   var x = document.forms["inputdatepickerform"]["time"].value;
   if (x == "") {
        jQuery('.inputdatepickernotification').html('Please select the Time.');
        return false;
    }
    else
    {
    jQuery('.inputcustomdatepicker').css('border','none'); 
    var visible = jQuery("#inputdatepicker").datepicker("widget").is(":visible");
    jQuery("#inputdatepicker").datepicker(visible ? "hide" : "show");
    jQuery('.inputdatepickernotification').html('');
    var datedata =  jQuery('#inputdatepicker').val();
    var radioValue = jQuery("input[name='time']:checked").val();
    jQuery('.inputdeliverydeatails').html(radioValue+',  '+datedata);
    var calendervalue =  jQuery('.inputdeliverydeatails').val(radioValue+',  '+datedata);
    if(calendervalue)
    {
     jQuery('#inputorder .ui-datepicker-trigger').addClass('inputnewimageclass');
    }
 }
}    
jQuery(document).ready(function () {
      jQuery('#inputorderloginbutton, #orderloginbutton').click(function()
         {
                jQuery('.customtrigger').trigger('click'); 
                jQuery('.asstore').css('display','none');
         }); 
         
	 jQuery('#inputordervendor, #ordervendor').click(function(){
                jQuery("#login_form").css("display", "block");
                jQuery('.logindata').css("display","block"); 
                jQuery('.forgot_pass_outer').css("display","none"); 
                jQuery('.asstore').css('display','none');
                document.getElementById("forgotform").reset();
                document.getElementById("customloginform").reset();
                jQuery('.forgoterror ').html(''); 
                jQuery(".loginerror").html('');
                jQuery(".error").html('');
                jQuery(".vendor-overlay").css({
                        "position": "fixed",
                        "background-color": "rgba(0, 0, 0, 0.5)",
                        "z-index": "1",
                        "height": "200vh",
                        "bottom": "auto",
                        "left": "0",
                        "top": "0",
                        "right": "0",
                });
          jQuery('.widget_azexo_dashboard_links .root').css('display','none');
          jQuery('.site-header .header-main .header-my-account.logged-in .dropdown .link').css('margin-top','13px');
		  
        });	 
    jQuery(".vendor-overlay").click(function(){
            jQuery("#login_form").css("display", "none");
            jQuery(".vendor-overlay").removeAttr("style");
            jQuery(".dropdown").css("pointer-events","all");
			jQuery('.widget_azexo_dashboard_links .root').css('display','block');
			jQuery('.site-header .header-main .header-my-account.logged-in .dropdown .link').removeAttr('style');
    });

     jQuery('.link').click(function(){
                    jQuery('.asstore').css('display','block'); 
     }) ;
		 
   jQuery('.tab2').click(function()
   {
     jQuery(this).addClass('active');
     jQuery('.productrightbar').css('display','block');
     jQuery('.productleftbar').css('display','none');
     jQuery('.tab1').removeClass("active");
   });
   jQuery('.tab1').click(function()
   {
     jQuery(this).addClass('active');
     jQuery('.productrightbar').css('display','none');
     jQuery('.productleftbar').css('display','block');
     jQuery('.tab2').removeClass("active");
   });

    var availableTags = <?php echo json_encode($a_json); ?>;
   
 jQuery('.inputproductid').autocomplete({
  source:availableTags,
  minLength:1,
  maxShowItems: 5
 });
<?php 
$inputproductname = '<div class="inputnewselectlist"> <input maxlength="100" type="text" class="inputproductid" name="inputproductid[]" placeholder="Type product name*"/> ';
$inputproductname .='<div class="toplist"><select class="selectqty proqty proqty_0 copy-custom-select" name="inputproductqty[]"><option value="">Q.ty*</option>';
for($qty=1;$qty<=100;$qty++){
$inputproductname .= '<option value="'.$qty.'">'.$qty.'</option>';    
}
$inputproductname .='</select></div>';
$inputproductname .='</select> <div class="toplist unittoplist"><select name="inputproductunit[]" class="selectunit prounit prounit_0 copy-custom-select"><option value="">Unit*</option><option value="Piece">Piece</option><option value="Box">Box</option><option value="Botttle">Botttle</option></select></div><input class="minusbutton inputremove_field" type="button" value="-"></div>';

?>    
var max_fields      = 14; //maximum input boxes allowed
var wrapper         = jQuery(".newbottomli"); //Fields wrapper
var add_button      = jQuery(".newplusbutton"); //Add button ID

var x = 1; //initlal text box count
jQuery(add_button).click(function(e){ //on add input button click
e.preventDefault();
if(jQuery(".inputnewselectlist" ).length < max_fields){ //max input box allowed
  //text box increment
 jQuery(wrapper).append('<?php echo $inputproductname;?>'); //add input box
 jQuery(".inputnewselectlist" ).last().addClass( "form_row_"+x );
 jQuery( ".form_row_"+x).find("select[name='inputproductqty[]']").attr('id','inputproqty_'+x);
 jQuery( ".form_row_"+x).find("select[name='inputproductunit[]']").attr('id','inputprounit_'+x);
 jQuery( ".form_row_"+x).find("input[name='inputproductid[]']").attr('id','inputproid_'+x);
 jQuery(".custom-select").customselect();
 jQuery(".copy-custom-select").copycustomselect();
 
 var availableTags = <?php echo json_encode($a_json); ?>;
 
 jQuery('.inputproductid').autocomplete({
  source:availableTags, 
  minLength:1,
  maxShowItems: 5
 });
jQuery('.inputnewselectlist .minusbutton').css("visibility","unset");
 x++;
} else {
 jQuery('#newplusbutton').css('visibility','hidden'); 
}
});
jQuery(wrapper).on("click",".inputremove_field", function(e){ //user click on remove text
jQuery('#newplusbutton').css('visibility','unset'); 
e.preventDefault(); jQuery(this).parent('div').remove();// x--;
if(jQuery(".inputnewselectlist" ).length <=1){
 jQuery('.inputnewselectlist .minusbutton').css("visibility","hidden");
}
});   
jQuery('#inputdatepicker').datepicker({ 
 minDate: 0,
 setDate: new Date(),
 showOn: "button",
 buttonText: "Select*",
 autoclose: false,
 dateFormat: 'dd/mm/yy',

beforeShow: function () {
 setTimeout(appendsomething, 0);
 jQuery('.overlay').css('display','block');
 jQuery(".site-header .header-main").css('z-index','1');
 },
 onSelect: function() {
 jQuery(this).data('datepicker').inline = true;
  setTimeout(appendsomething, 0);

 },
 onClose: function() {
     jQuery(this).data('datepicker').inline = false;
     jQuery('.overlay').css('display','none');
	 jQuery(".site-header .header-main").css('z-index','5');
 }
});
jQuery( "#inputdatepicker" ).datepicker('setDate', 'today');
var appendsomething = function () {
jQuery("#ui-datepicker-div").append("<div class='timediv'><h2 class='time'>Time:</h2><div class='timemenu'><form name='inputdatepickerform' method='post' action='' class='datepickerform' id='datepickerform'><ul><li><input type='radio' value='morning' name='time'><span>morning</span></li><li><input type='radio' value='noon' name='time'><span>noon</span></li><li><input type='radio' value='evening' name='time'><span>evening</span></li><li><input type='radio' value='night' name='time'><span>night</span></li></ul><input type='button'  onclick='newvalidatecalender();' class='allbutton dataajax' value='Select'><span class='inputdatepickernotification'></span></form></div></div>");
jQuery("#ui-datepicker-div").prepend('<div class="topheading"><h2>Date:</h2></div>');
}

jQuery('#inputorder').submit(function()
 {
   var fromid = jQuery(this).attr('id');
    var errorFlag = false;
     jQuery('#inputorder select').each(function () {           
         if(jQuery(this).val() == '') {
          jQuery('#'+jQuery(this).attr('id')).parent().closest('div').addClass('required_error');
          errorFlag = true;
         }
         else
         {
           jQuery('#'+jQuery(this).attr('id')).parent().closest('div').removeClass('required_error');
         }
     });

    jQuery('#inputorder input[type=text]').each(function () {           
         if(jQuery(this).val() == '') {
          jQuery('#'+jQuery(this).attr('id')).addClass('required_error');
          errorFlag = true;
         }
         else
         {
           jQuery('#'+jQuery(this).attr('id')).removeClass('required_error');
         }
     });
    /*var checkcalendar = jQuery('#inputdatepicker').val();
     if(checkcalendar == '')
     {
       jQuery('.inputcustomdatepicker').css('border','3px solid red');  
       errorFlag = true;
     }
     else
     {
      jQuery('.inputcustomdatepicker').css('border','none');    
     }*/
	 
	 var checkcalendar = jQuery('div.inputdeliverydeatails').val();
     if(checkcalendar == '')
     {
       jQuery('.inputcustomdatepicker').css('border','3px solid red');  
       errorFlag = true;
     }
     else
     {
      jQuery('.inputcustomdatepicker').css('border','none');    
     }
	 
     if(errorFlag == true) {
         jQuery('.inputrequiredfield_error').css('display','block');
         return false;
     }
    else
    {
       jQuery('.inputrequiredfield_error').css('display','none');  
       ordercreateajax(fromid);
       return false;
    }
 });  
});     
</script>