<?php
?><!DOCTYPE html>
<!--[if IE 7]>
<html class="ie ie7" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 8]>
<html class="ie ie8" <?php language_attributes(); ?>>
<![endif]-->
<!--[if !(IE 7) | !(IE 8)  ]><!-->
<html <?php language_attributes(); ?>>
    <!--<![endif]-->
    <head>
        <?php $options = get_option(AZEXO_FRAMEWORK); ?>
        <meta charset="<?php bloginfo('charset'); ?>">
        <meta name="viewport" content="width=device-width">
        <link rel="profile" href="http://gmpg.org/xfn/11">
        <link rel="pingback" href="<?php esc_url(bloginfo('pingback_url')); ?>">        
        <?php
        if (!has_site_icon()) {
            if (isset($options['favicon']['url']) && !empty($options['favicon']['url'])) {
                print '<link rel="shortcut icon" href="' . esc_url($options['favicon']['url']) . '" />';
            }
        } else {
            wp_site_icon();
        }
        ?>
        <?php wp_head(); 
        
        $validatejspath  =  get_stylesheet_directory_uri().'/js/jquery.validate.min.js';
        $additional  =  get_stylesheet_directory_uri().'/js/additional-methods.js';
               ?>
        <script src="<?php echo  $validatejspath; ?>"></script>
        <script src="<?php echo  $additional; ?>"></script>
       
        <!--zscript async defer  src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBxfncg-b5W3Sd9VdRCvtmS3VeQcDAb05g"></script-->
        
    </head>

    <body <?php body_class(); $count=1; ?>>    
  
        <div id="preloader"><div id="status"></div></div>
         <div id="page" class="hfeed site">
            <header id="masthead" class="site-header clearfix">
                <?php
                get_sidebar('header');
                ?>                
                <div class="header-main clearfix">
                    <div class="header-parts <?php print ((isset($options['header_parts_fullwidth']) && $options['header_parts_fullwidth']) ? '' : 'container'); ?>">
                        <?php
                        if (isset($options['header'])) {
                            foreach ((array) $options['header'] as $part) {

                                $template_part = azexo_is_template_part_exists('template-parts/header', $part);
                                if (!empty($template_part)) {
                                    get_template_part('template-parts/header', $part);
                                } else {
                                    switch ($part) {
                                        case 'logo':
                                            if (isset($options['logo']['url']) && !empty($options['logo']['url'])) {
                                                ?>
                                                <a class="site-title" href="<?php print esc_url(home_url('/')); ?>" rel="home"><img src="<?php print esc_url($options['logo']['url']); ?>" alt="logo"></a>
                                                <?php
                                            }
                                            break;
                                        case 'search':
                                            azexo_get_search_form();
                                            break;
                                        case 'mobile_menu_button':
                                            ?>
                                            <div class="mobile-menu-button"><span><i class="fa fa-bars"></i></span></div>                    
                                            <?php
                                            break;
                                        case 'mobile_menu':
                                            ?><nav class="site-navigation mobile-menu"><?php
                                                        if (has_nav_menu('primary')) {
                                                            wp_nav_menu(array(
                                                                'theme_location' => 'primary',
                                                                'menu_class' => 'nav-menu',
                                                                'menu_id' => 'primary-menu-mobile',
                                                                'walker' => new AZEXO_Walker_Nav_Menu(),
                                                            ));
                                                        }
                                                        ?></nav><?php
                                            break;
                                        case 'primary_menu':
                                            ?><nav class="site-navigation primary-navigation"><?php
                                                if (has_nav_menu('primary')) {
                                                    wp_nav_menu(array(
                                                        'theme_location' => 'primary',
                                                        'menu_class' => 'nav-menu',
                                                        'menu_id' => 'primary-menu',
                                                        'walker' => new AZEXO_Walker_Nav_Menu(),
                                                    ));
                                                }
                                                ?></nav><?php
                                            break;
                                        case 'secondary_menu':
                                            ?><nav class="secondary-navigation"><?php
                                                if (has_nav_menu('secondary')) {
                                                    wp_nav_menu(array(
                                                        'theme_location' => 'secondary',
                                                        'menu_class' => 'nav-menu',
                                                        'menu_id' => 'secondary-menu',
                                                        'walker' => new AZEXO_Walker_Nav_Menu(),
                                                    ));
                                                }
                                                ?></nav><?php
                                            break;
                                        default:
                                            break;
                                    }
                                }
                            }
                        }
                        ?>                        
                    </div>
                </div>
                <?php
                get_sidebar('middle');
                ?>                                
            </header><!-- #masthead -->
			
            <div id="main" class="site-main">
