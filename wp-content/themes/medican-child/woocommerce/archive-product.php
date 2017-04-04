<?php
/**
 * The Template for displaying product archives, including the main shop page which is a post type archive.
 *
 * Override this template by copying it to yourtheme/woocommerce/archive-product.php
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     10.0.0
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
global $wp_query;


$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
$default_posts_per_page = get_option( 'posts_per_page' );
$mycount = ($paged*$default_posts_per_page)-$default_posts_per_page;
$count=$mycount+1;
$numcount=$mycount+1;
    
$post_type = isset($wp_query->query['post_type']) ? $wp_query->query['post_type'] : 'post';
if (is_array($post_type)) {
    $post_type = reset($post_type);
}
$options = get_option(AZEXO_FRAMEWORK);
if (!isset($show_sidebar)) {
    $show_sidebar = isset($options['show_sidebar']) ? $options['show_sidebar'] : 'right';
}
if (!isset($product_template)) {
    $product_template = isset($options['default_product_template']) ? $options['default_product_template'] : 'shop_product';
}
if (isset($_GET['template'])) {
    $product_template = $_GET['template'];
}
if (isset($_GET['mode']) && $_GET['mode'] == 'gmap') {
    $product_template = 'google_map_post';
}
$additional_sidebar = isset($options[$post_type . '_additional_sidebar']) ? (array) $options[$post_type . '_additional_sidebar'] : array();
get_header('shop');
?>
<script src="https://cdn.rawgit.com/googlemaps/v3-utility-library/master/markerwithlabel/src/markerwithlabel.js"></script>


<script type='text/javascript'>
jQuery(document).ready(function(){
jQuery('.tab2').trigger('click');

//setTimeout(function() {
//    jQuery('.tab1').trigger('click');
//}, 1e3);
    jQuery('.tab1').click(function()
    {
        jQuery(".customtableft").show();
        jQuery('.customtabright').hide();
        jQuery('.tab1').addClass('active');
        jQuery('.tab2').removeClass('active');
    });
    jQuery('.tab2').click(function()
    {


        jQuery(".customtableft").hide();
        jQuery('.customtabright').show();
        jQuery('.customtabright').css('visibility','unset');
        jQuery('.customtabright').css('height','auto');
        jQuery('.tab2').addClass('active');
        jQuery('.tab1').removeClass('active');
//        google.maps.event.trigger(map, "resize");
        //google.maps.setZoom(13);
        //jQuery('div[title="Zoom out"]').trigger('click');

     });


});
//function geocodeAddress(addresses, geocoder, resultsMap,contant,icons,number,infowindow){
//    var bounds = new google.maps.LatLngBounds();
//    var address = addresses;
//    
//    geocoder.geocode({'address': address}, function(results, status){
//        if (status === 'OK'){
//            
//            var marker = new google.maps.Marker({
//                map: resultsMap,
//                position: results[0].geometry.location,
//                icon: icons,
//            });
//            bounds.extend(marker.position);
//
//           google.maps.event.addListener(marker, 'click', function() {
//                infowindow.close();
//                infowindow.setContent(contant);
//                infowindow.open(map,marker);
//            });
//            
//            map.fitBounds(bounds);
//            map.setCenter(marker.getPosition());
//            
//        }
//        else{
//            //alert('Geocode was not successful for the following reason: ' + status);
//        }
//    });
//}
</script>



<?php if (isset($options['before_list_place']) && ($options['before_list_place'] == 'before_container')): ?>
    <div class="before-shop-loop">
        <?php
        /**
         * woocommerce_before_shop_loop hook
         *
         * @hooked woocommerce_result_count - 20
         * @hooked woocommerce_catalog_ordering - 30
         */
        do_action('woocommerce_before_shop_loop');
        ?>
    </div>
<?php endif; ?>

<?php $searchproduct = $_GET['s']?>
<div class="search_top_outer">
    <div class="search_top_inner container ">
        <div class="bottom_sidebar_inner">
            <h1> Search Results </h1>
           <?php  $locationis  = $_GET['location'];
               $radiusvalue  = $_GET['radius'];
           ?>
            <span>Search result for <?php echo $searchproduct. '&nbsp;at&nbsp;' .$locationis.'&nbsp;in&nbsp;'.$radiusvalue.'&nbsp;(km)'; ?></span>
        </div>
    </div>
</div>



<div class="<?php print ((isset($options['content_fullwidth']) && $options['content_fullwidth']) ? '' : 'container'); ?> <?php print (is_active_sidebar('shop') && ($show_sidebar != 'hidden') ? 'active-sidebar ' . esc_attr($show_sidebar) : ''); ?> <?php print (in_array('list', $additional_sidebar) ? 'additional-sidebar' : ''); ?>">
    <?php
    /**
     * woocommerce_sidebar hook
     *
     * @hooked woocommerce_get_sidebar - 10
     */
    if ($show_sidebar == 'left') {
        do_action('woocommerce_sidebar');
    } else {
        if (in_array('list', $additional_sidebar)) {
            get_sidebar('additional');
        }
    }
    ?>
    <div id="primary" class="content-area">
        <?php
        /**
         * woocommerce_before_main_content hook
         *
         * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
         * @hooked woocommerce_breadcrumb - 20
         */
        do_action('woocommerce_before_main_content');
        ?>

        <?php if (apply_filters('woocommerce_show_page_title', true)) : ?>
            <div class="page-header">
                <h1 class="page-title"><?php woocommerce_page_title(); ?></h1>
            </div>            
        <?php endif; ?>

        <div class='tab_heading'>
            <ul>
                <li  class="tab-switcher active tab1" data-tab-index="0">List of Search Result</li>
                <li  class="tab-switcher tab2" data-tab-index="1">Map of Search Result</li>
            </ul>


        </div>
        <div class="customtabright" style="visibility:hidden; height: 0px;">

            <?php  print azl_google_map_shortcode(array());
             remove_action('woocommerce_after_shop_loop', 'woocommerce_pagination', 10);
            ?>
            
            <!--<div id="map" style="height: 400px; width: 100%;"></div>-->


        </div>
        <div id="content" class="detailed-shop-product customtableft site-content <?php print str_replace('_', '-', $product_template); ?> <?php print ((isset($options['infinite_scroll']) && $options['infinite_scroll']) ? 'infinite-scroll' : '') ?>" role="main">


            <?php do_action('woocommerce_archive_description'); ?>

            <?php
            if (have_posts()) :
                update_meta_cache($post_type, array_keys($wp_query->posts));
                ?>

                <?php if (isset($options['before_list_place']) && ($options['before_list_place'] == 'inside_content_area')): ?>
                    <div class="before-shop-loop">
                        <?php
                        /**
                         * woocommerce_before_shop_loop hook
                         *
                         * @hooked woocommerce_result_count - 20
                         * @hooked woocommerce_catalog_ordering - 30
                         */
                        do_action('woocommerce_before_shop_loop');
                        ?>
                    </div>
                <?php endif; ?>

                <?php
               
                if (isset($_GET['mode']) && $_GET['mode'] == 'gmap' && function_exists('azl_google_map_shortcode')) {
                    print azl_google_map_shortcode(array());
                    remove_action('woocommerce_after_shop_loop', 'woocommerce_pagination', 10);
                } else {
                    woocommerce_product_loop_start();
                    woocommerce_product_subcategories();
                    while (have_posts()) {
                       
                         the_post();
                         $postid = get_the_ID();
                         $storeaddress[] =  get_post_meta(get_the_ID(), 'store_address', true );
                         $storeat=  get_post_meta(get_the_ID(), 'latitude', true );
                         $storelon=  get_post_meta(get_the_ID(), 'longitude', true );
                         $location = ($storeat.','.$storelon);
                         $image = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'single-post-thumbnail' );
                        
                         if($image)
                         {
                          $imagepath = $image[0];   
                         }
                         else
                         {
                           $imagepath = get_stylesheet_directory_uri().'/images/imagenotavailable_lg.jpg'; ;  
                         }
                         $totalimages[]="<div class='outer_storemap scrollFix'> <div class='inner_storemap'><img src='$imagepath'><span>".get_the_title()."</span><div class='bottom_search'>Close now<a>Contact</a></div></div></div>"; ?>
                      
                      <?php  $located = wc_locate_template('content-product.php');
                        if (file_exists($located)) {
                            include( $located );
                        }
                    }  
                    
                      $countnumber = sprintf("%02d", $numcount);
                    
                    
                    ?>
                  