<script type="text/javascript">
jQuery(document).ready(function(){
  jQuery(".entry-thumbnail a").removeAttr("href");
  jQuery( ".vc_tta-tabs-container" ).find('li').click(function(){
     if(jQuery(this).find('a').attr('href') == "#Faqs")
    {
        jQuery('.vc_tta-panels-container').css({"display":"none"});
    }
    else
    {
        jQuery('.vc_tta-panels-container').css({"display":"block"});
    }
  });
    
var x = 1;
jQuery( ".vc_tta-tabs-container" ).find('li').each(function(){
  var className =  jQuery(this).find('span').text();
  var ankerName =  jQuery(this).find('a').attr('href'); 
 if(className.toLowerCase()=='faqs'){
    var startpoint = parseInt(x+1);
    jQuery(this).click(function(){
          var total_child = jQuery( ".vc_tta-tabs-list li").length;
                            for(i=startpoint;i<=total_child;i++){
                                    jQuery( ".vc_tta-tabs-list li:nth-child("+i+")" ).slideToggle();
                            }
          });  
  }
if(ankerName=='#aboutus')
      {
     var startpoint = parseInt(x+1);      
     jQuery(this).click(function(){
         
              var total_child = jQuery( ".vc_tta-tabs-list li").length;
                            for(i=startpoint+2;i<=total_child;i++){
                                    jQuery( ".vc_tta-tabs-list li:nth-child("+i+")" ).hide();
                            }
           });
      }
  if(ankerName=='#term')
      {
     var startpoint = parseInt(x+1);      
     jQuery(this).click(function(){
              var total_child = jQuery( ".vc_tta-tabs-list li").length;
                            for(i=startpoint+1;i<=total_child;i++){
                                    jQuery( ".vc_tta-tabs-list li:nth-child("+i+")" ).hide();
                            }
           });
      }    
 
x++;
 });
 jQuery( ".vc_tta-tabs-container" ).find('li').each(function(){
  var className =  jQuery(this).find('span').text();      
  if(className.toLowerCase()=='faqs'){
    jQuery(this).trigger('click');
 }
});

 jQuery( ".vc_tta-tabs-container" ).find('li').each(function(){
  var className =  jQuery(this).find('span').text();      
  if(className.toLowerCase()=='about us'){
    jQuery(this).trigger('click');
 }
});

</script>

<script type="text/javascript">
jQuery(document).ready(function()
{	
 if(jQuery('.not_login_message').is(':visible')) {
        
        jQuery('#page').hide();
     }
else
{
    jQuery('#page').show(); 
}
    
    
//   Code for the White list for consumer. 
    <?php
    if ( is_user_logged_in() ) {
     $userid = get_current_user_id();
     $caps = get_user_meta($userid, 'wp_capabilities', true);
     $roles = array_keys((array)$caps);
     if (in_array("customer", $roles)) {?>      
     jQuery(".homenewtab .vc_tta-tabs-container ul li a[href*='#customwhitelist']").add(); 
    <?php }
    else { ?>
     jQuery(".homenewtab .vc_tta-tabs-container ul li a[href*='#customwhitelist']").remove();       
    <?php } }else { ?>
    jQuery(".homenewtab .vc_tta-tabs-container ul li a[href*='#customwhitelist']").remove();      
     <?php } ?>
//   end Code for the White list for consumer.
  
// leftsidebaron store detail page   
if(jQuery('.customlikeclass').length == 0) {
jQuery('.productrow .Whitelist').css('display','none');
jQuery('.single-product .productrow .Close i').css('top','16px');
}

jQuery('.sl-button').click(function()
{
  if(jQuery(this).attr('title')=='Unlike')
  {
    jQuery('.addtofeb').css('display','block');  
    jQuery('.removetofev').css('display','none'); 
  }
  else
  {
    jQuery('.removetofev').css('display','block'); 
    jQuery('.addtofeb').css('display','none');  
  }
});




// end of leftsidebaron store detail page 

jQuery('.Location h3').click(function()
{
  jQuery('.piclocation').css('visibility','unset'); 
   jQuery('.overlay').css('display','block');
});

jQuery('.crossimage').click(function()
{
  jQuery('.piclocation').css('visibility','hidden'); 
   jQuery('.overlay').css('display','none');
});

jQuery('.signinpopup').click(function()
{
  jQuery('.customtrigger').trigger('click');  
});

    
    jQuery( "#mylocationsearch" ).change(function() {
 if(jQuery(".pac-container .pac-item:first span:eq(3)").text() == "")
     
    var  firstValue = jQuery(".pac-container .pac-item:first .pac-item-query").text();

else
    firstValue = jQuery(".pac-container .pac-item:first .pac-item-query").text() + ", " + jQuery(".pac-container .pac-item:first span:eq(3)").text();
    jQuery(this).val(firstValue);

    jQuery(".pac-container").trigger('click');

        });
});

<?php
if ( is_user_logged_in() ) {
     $userid = get_current_user_id();
    $customcaps = get_user_meta($userid, 'custom_capability', true);
    if(count($user_roles)>1)
    ?>
  jQuery( ".widget_azexo_dashboard_links .root li a" ).first().attr('href', '').css({'cursor': 'pointer', 'pointer-events' : 'none'}); 
  jQuery( ".widget_azexo_dashboard_links .root li a" ).first().attr('id', 'customspan');
   var loginspan =  jQuery( ".widget_azexo_dashboard_links .root li a" ).first().html();
   var str=loginspan.replace(/(%)(.*)(%)/g,"<span class='customspanlogin'>$2</span>");
   document.getElementById("customspan").innerHTML=str;  
   jQuery('.signup.right_signup').css('display','none');
    
<?php }
?>
    
function homesearchfrom1()
{
    
    if(!jQuery("#mylocationsearch").val())
    {
         jQuery(".newcustomclasssubmit").trigger('click');
    }
    else 
    {
        setTimeout(function() {   //calls click event after a certain time
            jQuery(".newcustomclasssubmit").trigger('click');
          }, 1000);
    }
}

function homesearchfrom()
{ 
   if(locationvalue)
    else
        {
        jQuery('#mylocationsearch').get(0).setCustomValidity('Please input location.');
    
    document.getElementById("customradiusclass").defaultValue = "05";
    var x;
    x = document.getElementById("customradiusclass").value;
    if (isNaN(x) || x < 1 || x > 10) {
       return false;
    }
   
}
function checklogin()
{
	jQuery('.not_login_message').hide();
        jQuery('#page').show(); 
}

</script>