<!--                <div class="myscript">
                    <script type="text/javascript">
                         jQuery(document).ready(function() {
                              var geocoder = new google.maps.Geocoder();
                              var addresses = <?php // echo json_encode($storeaddress); ?>;
                              var contants = <?php // echo json_encode($totalimages); ?>;
                              var abc = '<?php // echo $countnumber;?>'
                              

                             map = new google.maps.Map(document.getElementById('map'), {
                                 zoom: 14,
                                 scaleControl: true,
                                 scrollwheel: false,
                              });
                              
                              
                              
                              
                         var listener = google.maps.event.addListener(map, "idle", function() { 
                               if (map.getZoom() > 14) map.setZoom(14); 
                               
                               if(addresses.length>0)
                               {
                                    
                                    var centlat = "";
                                    var centlong = "";
                                    geocoder.geocode( { 'address': addresses[0]}, function(results, status) {
                                         map.setCenter(new google.maps.LatLng(results[0].geometry.location.lat(),results[0].geometry.location.lng()));
                                     });

                                    //map.setCenter(new google.maps.LatLng(centlat,centlong));
                               }
                         });
                        var infowindow = new google.maps.InfoWindow({
                                content: '',
                                closeBoxURL: "http://localhost/Themeforest/wp-content/uploads/2017/01/hp_1484042548.jpg",
                            });

                       for(var i = 0; i < addresses.length; i++){
                            //console.log(addresses[i]);
                            currAddress = addresses[i];
                            contant = contants[i]; 
                            var  number = abc++;
                            console.log('abc'+number);
//                            var num = i+1;
//                            number =   num.toString();
                            var icons =  "https://chart.googleapis.com/chart?chst=d_map_pin_letter&chld="+number+"|FF0000|000000";
                            geocodeAddress(currAddress, geocoder, map,contant,icons,number,infowindow);
                          }
                        });
                    </script>
                </div>-->
                    <?php
                    woocommerce_product_loop_end();
                }
                ?>

                <div class="after-shop-loop">
                    <?php
                    /**
                     * woocommerce_after_shop_loop hook
                     *
                     * @hooked woocommerce_pagination - 10
                     */
                    do_action('woocommerce_after_shop_loop');
                    ?>
                </div>
            <?php elseif (!woocommerce_product_subcategories(array('before' => woocommerce_product_loop_start(false), 'after' => woocommerce_product_loop_end(false)))) : ?>

                <?php wc_get_template('loop/no-products-found.php'); ?>

            <?php endif; ?>

        </div><!-- #content -->
        <?php
        /**
         * woocommerce_after_main_content hook
         *
         * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
         */
        do_action('woocommerce_after_main_content');
        ?>
    </div><!-- #primary -->
    <?php
    /**
     * woocommerce_sidebar hook
     *
     * @hooked woocommerce_get_sidebar - 10
     */
    if ($show_sidebar == 'right') {
        do_action('woocommerce_sidebar');
    } else {
        if (in_array('list', $additional_sidebar)) {
            get_sidebar('additional');
        }
    }
    ?>
</div>
    <?php get_footer('shop'); 
    ?>

