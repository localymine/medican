<?php
/*
Plugin Name: WaspThemes - Yellow Pencil Pro
Plugin URI: http://waspthemes.com/yellow-pencil
Description: Customize your WordPress site in minutes and keep the site design under your control with 100% front-end Style Editor. 
Version: 5.4.9
Author: WaspThemes
Author URI: http://www.waspthemes.com
*/


/* ---------------------------------------------------- */
/* Basic 												*/
/* ---------------------------------------------------- */
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}


/* ---------------------------------------------------- */
/* Check if lite version or not. 						*/
/* ---------------------------------------------------- */
if(strstr(__FILE__,"yellow-pencil-visual-theme-customizer")){
	$lite_dir = __FILE__;
	$pro_dir = str_replace("yellow-pencil-visual-theme-customizer", "waspthemes-yellow-pencil", __FILE__);
}else{
	$pro_dir = __FILE__;
	$lite_dir = str_replace("waspthemes-yellow-pencil", "yellow-pencil-visual-theme-customizer", __FILE__);
}

// Checking if files exists
$pro_exists = file_exists($pro_dir);
$lite_exists = file_exists($lite_dir);

// If pro version is there?
if($pro_exists == true && $lite_exists == true){

	// Be sure deactivate_plugins function is exists
	if(!function_exists("deactivate_plugins")){
		require_once(ABSPATH .'wp-admin/includes/plugin.php');
	}

	// deactivate Lite Version.
	deactivate_plugins(plugin_basename($lite_dir));

}

// Generate Base Editor URL.
function yp_uri(){
	if(current_user_can("edit_theme_options") == true){
		return admin_url('admin.php?page=yellow-pencil-editor');
	}elseif(defined('WT_DEMO_MODE')){
		return add_query_arg(array('yellow_pencil' => 'true'),get_home_url().'/');
	}
}


/* ---------------------------------------------------- */
/* Define 												*/
/* ---------------------------------------------------- */
define( 'WT_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'WT_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

define( 'YP_MODE', "min"); // min & dev.
define( 'YP_VERSION', "5.4.9");
define( 'WTFV', TRUE);



/* ---------------------------------------------------- */
/* Add animation ajax callback							*/
/* ---------------------------------------------------- */
function yp_add_animation(){

	if(current_user_can("edit_theme_options") == true){

		$css = wp_strip_all_tags($_POST['yp_anim_data']);
		$name = wp_strip_all_tags($_POST['yp_anim_name']);

		if(!update_option("yp_anim_".$name,$css)){
			add_option("yp_anim_".$name,$css);
		}

	}

}

add_action( 'wp_ajax_yp_add_animation', 'yp_add_animation' );




/* ---------------------------------------------------- */
/* Get Translation Text Domain							*/
/* ---------------------------------------------------- */
function yp_plugin_lang(){
	load_plugin_textdomain( 'yp', false, dirname(plugin_basename( __FILE__ )) . '/languages' ); 
}
add_action( 'plugins_loaded', 'yp_plugin_lang' );




/* ---------------------------------------------------- */
/* Add a customize link in wp plugins page				*/
/* ---------------------------------------------------- */
function yp_customize_link($links,$file){

    if($file == plugin_basename(dirname(__FILE__) . '/yellow-pencil.php')){
        $in = '<a style="color:rgb(64, 113, 64);" href="'.admin_url('themes.php?page=yellow-pencil').'">' . __('Customize','yp') . '</a>';
        array_unshift($links, $in);
    }
    return $links;
}

add_filter( 'plugin_action_links', 'yp_customize_link', 10, 2 );



/* ---------------------------------------------------- */
/* GET UPDATE API IF NEED   							*/
/* ---------------------------------------------------- */
$yp_part_of_theme = get_site_option( 'YP_PART_OF_THEME' );
if(defined("WTFV") == true && empty($yp_part_of_theme) == true){

	// Get Version
	function yp_version($v){
	    $v = preg_replace('/[^0-9]/s', '', $v);
	    if(strlen($v) == 2){
	        return $v."0";
	    }elseif(strlen($v) == 1){
	        return $v."00";
	    }else{
	        return $v;
	    }
	}

	// Need include plugin.php file
	if(!function_exists("get_plugin_data")){
		require_once(ABSPATH .'wp-admin/includes/plugin.php');
	}

	// Get Plugin Version
	$yp_plugin_data = get_plugin_data( __FILE__ );
	define("YP_PLUGIN_VERSION",yp_version($yp_plugin_data['Version']));

	// Include Update Api.
	require_once(WT_PLUGIN_DIR.'/library/php/update-api.php');
	
}



/* ---------------------------------------------------- */
/* Get Font Families   									*/
/* ---------------------------------------------------- */
function yp_load_fonts(){
	$css = yp_get_css(true);
	yp_get_font_families($css);
}



/* ---------------------------------------------------- */
/* Getting font Families By CSS OUTPUT					*/
/* ---------------------------------------------------- */
function yp_get_font_families($css){
	
	$protocol = is_ssl() ? 'https' : 'http';
	
	preg_match_all('/font-family:(.*?);/', $css, $r);

	foreach($r['1'] as &$k){
		$k = yp_font_name($k);
	}
	
	foreach(array_unique($r['1']) as $family){
		
		$id = str_replace("+", "-", strtolower($family));

		$id = str_replace("\\", "", $id);

		if($id == 'arial' || $id == 'helvetica' || $id == 'georgia' || $id == 'serif' || $id == 'helvetica-neue' || $id == 'times-new-roman' || $id == 'times' || $id == 'sans-serif' || $id == 'arial-black' || $id == 'gadget' || $id == 'impact' || $id == 'charcoal' || $id == 'tahoma' || $id == 'geneva' || $id == 'verdana' || $id == 'inherit'){
			return false;
		}

		if($id == '' || $id == ' '){
			return false;
		}

		// Getting fonts from google api.
		wp_enqueue_style($id, esc_url(''.$protocol.'://fonts.googleapis.com/css?family='.$family.':300,300italic,400,400italic,500,500italic,600,600italic,700,700italic'));	
		
	}
	
}



/* ---------------------------------------------------- */
/* Finding Font Names From CSS data     				*/
/* ---------------------------------------------------- */
function yp_font_name($k){
	
	$k = str_replace("font-family:","",$k);
	
	$k = str_replace('"',"",$k);
	$k = str_replace("'","",$k);
	
	$k = str_replace(" ","+",$k);
	
	$k = str_replace("+!important","",$k);
	
	$k = str_replace("!important","",$k);
	
	if(strstr($k,",")){
		$array = explode(",",$k);
		return $array[0];
	}else{
		return $k;
	}

}



/* ---------------------------------------------------- */
/* Checking current user can or not						*/
/* ---------------------------------------------------- */
function yp_check_let(){
	
	// If Demo Mode
	if(defined("WT_DEMO_MODE") == true && isset($_GET['yellow_pencil_frame']) == true){
		return true;
	}
	
	// If user can.
	if(current_user_can("edit_theme_options") == true){
		return true;
	}else{
		return false;
	}
	
}



/* ---------------------------------------------------- */
/* Checking current user can or not (FOR FRAME)			*/
/* ---------------------------------------------------- */
function yp_check_let_frame(){
	
	// If Demo Mode
	if(defined("WT_DEMO_MODE") == true && isset($_GET['yellow_pencil_frame']) == true){
		return true;
	}
	
	// Be sure, user can.
	if(current_user_can("edit_theme_options") == true && isset($_GET['yellow_pencil_frame']) == true){
		return true;
	}else{
		return false;
	}
	
}



/* ---------------------------------------------------- */
/* Getting Last Post Title 								*/
/* ---------------------------------------------------- */
function yp_getting_last_post_title(){
	$last = wp_get_recent_posts(array("numberposts" => 1,"post_status" => "publish"));

	if(isset($last['0']['ID'])){
		$last_id = $last['0']['ID'];
	}else{
		return false;
	}

	$title = get_the_title($last_id);

	if(strstr($title," ")){
		$words = explode(" ", $title);
		return $words[0];
	}else{
		return $title;
	}

}



/* ---------------------------------------------------- */
/* Clean protocol from URL 								*/
/* ---------------------------------------------------- */
function yp_urlencode($v){
	$v = explode("://",urldecode($v));
	return urlencode($v[1]);
}



/* ---------------------------------------------------- */
/* Register Admin Script								*/
/* ---------------------------------------------------- */
function yp_enqueue_admin_pages($hook){

	// Options page.
	if('settings_page_yp-options' == $hook){
		wp_enqueue_style('yellow-pencil-admin', plugins_url( 'css/options.css' , __FILE__ ));
	}
	
	// Post pages.
    if ( 'post.php' == $hook ) {
    	if(current_user_can("edit_theme_options") == true){
        	wp_enqueue_script('yellow-pencil-admin', plugins_url( 'js/admin.js' , __FILE__ ), 'jquery', '1.0', TRUE);
		}
    }

    // Admin css
    wp_enqueue_style('yellow-pencil-admin', plugins_url( 'css/admin.css' , __FILE__ ));
	
}

add_action( 'admin_enqueue_scripts', 'yp_enqueue_admin_pages' );




/* ---------------------------------------------------- */
/* Register Plugin Styles For Iframe					*/
/* ---------------------------------------------------- */
function yp_styles_frame() {
		
	$protocol = is_ssl() ? 'https' : 'http';

	// Google web fonts.
	wp_enqueue_style('yellow-pencil-font', ''.$protocol.'://fonts.googleapis.com/css?family=Open+Sans:400,600,800');	
	wp_enqueue_style('yellow-pencil-frame', plugins_url( 'css/frame.css' , __FILE__ ));
	
	// animate library.
	wp_enqueue_style('yellow-pencil-animate', plugins_url( 'library/css/animate.css' , __FILE__ ));
	
}




/* ---------------------------------------------------- */
/* Adding Link To Admin Appearance Menu					*/
/* ---------------------------------------------------- */
function yp_menu() {
	add_theme_page('Yellow Pencil Editor', 'Yellow Pencil Editor', 'edit_theme_options', 'yellow-pencil', 'yp_menu_function',999);
}



/* ---------------------------------------------------- */
/* Appearance page Loading And Location					*/
/* ---------------------------------------------------- */
function yp_menu_function(){

	$yellow_pencil_uri = yp_uri();
	
	// Background
	echo '<div class="yp-bg"></div>';
	
	// Loader
	echo '';
	
	// Background and loader CSS
	echo '<style>html,body{display:none;}</style>';
	
	// Location..
	echo '<script type="text/javascript">window.location = "'.add_query_arg(array('href' => yp_urlencode(get_home_url().'/')),$yellow_pencil_uri).'";</script>';
	
	// Die
	exit;
	
}

add_action('admin_menu', 'yp_menu');



/* ---------------------------------------------------- */
/* Sub string after 18chars								*/
/* ---------------------------------------------------- */
function yp_get_short_title($title){

	$title = ucfirst(strip_tags($title));

	if($title == ''){
		$title = 'Untitled';
	}

	if(strlen($title) > 18){
		return mb_substr($title,0,18,'UTF-8').'..';
	}else{
		return $title;
	}

}



/* ---------------------------------------------------- */
/* Getting All Title For Tooltip						*/
/* ---------------------------------------------------- */
function yp_get_long_tooltip_title($title){

	$title = ucfirst(strip_tags($title));

	if($title == '' || strlen($title) < 18){
		return false;
	}

	if(strlen($title) > 18){
		return $title;
	}

}



/* ---------------------------------------------------- */
/* Register Yellow Pencil Panel							*/
/* ---------------------------------------------------- */
function yp_yellow_penci_bar() {

	$yellow_pencil_uri = yp_uri();

	$liveLink = add_query_arg(array('yp_live_preview' => 'true'),esc_url($_GET['href']));

	if(isset($_GET['yp_id'])){
		$liveLink = add_query_arg(array('yp_id' => $_GET['yp_id']),esc_url($liveLink));
	}elseif(isset($_GET['yp_type'])){
		$liveLink = add_query_arg(array('yp_type' => $_GET['yp_type']),esc_url($liveLink));
	}

	$liveLink = str_replace("#038;yp_live_preview", "&amp;yp_live_preview", $liveLink);
	
    echo "<div class='yp-select-bar yp-disable-cancel'>
		<div class='yp-editor-top'>
		
			<a href='".esc_url($_GET['href'])."' class='wf-close-btn-link'><span data-toggle='tooltip' data-placement='left' title='".__('Close Editor','yp')."' class='dashicons dashicons-no-alt yp-close-btn'></span></a>

			<a class='yp-button yp-save-btn'>".__('Save','yp')."</a>

			<a data-toggle='tooltip' data-placement='bottom' title='".__('Reset Changes','yp')."' class='yp-button-reset'></a>

			<a target='_blank' data-href='".$liveLink."' data-toggle='tooltip' data-placement='bottom' title='".__('Live Preview','yp')."' class='yp-button-live'></a>
			
			<div class='yp-clearfix'></div>
			
		</div>";
		
		// Set variables.
		$tag_id = null;
		$category_id = null;
		$last_post_id = null;
		$last_portfolio_id = null;
		$last_page_id = null;
		
		// Getting tags
		$tags = get_tags(array('orderby' => 'count', 'order' => 'DESC','number'=> 1 ));
		if(empty($tags) == false){
			$tag_id = $tags[0];
		}
		
		// Getting categories
		$categories = get_categories(array('orderby' => 'count', 'order' => 'DESC','number'=> 1 ));
		if(empty($categories) == false){
			$category_id = $categories[0];
		}
		
		// Set null to variables.
		$category_page = '';
		$homepage = '';
		$global_current_page = '';
		$global_current_page_url = '';
		$tag_page = '';
		$is_type = '';
		$is_id = '';
		$all_singles = '';
		$all_pages = '';
		$gb_category_active = false;
		$gb_tag_active = false;
		$editingHas = '0';
		
		// Checking if its is a type
		if(isset($_GET['yp_type'])){
			$is_type = $_GET['yp_type'];
		}
		
		// Checking if its id.
		if(isset($_GET['yp_id'])){
			$is_id = $_GET['yp_id'];
		}
		
		// Getting current URL
		if(is_ssl()){
			$current_url = "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
		}else{
			$current_url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
		}
	
		// Category Page
		if($category_id != '' && $category_id != null){
			
			$url = add_query_arg(array('href' => yp_urlencode(get_term_link($category_id))),$yellow_pencil_uri);
			
			$active = '';
			if($current_url == $url){
				$active = ' class="active" ';
				$editingHas = '1';
				$gb_category_active = true;
			}
			
			$category_page = '<li'.$active.'><a href="'.esc_url($url).'">'.__("Category Page","yp").'</a></li>';
			
		}

		// if is global, try to add current page to global section.
		if(isset($_GET['yp_id']) == false && isset($_GET['yp_type']) == false){

			$postid = url_to_postid(esc_url($_GET['href']));

			if($postid != null){

				$url = add_query_arg(array('href' => yp_urlencode(get_permalink($postid))),$yellow_pencil_uri);

				$global_current_page = '<li class="active"><a data-toggle="tooltip" data-placement="left" title="'.yp_get_long_tooltip_title(get_the_title($postid)).'" href="'.esc_url($url).'">'.yp_get_short_title(get_the_title($postid)).'</a></li>';
				$global_current_page_url = esc_url($url);

			}else if($gb_category_active == false && $gb_tag_active == false){

				$global_current_page = '<li class="active"><a>Unknown Page</a></li>';
				$global_current_page_url = '';

			}

		}

		// tag Page
		if($tag_id != '' && $tag_id != null){
			
			$url = add_query_arg(array('href' => yp_urlencode(get_term_link($tag_id))),$yellow_pencil_uri);
			
			$active = '';
			if($current_url == $url){
				$active = ' class="active" ';
				$editingHas = '1';
				$gb_tag_active = true;
			}
			
			$tag_page = '<li'.$active.'><a href="'.esc_url($url).'">'.__("Tag Page","yp").'</a></li>';
			
		}
		
		// Home Page
		if($global_current_page_url != $url){
			$url = add_query_arg(array('href' => yp_urlencode(esc_url(get_home_url().'/'))),$yellow_pencil_uri);
				
			$active = '';
			if($current_url == $url){
				$active = ' class="active" ';
				$global_current_page = '';
				$editingHas = '1';
			}
				
			$homepage = '<li'.$active.'><a href="'.esc_url($url).'">'.__("Home","yp").'</a></li>';

		}

		
		// Getting pages with custom templates.
		$args = array(
			'posts_per_page' => 8,
			"post_status" => "publish",
		    'post_type' => 'page',
		    'meta_query' => array(
		        array(
		            'key' => '_wp_page_template',
		            'value' => 'default',
					'compare' => '!='
				)
			)
		);

		$other_pages = get_posts($args);
		$c = 1;
		$current_pages_id = array();
		foreach($other_pages as $page){

			$c++;

			array_push($current_pages_id, $page->ID);
			
			$url = add_query_arg(array('href' => yp_urlencode(get_permalink($page->ID)), 'yp_id' => $page->ID),$yellow_pencil_uri);
			
			$active = '';
			if($current_url == $url){
				$active = ' class="active" ';
				$editingHas = '1';
			}

			$title = $page->post_title;

			if($title == '' || $title == ' '){
				$title = 'Untitled';
			}
		
			$all_pages .= '<li'.$active.' id="page-'.esc_attr($page->ID).'-btn"><a data-toggle="tooltip" data-placement="left" title="'.yp_get_long_tooltip_title($title).'" href="'.esc_url($url).'">'.yp_get_short_title($title).'</a></li>';
			
		}

		// First get pages with templates,
		// if there not more 6 page templates,
		// so show normal pages.
		if($c < 6){
			// Getting all pages.
			$args = array(
				'posts_per_page' => (6-$c),
				"post_status" => "publish",
			    'post_type' => 'page',
			    'exclude' => $current_pages_id
			);

			$other_pages = get_posts($args);

			foreach($other_pages as $page){

				$url = add_query_arg(array('href' => yp_urlencode(get_permalink($page->ID)), 'yp_id' => $page->ID),$yellow_pencil_uri);
				
				$active = '';
				if($current_url == $url){
					$active = ' class="active" ';
					$editingHas = '1';
				}

				$title = $page->post_title;

				if($title == '' || $title == ' '){
					$title = 'Untitled';
				}
				
				$all_pages .= '<li'.$active.' id="page-'.esc_attr($page->ID).'-btn"><a data-toggle="tooltip" data-placement="left" title="'.yp_get_long_tooltip_title($title).'" href="'.esc_url($url).'">'.yp_get_short_title($title).'</a></li>';
				
			}

		}

		// Search Page.
		$url = add_query_arg(array('href' => yp_urlencode(esc_url(get_home_url()).'/?s='.yp_getting_last_post_title().'').'&yp_type=search'),$yellow_pencil_uri);
		$active = '';
		if($current_url == $url){
			$active = ' class="active" ';
			$editingHas = '1';
		}
		$all_singles .= '<li'.$active.' id="search-page-btn"><a href="'.esc_url($url).'">'.__("Search","yp").'</a></li>';

		// 404 Page.
		$url = add_query_arg(array('href' => yp_urlencode(esc_url(get_home_url()).'/?p=987654321').'&yp_type=404'),$yellow_pencil_uri);
		$active = '';
		if($current_url == $url){
			$active = ' class="active" ';
			$editingHas = '1';
		}
		$all_singles .= '<li'.$active.' id="error-page-btn"><a href="'.esc_url($url).'">404</a></li>';

		// tag Page.
		if($tag_id != '' && $tag_id != null){
			$url = add_query_arg(array('href' => yp_urlencode(esc_url(get_term_link($tag_id))).'&yp_type=tag'),$yellow_pencil_uri);
			$active = '';
			if($current_url == $url){
				$active = ' class="active" ';
				$editingHas = '1';
			}
			$all_singles .= '<li'.$active.' id="tag-page-btn"><a href="'.esc_url($url).'">'.__("Tag","yp").'</a></li>';
		}

		// Category Page.
		if($category_id != '' && $category_id != null){
			$url = add_query_arg(array('href' => yp_urlencode(esc_url(get_term_link($category_id))).'&yp_type=category'),$yellow_pencil_uri);
			$active = '';
			if($current_url == $url){
				$active = ' class="active" ';
				$editingHas = '1';
			}
			$all_singles .= '<li'.$active.' id="category-page-btn"><a href="'.esc_url($url).'">'.__("Category","yp").'</a></li>';
		}

		// Author Page.
		$url = add_query_arg(array('href' => yp_urlencode(esc_url(get_author_posts_url("1"))).'&yp_type=author'),$yellow_pencil_uri);
		$active = '';
		if($current_url == $url){
			$active = ' class="active" ';
			$editingHas = '1';
		}
		$all_singles .= '<li'.$active.' id="author-page-btn"><a href="'.esc_url($url).'">'.__("Author","yp").'</a></li>';


		// Home Page.
		$frontpage_id = get_option('page_on_front');
		if($frontpage_id == 0 || $frontpage_id == null){
			$url = add_query_arg(array('href' => yp_urlencode(esc_url(get_home_url().'/')).'&yp_type=home'),$yellow_pencil_uri);
			$active = '';
			if($current_url == $url){
				$active = ' class="active" ';
				$editingHas = '1';
			}
			$all_pages .= '<li'.$active.' id="page-page-home-btn"><a href="'.esc_url($url).'">'.__("Home Page","yp").'</a></li>';
		}


		$post_types = get_post_types(array(
		   'public'   => true,
		   '_builtin' => false
		));

		// Adding default post types.
		array_push($post_types, 'post');
		array_push($post_types, 'page');

		$pi = 0;
		foreach ($post_types as $post_type){

			$pi++;

				if($pi < 7){

				if($post_type == 'page'){
					$last_post = wp_get_recent_posts(array("post_status" => "publish","meta_key" => "_wp_page_template", "meta_value" => "default", "numberposts" => 1, "post_type" => $post_type));
				}else{
					$last_post = wp_get_recent_posts(array("post_status" => "publish","numberposts" => 1, "post_type" => $post_type));
				}

				if(empty($last_post) == false){

					$last_post_id = $last_post['0']['ID'];

					$url = add_query_arg(array('href' => yp_urlencode(get_permalink($last_post_id)), 'yp_type' => $post_type),$yellow_pencil_uri);

					$active = '';
					if(isset($_GET['yp_type'])){
						if($_GET['yp_type'] == $post_type){
						$active = ' class="active" ';
						$editingHas = '1';
						}
					}

					$all_singles .= '<li'.$active.' id="single-'.esc_attr($post_type).'-page-btn"><a href="'.esc_url($url).'">'.ucfirst($post_type).'</a></li>';

				}

			}

		}
		
		// Show editing page on all pages list.
		if(isset($_GET['yp_id'])){
			if($editingHas == '0'){
				$url = add_query_arg(array('href' => yp_urlencode(get_permalink($is_id)), 'yp_id' => $is_id),$yellow_pencil_uri);

				$title = get_the_title($is_id);

				if($title == '' || $title == ' '){
					$title = 'Untitled';
				}

				$all_pages .= '<li class="active" id="page-'.$is_id.'-btn"><a data-toggle="tooltip" data-placement="left" title="'.yp_get_long_tooltip_title($title).'" href="'.esc_url($url).'">'.yp_get_short_title($title).'</a></li>';
			}
		}elseif(isset($_GET['yp_type'])){
			if($editingHas == '0'){

				// Getting last post for current post type.
				if($is_type == 'page'){
					$last_post = wp_get_recent_posts(array("post_status" => "publish","meta_key" => "_wp_page_template", "meta_value" => "default", "numberposts" => 1, "post_type" => $is_type));
				}else{
					$last_post = wp_get_recent_posts(array("post_status" => "publish","numberposts" => 1, "post_type" => $is_type));
				}

				$last_post_id = $last_post['0']['ID'];

				$url = add_query_arg(array('href' => yp_urlencode(get_permalink($last_post_id)), 'yp_type' => $is_type),$yellow_pencil_uri);

				$title = $is_type;

				if($title == '' || $title == ' '){
					$title = 'Untitled';
				}

				$all_singles .= '<li class="active" id="page-'.$last_post_id.'-btn"><a data-toggle="tooltip" data-placement="left" title="'.yp_get_long_tooltip_title($title).'" href="'.esc_url($url).'">'.yp_get_short_title($title).'</a></li>';

			}
		}


		// Markup For Global Page Links etc.
		$other_pages = '<div class="yp-other-pages">
		<span data-toggle="popover" class="yp-start-info" title="'.__("Global Customize","yp").'" data-placement="left" data-content="'.__("Global changes will be loading on every page. Global customize ideal to edit 'Header', 'Footer', 'General Site Design' etc.","yp").'">'.__('Global Customize','yp').':</span>
		
		<ul class="yp-ul-global-list">'.$category_page.''.$homepage.''.$global_current_page.''.$tag_page.'</ul>';
		
		if($all_pages != '' && $all_pages != null){
		$other_pages .= '<span class="yp-other-other-pages yp-start-info" data-toggle="popover" title="'.__("Customize One Page","yp").'" data-placement="left" data-content="'.__("Use following links for apply changes to just one page.","yp").'">'.__('Customize One Page','yp').':</span>
		
		<ul class="yp-ul-all-pages-list">'.$all_pages.'</ul>'; }
		
		$other_pages .= '<span class="yp-start-info yp-other-other-pages" data-toggle="popover" title="'.__("Customize Templates","yp").'" data-placement="left" data-content="'.__("Use following links for edit Templates. Sample: 'all single posts', 'all product pages' etc.","yp").'">'.__('Customize Templates','yp').':</span>
		
		<ul class="yp-ul-single-list">'.$all_singles.'</ul></div>';
		

		// Default.
		echo '<div class="yp-no-selected"><div class="yp-hand"></div><div class="yp-hand-after"></div>'.__('Click on any element that you want to customize!','yp').' '.$other_pages.'<div class="yp-tip"><span class="dashicons dashicons-arrow-right"></span> '.__("Press to H key for hiding plugin panel.","yp").'</div></div>';
		

		// Options
		include( WT_PLUGIN_DIR . 'options.php' );
		
		
	echo "</div>";
	
}



/* ---------------------------------------------------- */
/* Getting Custom Animations Codes						*/
/* ---------------------------------------------------- */
function yp_get_custom_animations(){

	$all_options =  wp_load_alloptions();
	foreach($all_options as $name => $value){
		if(stristr($name, 'yp_anim')){

			// Get animations
			$value = stripslashes(yp_css_prefix($value));
    		$value = preg_replace('/\s+|\t/',' ',$value);

			echo "\n".'<style id="yp-animate-'.strtolower(str_replace("yp_anim_","",$name)).'">'."\n".''.$value."\n".str_replace("keyframes", "-webkit-keyframes", $value).''."\n".'</style>';

		}
	}

}



/* ---------------------------------------------------- */
/* Getting CSS Codes									*/
/* ---------------------------------------------------- */
/*
	yp_get_css(false) : echo output CSS
	yp_get_css(true) : return just CSS Codes.
*/
function yp_get_css($r = false){
	
	global $post;
	
	$return = '<style id="yellow-pencil">';
	$onlyCSS = '';
	
	$get_type_option = '';
	$get_post_meta = '';
	
	global $wp_query;
	if(isset($wp_query->queried_object)){
		$id = @$wp_query->queried_object->ID;
	}else{
		$id = null;
	}

	if(class_exists( 'WooCommerce')){
		if(is_shop()){
			$id = wc_get_page_id('shop');
		}
	}
	
	$get_option = get_option("wt_css");
	if($id != null){
		$get_type_option = get_option("wt_".get_post_type($id)."_css");
		$get_post_meta = get_post_meta($id, '_wt_css', true);
	}
	
	if($get_option == 'false'){
		$get_option = false;
	}
	
	if($get_type_option == 'false'){
		$get_type_option = false;
	}
	
	if($get_post_meta == 'false'){
		$get_post_meta = false;
	}
	
	if(empty($get_option) == false){
		$return .= "\r\n/* CSS Codes Generated By Yellow Pencil Editor */ \r\n".$get_option;
		$onlyCSS .= $get_option;
	}
	
	if(empty($get_type_option) == false){
		$return .= $get_type_option;
		$onlyCSS .= $get_type_option;
	}
	
	if(empty($get_post_meta) == false){
		$return .= $get_post_meta;
		$onlyCSS .= $get_post_meta;
	}

	if(is_author()){
		$return .= get_option("wt_author_css");
		$onlyCSS .= get_option("wt_author_css");
	}elseif(is_tag()){
		$return .= get_option("wt_tag_css");
		$onlyCSS .= get_option("wt_tag_css");
	}elseif(is_category()){
		$return .= get_option("wt_category_css");
		$onlyCSS .= get_option("wt_category_css");
	}elseif(is_404()){
		$return .= get_option("wt_404_css");
		$onlyCSS .= get_option("wt_404_css");
	}elseif(is_search()){
		$return .= get_option("wt_search_css");
		$onlyCSS .= get_option("wt_search_css");
	}

	// home.
	if(is_front_page() && is_home()){
		$return .= get_option("wt_home_css");
		$onlyCSS .= get_option("wt_home_css");
	}
	
	$return .= "\n".'</style>';
	
	
	if($return != "<style id=\"yellow-pencil\">\n</style>" && $r == false){
		echo stripslashes(yp_css_prefix(yp_animation_prefix(yp_hover_focus_match($return))));
	}
	
	if($r == true){
		return $onlyCSS;
	}
	
}



// Adding all CSS codes to WP Head if not live preview and editor page.
if(isset($_GET['yellow_pencil_frame']) == false && isset($_GET['yp_live_preview']) == false){
	add_action('wp_head','yp_get_css',9999);
}



// Adding all CSS animations to WP Head.
if(isset($_GET['yellow_pencil_frame']) == false){
	add_action('wp_head','yp_get_custom_animations',9999);
}



/* ---------------------------------------------------- */
/* Getting Live Preview CSS								*/
/* ---------------------------------------------------- */
function yp_get_live_css(){

	// Get recent generated CSS codes.
	$css = get_option('yp_live_view_css_data');

	if(empty($css)){
		return $css;
	}

	return stripslashes(yp_css_prefix(yp_animation_prefix(yp_hover_focus_match($css))));

}



/* ---------------------------------------------------- */
/* Getting fonts for live preview						*/
/* ---------------------------------------------------- */
function yp_load_fonts_for_live(){
	$css = yp_get_live_css();
	yp_get_font_families($css);
}



/* ---------------------------------------------------- */
/* Generating Live Preview data 						*/
/* ---------------------------------------------------- */
function yp_get_live_preview(){

	$css = yp_get_live_css();

	if(empty($css) == false){

		$css = '<style id="yp-live-preview">'.$css.'</style>';

		if($css != '<style id="yp-live-preview"></style>'){
			echo $css;
		}

	}

}



/* ---------------------------------------------------- */
/* Adding generated live preview CSS data To WP HEAD	*/
/* ---------------------------------------------------- */
if(isset($_GET['yp_live_preview']) == true){
	add_action('wp_head','yp_get_css_backend',9999);
	add_action('wp_head','yp_get_live_preview',9999);
}



/* ---------------------------------------------------- */
/* Hover/Focus System									*/
/* ---------------------------------------------------- */
/*
	Replace 'body.yp-selector-hover' to hover.
	replace 'body.yp-selector-focus' to focus.
*/
function yp_hover_focus_match($data){

	preg_match_all('@body.yp-selector-(.*?){@si',$data,$keys);
	
	foreach($keys[1] as $key){
		$dir = 'body.yp-selector-'.$key;

		
		$dirt = 'body.yp-selector-'.$key.':'.substr($key, 0, 5);		
		
		$dirt = str_replace('body.yp-selector-hover','body',$dirt);
		$dirt = str_replace('body.yp-selector-focus','body',$dirt);
		$data = (str_replace($dir,$dirt,$data));
	}
	
	$data = str_replace('.yp-selected','',$data);
	
	return $data;
	
}



/* ---------------------------------------------------- */
/* Adding Prefix To Some CSS Rules						*/
/* ---------------------------------------------------- */
function yp_css_prefix($outputCSS){
	
	$outputCSS = preg_replace('@-webkit-(.*?):(.*?);@si',"",$outputCSS);

	// Adding automatic prefix to output CSS.
	$CSSPrefix = array(
		"border-radius",
		"border-top-left-radius",
		"border-top-right-radius",
		"border-bottom-left-radius",
		"border-bottom-right-radius",
		"animation-fill-mode",
		"animation-duration",
		"animation-name",
		"filter",
		"box-shadow",
		"box-sizing",
		"transform",
		"transition"
	);
		
	foreach($CSSPrefix as $prefix){
		$outputCSS = preg_replace('@'.$prefix.':(.*?);@si',"".$prefix.":$1;\r	-moz-".$prefix.":$1;\r	-webkit-".$prefix.":$1;",$outputCSS);
	}
	
	return $outputCSS;
	
}



/* ---------------------------------------------------- */
/* Prefix for Animations								*/
/* ---------------------------------------------------- */
function yp_animation_prefix($outputCSS){
	
	$outputCSS = str_replace(".yp_focus:focus",":focus",$outputCSS);
	$outputCSS = str_replace(".yp_focus:hover",":focus",$outputCSS);
		
	$outputCSS = str_replace(".yp_hover:hover",":hover",$outputCSS);
	$outputCSS = str_replace(".yp_hover:focus",":hover",$outputCSS);
		
	$outputCSS = str_replace(".yp_onscreen:hover",".yp_onscreen",$outputCSS);
	$outputCSS = str_replace(".yp_onscreen:focus",".yp_onscreen",$outputCSS);
		
	$outputCSS = str_replace(".yp_click:hover",".yp_click",$outputCSS);
	$outputCSS = str_replace(".yp_click:focus",".yp_click",$outputCSS);
	
	$outputCSS = str_replace(".yp_hover",":hover",$outputCSS);
	$outputCSS = str_replace(".yp_focus",":focus",$outputCSS);
	
	return $outputCSS;
	
}



/* ---------------------------------------------------- */
/* Adding no-index meta to head for demo mode YP Links!	*/
/* ---------------------------------------------------- */
function yp_head_meta(){
	echo '<meta name="robots" content="noindex">' . "\n";
}



/* ---------------------------------------------------- */
/* Getting CSS data	for Backend							*/
/* ---------------------------------------------------- */
function yp_get_css_backend(){
	
	global $post;
	
	$get_type_option = '';
	$get_post_meta = '';
	
	global $wp_query;
	if(isset($wp_query->queried_object)){
		$id = @$wp_query->queried_object->ID;
	}else{
		$id = null;
	}
	
	$id_is = isset($_GET['yp_id']);
	$type_is = isset($_GET['yp_type']);
	
	$return = '<style>';
	
	$get_option = get_option("wt_css");
	if($id != null){
		$get_type_option = get_option("wt_".get_post_type($id)."_css");
		$get_post_meta = get_post_meta($id, '_wt_css', true);
	}
	
	if($get_option == 'false'){
		$get_option = false;
	}
	
	if($get_type_option == 'false'){
		$get_type_option = false;
	}
	
	if($get_post_meta == 'false'){
		$get_post_meta = false;
	}
	
	if(empty($get_option) == false){
		
		if($id_is == true || $type_is == true){
			$return .= $get_option;
		}
		
	}
	
	if(empty($get_type_option) == false){
		
		if($type_is == false){
			$return .= $get_type_option;
		}
		
	}
	
	if(empty($get_post_meta) == false){
		
		if($id_is == false){
			$return .= $get_post_meta;
		}
		
	}

	if($type_is == false){

		if(is_author()){
			$return .= get_option("wt_author_css");
		}elseif(is_tag()){
			$return .= get_option("wt_tag_css");
		}elseif(is_category()){
			$return .= get_option("wt_category_css");
		}elseif(is_404()){
			$return .= get_option("wt_404_css");
		}elseif(is_search()){
			$return .= get_option("wt_search_css");
		}

		// home.
		if(is_front_page() && is_home()){
			$return .= get_option("wt_home_css");
		}

	}
	
	$return .= '</style>';
	
	
	if($return != '<style></style>'){
		echo stripslashes($return);
	}
	
}



/* ---------------------------------------------------- */
/* Adding other CSS Data to Editor frame				*/
/* ---------------------------------------------------- */
if(isset($_GET['yellow_pencil_frame']) == true){
	add_action('wp_head','yp_get_css_backend',9998);
	add_action('wp_head','yp_head_meta');
}



/* ------------------------------------------------------------------- */
/* Other CSS Codes (All CSS Codes excluding current editing type CSS)  */
/* ------------------------------------------------------------------- */
function yp_editor_styles(){
		
	global $post;
	
	$get_type_option = '';
	$get_post_meta = '';
	
	global $wp_query;
	if(isset($wp_query->queried_object)){
		$id = @$wp_query->queried_object->ID;
	}else{
		$id = null;
	}
	
	$id_is = isset($_GET['yp_id']);
	$type_is = isset($_GET['yp_type']);
	
	$return = '<div class="yp-styles-area">';
	
	$get_option = get_option("wt_styles");
	if($id != null){
		$get_type_option = get_option("wt_".get_post_type($id)."_styles");
		$get_post_meta = get_post_meta($id, '_wt_styles', true);
	}
	
	if(empty($get_option) == false){
		
		if($id_is == false && $type_is == false){
			$return .= $get_option;
		}
		
	}
	
	if(empty($get_type_option) == false){
		
		if($type_is == true){
			$return .= $get_type_option;
		}
		
	}
	
	if(empty($get_post_meta) == false){
		
		if($id_is == true){
			$return .= $get_post_meta;
		}
		
	}

	if($type_is == true){

		$type = $_GET['yp_type'];

		if($type == 'author'){
			$return .= get_option("wt_author_styles");
		}

		if($type == 'tag'){
			$return .= get_option("wt_tag_styles");
		}

		if($type == 'category'){
			$return .= get_option("wt_category_styles");
		}

		if($type == '404'){
			$return .= get_option("wt_404_styles");
		}

		if($type == 'search'){
			$return .= get_option("wt_search_styles");
		}

		if($type == 'home'){
			$return .= get_option("wt_home_styles");
		}


	}

	$return .= '</div>';

	$animations = '';

	$all_options =  wp_load_alloptions();
	foreach($all_options as $name => $value){
		if(stristr($name, 'yp_anim')){
			$animations .= $value;
		}
	}

	$return .= '<div class="yp-animate-data"><style>'.$animations.'</style></div>';

	echo stripslashes($return);
	
}



/* ---------------------------------------------------- */
/* Adding styles to Editor 								*/
/* ---------------------------------------------------- */
if(isset($_GET['yellow_pencil_frame']) == true){
	add_action('wp_footer','yp_editor_styles');
}




/* ---------------------------------------------------- */
/* Include options Library								*/
/* ---------------------------------------------------- */
include_once( WT_PLUGIN_DIR . 'base.php' );




/*-------------------------------------------------------*/
/*	Ajax Preview Save CallBack							 */
/*-------------------------------------------------------*/
function yp_preview_data_save(){
	
	if(current_user_can("edit_theme_options") == true){

		$css = wp_strip_all_tags($_POST['yp_data']);

		if(!update_option('yp_live_view_css_data', $css)){
			add_option('yp_live_view_css_data',$css);
		}

	}
	
	die();
	
}

add_action( 'wp_ajax_yp_preview_data_save', 'yp_preview_data_save' );



/*-------------------------------------------------------*/
/*	Ajax Real Save Callback								 */
/*-------------------------------------------------------*/
function yp_ajax_save(){
	
	if(current_user_can("edit_theme_options") == true){

		$css = wp_strip_all_tags($_POST['yp_data']);
		
		$styles = $_POST['yp_editor_data'];

		$styles = str_replace("YP|@", "<", $styles);
		$styles = str_replace("YP@|", ">", $styles);
		
		$id = '';
		
		$type = '';
		
		if(isset($_POST['yp_id'])){
			$id = $_POST['yp_id'];
		}
		
		if(isset($_POST['yp_stype'])){
			$type = $_POST['yp_stype'];
			if(count(explode("#",$type)) == 2){
				$type = explode("#",$type);
				$type = $type[0];
			}
		}
		
		if($id == 'undefined'){$id = '';}
		if($type == 'undefined'){$type = '';}
		if($css == 'undefined'){$css = '';}
		
		if($id == '' && $type == ''){
			
			// CSS Data
			if(empty($css) == false){
				if(!update_option ('wt_css', $css)){
					add_option('wt_css',$css);
				}
			}else{
				delete_option('wt_css');
			}
			
			// Styles
			if(empty($css) == false){
				if(!update_option ('wt_styles', $styles)){
					add_option('wt_styles',$styles);
				}
			}else{
				delete_option('wt_styles');
			}
			
		}elseif($type == ''){
		
			// CSS Data
			if(empty($css) == false){
				if(!update_post_meta ($id, '_wt_css', $css)){
					add_post_meta($id,'_wt_css',$css, true);
				}
			}else{
				delete_post_meta($id,'_wt_css');
			}
			
			// Styles
			if(empty($css) == false){
				if(!update_post_meta ($id, '_wt_styles', $styles)){
					add_post_meta($id,'_wt_styles',$styles, true);
				}
			}else{
				delete_post_meta($id,'_wt_styles');
			}
			
		}else{

			// CSS Data
			if(empty($css) == false){
				if(!update_option ('wt_'.$type.'_css', $css)){
					add_option('wt_'.$type.'_css',$css);
				}
			}else{
				delete_option('wt_'.$type.'_css');
			}
			
			// Styles
			if(empty($css) == false){
				if(!update_option ('wt_'.$type.'_styles', $styles)){
					add_option('wt_'.$type.'_styles',$styles);
				}
			}else{
				delete_option('wt_'.$type.'_styles');
			}
			
		}
	
	}
	
	die();
	
}

add_action( 'wp_ajax_yp_ajax_save', 'yp_ajax_save' );



/* ---------------------------------------------------- */
/* Arrow icon Markup        							*/
/* ---------------------------------------------------- */
function yp_arrow_icon(){
	return "<span class='dashicons yp-arrow-icon dashicons-arrow-up'></span><span class='dashicons yp-arrow-icon dashicons-arrow-down'></span>";
}



/* ---------------------------------------------------- */
/* Getting current theme/page name                      */
/* ---------------------------------------------------- */
function yp_customizer_name(){
	
	if(isset($_GET['yp_id']) == true){
		
		// The id.
		$id = $_GET['yp_id'];
		
		$title = get_the_title($id);
		$slug = ucfirst(get_post_type($id));
		
		if(strlen($title) > 14){

			return '"'.mb_substr($title,0,14,'UTF-8').'..'.'" '.$slug.'';
		}else{
			if($title == ''){
				$title = 'Untitled';
			}
			return '"'.$title.'" '.$slug.'';
		}
		
	}elseif(isset($_GET['yp_type']) == true){
		
		// The id.
		$type = ucfirst($_GET['yp_type']);
		
		if($type == 'Page' || $type == 'Author' || $type == 'Search' || $type == '404' || $type == 'Category'){
			$title = ''.$type.' '.__("Template","yp").'';
		}else{
			$title = ''.__("Single","yp").' '.$type.' '.__("Template","yp").'';
		}

		if($type == 'Home'){
			$title = __('Home Page','yp');
		}

		if($type == 'Page'){
			$title = __('Default Page Template','yp');
		}
		
		return $title;
		
	}else{
		
		$yp_theme = wp_get_theme();

		// Replace 'theme' word from theme name.
		$name = str_replace(' theme', '', $yp_theme->get('Name'));
		$name = str_replace(' Theme', '', $name);
		$name = str_replace('theme', '', $name);
		$name = str_replace('Theme', '', $name);

		// Keep it short.
		if(strlen($name) > 10){
			return '"'.mb_substr($name,0,10,'UTF-8').'.." '.__("Theme",'yp').' (Global)';
		}else{
			if($name == ''){
				$name = __('Untitled','yp');
			}
			return '"'.$name.'" '.__("Theme",'yp').'  (Global)';
		}
		
	}
	
}



/* ---------------------------------------------------- */
/* Adding helper style for wp-admin-bar					*/
/* ---------------------------------------------------- */
function yp_yellow_pencil_style() {
  echo '<style>#wp-admin-bar-yellow-pencil > .ab-item:before{content: "\f309";top:2px;}#wp-admin-bar-yp-update .ab-item:before{content: "\f316";top:3px;}</style>';
}



/* ---------------------------------------------------- */
/* Adding menu to wp-admin-bar							*/
/* ---------------------------------------------------- */
function yp_yellow_pencil_edit_admin_bar( $wp_admin_bar ){
	
	$id = null;
	global $wp_query;
	$yellow_pencil_uri = yp_uri();
	
	if(isset($_GET['page_id'])){
		$id = $_GET['page_id'];
	}elseif(isset($_GET['post']) && is_admin() == true){
		$id = $_GET['post'];
	}elseif(isset($wp_query->queried_object) == true){
		$id = @$wp_query->queried_object->ID;
	}
	
	$args = array(
		'id'    => 'yellow-pencil',
		'title' => __('Edit With Yellow Pencil','yp'),
		'href'  => '',
		'meta'  => array( 'class' => 'yellow-pencil' )
	);
	$wp_admin_bar->add_node( $args );

	$args = array();

	// Since 4.5.2
	// category,author,tag, 404 and archive page support.
	$status = get_post_type($id);
	$key = get_post_type($id);
	$go_link = get_permalink($id);

	if(is_author()){
		$status = __('Author','yp');
		$key = 'author';
		$id = $wp_query->query_vars['author'];
		$go_link = get_author_posts_url($id);
	}elseif(is_tag()){
		$status = __('Tag','yp');
		$key = 'tag';
		$id = $wp_query->query_vars['tag_id'];
		$go_link = get_tag_link($id);
	}elseif(is_category()){
		$status = __('Category','yp');
		$key = 'category';
		$id = $wp_query->query_vars['cat'];
		$go_link = get_category_link($id);
	}elseif(is_404()){
		$status = '404';
		$key = '404';
		$go_link = esc_url(get_home_url().'/?p=987654321');
	}elseif(is_archive()){
		$status = __('Archive','yp');
		$key = 'archive';
	}elseif(is_search()){
		$status = __('Search','yp');
		$key = 'search';
		$go_link = esc_url(get_home_url().'/?s='.yp_getting_last_post_title().'');
	}

	// Blog
	if(is_front_page() && is_home()){
		$status = __('Home Page','yp');
		$key = 'home';
		$go_link = esc_url(get_home_url().'/');
	}elseif ( is_front_page() == false && is_home() == true ) {
		$status = __('Page','yp');
	}

	if(class_exists( 'WooCommerce' )){
		if(is_shop()){
			$id = wc_get_page_id('shop');
			$status = __('Page','yp');
			$key = 'page';
			$go_link = esc_url(get_permalink($id));
		}
	}

	if($go_link == ''){
		global $wp;
		$key = '';
		$go_link = add_query_arg($wp->query_string, '', home_url( $wp->request ));
	}

	// null if zero.
	if($id == 0){
		$id = null;
	}

	// Edit theme
	array_push($args,array(
		'id'		=>	'yp-edit-theme',
		'title'		=>	__('Global Customize','yp'),
		'href'		=>	add_query_arg(array('href' => yp_urlencode($go_link)),$yellow_pencil_uri),
		'parent'	=>	'yellow-pencil',
	));

	// Edit All similar
	if($key != 'home' && $key != 'archive' && $key != ''){

		if($key != '404' && $key != 'search'){
			$s = '\'s';
			$all = 'All ';
		}else{
			$s = '';
			$all = '';
		}

		array_push($args,array(
			'id'     	=> 'yp-edit-all',
			'title'		=>	''.__("Edit",'yp').' '.ucfirst($status).' '.__("Template",'yp').'',
			'href'		=>	add_query_arg(array('href' => yp_urlencode($go_link), 'yp_type' => $key),$yellow_pencil_uri),
			'parent' 	=> 'yellow-pencil',
			'meta'   	=> array( 'class' => 'first-toolbar-group' ),
		));

	}
	
	// Edit it.
	if($key != 'search' && $key != 'archive' && $key != 'tag' && $key != 'category' && $key != 'author' && $key != '404' && $key != ''){
		
		if($key == 'home'){

			array_push($args,array(
				'id'		=>	'yp-edit-it',
				'title'		=>	''.__("Edit Only","yp").' '.ucfirst($status).'',
				'href'		=>	add_query_arg(array('href' => yp_urlencode($go_link), 'yp_type' =>  $key),$yellow_pencil_uri),
				'parent'	=>	'yellow-pencil',
			));
		}else{
			
			array_push($args,array(
				'id'		=>	'yp-edit-it',
				'title'		=>	''.__("Edit This",'yp').' '.ucfirst($status).'',
				'href'		=>	add_query_arg(array('href' => yp_urlencode($go_link), 'yp_id' =>  $id),$yellow_pencil_uri),
				'parent'	=>	'yellow-pencil',
			));

		}

		
	}
		
	// Add to Wp Admin Bar
	for($a = 0;$a < sizeOf($args); $a++){
		$wp_admin_bar->add_node($args[$a]);
	}
	

}



/* ---------------------------------------------------- */
/* Adding Body Classes									*/
/* ---------------------------------------------------- */
function yp_body_class($classes) {
	
	$classes[] = 'yp-yellow-pencil wt-yellow-pencil';

	if(current_user_can("edit_theme_options") == false){
		if(defined("WT_DEMO_MODE")){
			$classes[] = 'yp-yellow-pencil-demo-mode';
		}
	}
	
	if(defined("WT_DISABLE_LINKS")){
		$classes[] = 'yp-yellow-pencil-disable-links';
	}

	if(!defined('WTFV')){
		$classes[] = 'wtfv';
	}

	return $classes;
	
}



/* ---------------------------------------------------- */
/* Install the plugin									*/
/* ---------------------------------------------------- */
function yp_init(){
	

	// See Developer Documentation for more info.
	if(defined("WT_DEMO_MODE")){
		include( WT_PLUGIN_DIR . 'demo_mode.php' );
	}
	

	// Iframe Settings.
	// Disable admin bar in iframe
	// Add Classes to iframe body.
	// Add Styles for iframe.
	if(yp_check_let_frame()){
		show_admin_bar(false);
		add_filter('body_class', 'yp_body_class');
		add_action( 'wp_enqueue_scripts', 'yp_styles_frame' );
	}
	

	// If yellow pencil is active and theme support;
	// Adding Link to #wpadminbar.
	if(yp_check_let()){

		// If not admin page, Add Customizer link.
		if(is_admin() === false){

			add_action( 'admin_bar_menu', 'yp_yellow_pencil_edit_admin_bar', 999 );

			// Adding CSS helper for admin bar link.
			add_action('wp_head', 'yp_yellow_pencil_style');

		}

	}

	
	// Getting Current font families.
	if(is_admin() === false){
		add_action('wp_enqueue_scripts','yp_load_fonts');
	}


	// Live preview
	if(isset($_GET['yp_live_preview']) == true){
		add_action('wp_enqueue_scripts','yp_load_fonts_for_live');
	}


}

add_action("init","yp_init");




/* ---------------------------------------------------- */
/* Uploader Style 										*/
/* ---------------------------------------------------- */
function yp_uploader_style(){

	echo '<style>
		tr.url,tr.post_content,tr.post_excerpt,tr.field,tr.label,tr.align,tr.image-size,tr.post_title,tr.image_alt,.del-link,#tab-type_url{display:none !important;}
		.media-item-info > tr > td > p:last-child,.savebutton,.ml-submit{display:none !important;}
		#filter{display:none !important;}
		.media-item .describe input[type="text"], .media-item .describe textarea{width:334px;}
		div#media-upload-header{
		}
	</style>';

}



/* ---------------------------------------------------- */
/* Add action to Admin Head for Uploader Style			*/
/* ---------------------------------------------------- */
if(isset($_GET['yp_uploader'])){
	if($_GET['yp_uploader'] == 1){
		add_action('admin_head','yp_uploader_style');
	}
}



/* ---------------------------------------------------- */
/* CSS library for Yellow Pencil						*/
/* ---------------------------------------------------- */
function yp_register_styles() {

	// Animate library.
	if(strstr(yp_get_css(true),"animation-name:")){
		wp_enqueue_style('yellow-pencil-animate', plugins_url( 'library/css/animate.css' , __FILE__ ));
	}

	// Animate library for live preview
	if(isset($_GET['yp_live_preview']) == true){

		$css = yp_get_live_css();

		if(strstr($css,"animation-name:")){
			wp_enqueue_style('yellow-pencil-animate', plugins_url( 'library/css/animate.css' , __FILE__ ));
		}

	}
	
}



/* ---------------------------------------------------- */
/* Jquery plugins for CSS Engine						*/
/* ---------------------------------------------------- */
function yp_register_scripts() {
	
	$outputCSS = yp_get_css(true);
	$needCSSEngine = false;
	
	// Yellow Pencil Library Helper.
	if(strstr($outputCSS,"animation-name:") == true || isset($_GET['yellow_pencil_frame']) == true || isset($_GET['yp_live_preview']) == true){
		wp_enqueue_script('yellow-pencil-library', plugins_url( 'library/js/yellow-pencil-library.js' , __FILE__ ), 'jquery', '1.0', TRUE);
		$needCSSEngine = true;
	}
	
	// Background Parallax
	if(strstr($outputCSS,"background-parallax:") == true || isset($_GET['yellow_pencil_frame']) == true || isset($_GET['yp_live_preview']) == true){
		wp_enqueue_script('yellow-pencil-background-parallax', plugins_url( 'library/js/parallax.js' , __FILE__ ), 'jquery', '1.0', TRUE);
		$needCSSEngine = true;
	}
	
	// CSS Engine for special CSS rules.
	// example: my-css-rule:data("value");
	if($needCSSEngine == true){
		wp_enqueue_script('yellow-pencil-css-engine', plugins_url( 'library/js/css-engine.js' , __FILE__ ), 'jquery', '1.0', TRUE);
	}
	
	// Jquery
	if($needCSSEngine == true){
		wp_enqueue_script( 'jquery' );
	}
	
}

add_action( 'wp_enqueue_scripts', 'yp_register_styles' );
add_action( 'wp_enqueue_scripts', 'yp_register_scripts' );




/* ---------------------------------------------------- */
/* 50 Scripts Area for Parallax Option					*/
/* ---------------------------------------------------- */
function yp_scripts_areas() {
    
	if(isset($_GET['yellow_pencil_frame']) == true){
		
		// script area enough for yellow pencil.
		for ($i = 1; $i <= 50; $i++) {
			echo "<script class='yellow-pencil-scripts'></script>\r";
		}
		
	}

}
add_action( 'wp_footer', 'yp_scripts_areas', 9999);



/* ---------------------------------------------------- */
/* Iframe Admin Page									*/
/* ---------------------------------------------------- */
function yp_yellow_pencil_editor() {

    $hook = add_submenu_page(null, __('Yellow Pencil Editor','yp'), __('Yellow Pencil Editor','yp'), 'edit_theme_options', 'yellow-pencil-editor','yp_editor_func');

}

add_action('admin_menu', 'yp_yellow_pencil_editor');



/* ---------------------------------------------------- */
/*  We need an blank page (hack)						*/
/* ---------------------------------------------------- */
function yp_editor_func(){
	
}

add_action('load-admin_page_yellow-pencil-editor', 'yp_frame_output');



/* ---------------------------------------------------- */
/* Custom Action yp_head 								*/
/* ---------------------------------------------------- */
function yp_head() {
    do_action('yp_head');
}



/* ---------------------------------------------------- */
/* Custom Action yp_footer 								*/
/* ---------------------------------------------------- */
function yp_footer() {
    do_action('yp_footer');
}



/* ---------------------------------------------------- */
/* Editor Page Markup 									*/
/* ---------------------------------------------------- */
function yp_frame_output(){

$protocol = is_ssl() ? 'https' : 'http';

$protocol = $protocol.'://';

// Editor Markup
include( WT_PLUGIN_DIR . 'editor.php' );

exit;

}



/* ---------------------------------------------------- */
/* Adding link to plugins page 							*/
/* ---------------------------------------------------- */
if(!defined('WTFV')){

	add_filter('plugin_row_meta', 'yp_plugin_links', 10, 2);

	function yp_plugin_links($links, $file){

		if ( $file == plugin_basename(dirname(__FILE__).'/yellow-pencil.php') ) {
			$links[] = '<a href="http://waspthemes.com/yellow-pencil/buy">' . __('Get Premium', 'yp') . '</a>';
		}

		return $links;

	}

}


/* ---------------------------------------------------- */
/* Ading Prefix to CSS selectors for global export		*/
/* ---------------------------------------------------- */
function yp_add_prefix_to_css_selectors($css,$prefix){

    # Wipe all block comments
    $css = preg_replace('!/\*.*?\*/!s', '', $css);

    $parts = explode('}', $css);
    $mediaQueryStarted = false;

    foreach($parts as &$part){
        $part = trim($part); # Wht not trim immediately .. ?

        if(empty($part)){
        	continue;
        }else{ # This else is also required
        
            $partDetails = explode('{', $part);

            if(substr_count($part, "{") == 2){
                $mediaQuery = $partDetails[0]."{";
                $partDetails[0] = $partDetails[1];
                $mediaQueryStarted = true;
            }

            $subParts = explode(',', $partDetails[0]);

            foreach($subParts as &$subPart){
                if(trim($subPart) === "@font-face"){
                	continue;
                }else{

                	// Selector
                	$subPart = trim($subPart);

                	// Array
                	$subPartArray = explode(" ", $subPart);

                	// not HTML.
                	if(strtolower($subPart) != 'html'){
                	
		                // Get index of "body" term.
		                $i = 0;
		                $index = 0;
		                foreach($subPartArray as $term){
							if(preg_match("/\bbody/i", $term)){
								$index = $i;
								break;
							}
							$i++;
						}

		                // Adding prefix class to Body
		                $subPartArray[$index] = $subPartArray[$index] . $prefix;

		                // Update Selector
		                $subPart = implode(" ",$subPartArray);

                	}

                }
            }

            if(substr_count($part,"{") == 2){
                $part = $mediaQuery."\n".implode(', ', $subParts)."{".$partDetails[2];
            }elseif(empty($part[0]) && $mediaQueryStarted){
                $mediaQueryStarted = false;
                $part = implode(', ', $subParts)."{".$partDetails[2]."}\n"; //finish media query
            }else{
                if(isset($partDetails[1])){
                	# Sometimes, without this check,
                    # there is an error-notice, we don't need that..
                    $part = implode(', ', $subParts)."{".$partDetails[1];
                }
            }

            unset($partDetails, $mediaQuery, $subParts); # Kill those three..

        }
        unset($part); # Kill this one as well
    }

    // Delete spaces
    $output = preg_replace('/\s+/',' ',implode("} ", $parts));

    // Delete all other spaces
    $output = str_replace("{ ", "{", $output);
    $output = str_replace(" {", "{", $output);
    $output = str_replace("} ", "}", $output);
    $output = str_replace("; ", ";", $output);

    // Beatifull >
    $output = str_replace("{", "{\n\t", $output);
    $output = str_replace("}", "\n}\n\n", $output);
    $output = str_replace("}\n\n\n", "}\n\n", $output);
    $output = str_replace("){", "){\n", $output);
    $output = str_replace(";", ";\n\t", $output);
    $output = str_replace("\t\n}", "}", $output);
    $output = str_replace("}\n\n}", "\t}\n\n}\n\n", $output);
    

    # Finish with the whole new prefixed string/file in one line
    return($output);

}



/* ---------------------------------------------------- */
/* Shhh! Thats base(64) functions						*/
/* ---------------------------------------------------- */
function yp_encode( $value ) {
  $func = 'base64' . '_encode';
  return $func( $value );
}

function yp_decode( $value ) {
  $func = 'base64' . '_decode';
  return $func( $value );
}



/* ---------------------------------------------------- */
/* Getting All plugin options by prefix					*/
/* ---------------------------------------------------- */
function yp_get_all_options($prefix = '', $en = false){

	global $wpdb;
	$ret = array();
	$options = $wpdb->get_results($wpdb->prepare("SELECT option_name,option_value FROM {$wpdb->options} WHERE option_name LIKE %s",$prefix.'%'),ARRAY_A);

	if(!empty($options)){
		foreach ($options as $v) {
			if(strstr($v['option_name'],'wt_theme') == false && strstr($v['option_name'],'wt_available_version') == false && strstr($v['option_name'],'wt_last_check_version') == false){
				if($en == true){
					$ret[$v['option_name']] = yp_encode(stripslashes($v['option_value']));
				}else{
					$ret[$v['option_name']] = stripslashes($v['option_value']);
				}
			}
		}
	}

	return (!empty($ret)) ? $ret : false;

}



/* ---------------------------------------------------- */
/* Getting All post meta data by prefix					*/
/* ---------------------------------------------------- */
function yp_get_all_post_options($prefix = '', $en = false){

	global $wpdb;
	$ret = array();
	$options = $wpdb->get_results($wpdb->prepare("SELECT post_id,meta_key,meta_value FROM {$wpdb->postmeta} WHERE meta_key LIKE %s",$prefix.'%'),ARRAY_A);

	if(!empty($options)){
		foreach ($options as $v) {
			if($en == true){
				$ret[$v['post_id'].".".$v['meta_key']] = yp_encode(stripslashes($v['meta_value']));
			}else{
				$ret[$v['post_id'].".".$v['meta_key']] = stripslashes($v['meta_value']);
			}
	}
	}

	return (!empty($ret)) ? $ret : false;

}



/* ---------------------------------------------------- */
/* Creating a json data for export data					*/
/* ---------------------------------------------------- */
function yp_get_export_data(){

	$allData = array();
	$postmeta_CSS = yp_get_all_post_options('_wt_css',true);
	$postmeta_HTML = yp_get_all_post_options('_wt_styles',true);
	$option_Data = yp_get_all_options('wt_',true);
	$option_Anims = yp_get_all_options('yp_anim',true);

	if(is_array($postmeta_CSS)){
		array_push($allData,$postmeta_CSS);
	}

	if(is_array($postmeta_HTML)){
		array_push($allData,$postmeta_HTML);
	}

	if(is_array($option_Data)){
		array_push($allData,$option_Data);
	}

	if(is_array($option_Anims)){
		array_push($allData,$option_Anims);
	}

	if(empty($allData) == false){
		$data = array_values($allData);	
		$jsonData = json_encode($data);
		return $jsonData;
	}

	return false;

}



/* ---------------------------------------------------- */
/* Generate All CSS styles as ready-to-use				*/
/* ---------------------------------------------------- */
function yp_get_export_css(){

	$allData = array();
	$postmeta_CSS = yp_get_all_post_options('_wt_css',false);
	$option_Data = yp_get_all_options('wt_',false);
	$option_Anims = yp_get_all_options('yp_anim',false);

	if(is_array($postmeta_CSS)){
		array_push($allData,$postmeta_CSS);
	}

	if(is_array($option_Data)){
		array_push($allData,$option_Data);
	}

	if(is_array($option_Anims)){
		array_push($allData,$option_Anims);
	}

	if(empty($allData) == false){
		$data = array_values($allData);

		$output = null;
		$table = array();
		$tableIndex = 0;
		$prefix = '';

		foreach ($data as $nodes){

			foreach ($nodes as $key => $css){
			$tableIndex++;

				// If post meta
				if(strstr($key, '._')){

					$keyArray = explode(".", $key);
					$postID = $keyArray[0];
					$type = get_post_type($postID);
					$title = '"'.ucfirst(get_the_title($postID)).'" '.ucfirst($type).' Style';

					if($type == 'page'){
						$prefix = '.page-id-'.$postID.'';
					}else{
						$prefix = '.postid-'.$postID.'';
					}

				}else{

					if($key == 'wt_css'){
						$title = 'Global Styles';
						$prefix = '';
					}else if($key == 'wt_author_css'){
						$title = 'Author Page Styles';
						$prefix = '.author';
					}else if($key == 'wt_category_css'){
						$title = 'Category Page Styles';
						$prefix = '.category';
					}else if($key == 'wt_tag_css'){
						$title = 'Tag Page Styles';
						$prefix = '.tag';
					}else if($key == 'wt_404_css'){
						$title = '404 Error Page Styles';
						$prefix = '.error404';
					}else if($key == 'wt_search_css'){
						$title = 'Search Page Styles';
						$prefix = '.search';
					}else if($key == 'wt_home_css'){
						$title = 'Home Page Styles';
						$prefix = '.home';
					}

					else if(strstr($key, 'yp_anim')){
						$title = str_replace("yp_anim_", "", $key);
						$title = $title." Animate";
					}else if(strstr($key, 'wt_') && strstr($key, '_css')){
						$title = str_replace("wt_", "", $key);
						$title = str_replace("_css", "", $title);

						if(strtolower($title) == 'page'){
							$prefix = '.page';
						}else{
							$prefix = '.single-'.strtolower($title).'';
						}

						$title = $title." Template Style";
					}

				}

				if(!strstr($key, '_styles')){
					$len = 48-(strlen($title)+2);
					$extra = null;

					for ($i=1; $i < $len; $i++){
						$extra .= ' ';
					}

					array_push($table, ucfirst($title));
					$output .= "/*-----------------------------------------------*/\r\n";
					$output .= "/*  ".ucfirst($title)."".$extra."*/\r\n";
					$output .= "/*-----------------------------------------------*/\r\n";
					$output .= yp_add_prefix_to_css_selectors($css,$prefix)."\r\n\r\n\r\n";

				}

			}
		}

	}

	$tableList = null;
	foreach ($table as $key => $value) {
		$tableList .= "    ".sprintf("%02d", $key+1).". ".$value."\r\n";
	}


	$allOutPut = "/*\r\n\r\n    All these CSS codes generated by Yellow Pencil Editor.\r\n";
	$allOutPut .= "    http://waspthemes.com/yellow-pencil\r\n\r\n\r\n";
	$allOutPut .= "    T A B L E   O F   C O N T E N T S\r\n";
	$allOutPut .= "    ........................................................................\r\n\r\n";
	$allOutPut .= $tableList;
	$allOutPut .= "\r\n*/\r\n\r\n\r\n";
	$allOutPut .= $output;

	return $allOutPut;

}



/* ---------------------------------------------------- */
/* Import Plugin data                					*/
/* ---------------------------------------------------- */
function yp_import_data($json){

	$json = stripslashes($json);

	if(empty($json)){
		return false;
	}

	$array = json_decode($json,true);

	foreach ($array as $nodes){

		foreach ($nodes as $key => $value){

			$value = yp_decode($value);

			// If post meta
			if(strstr($key, '._')){

				$keyArray = explode(".", $key);
				$postID = $keyArray[0];
				$metaKey = $keyArray[1];

				if(!add_post_meta($postID, $metaKey,$value,true)){
				   update_post_meta($postID,$metaKey,$value);
				}

			}else{ // else option
				if(!update_option($key,$value)){
					add_option($key,$value);
				}
			}

		}

	}

}



/* ---------------------------------------------------- */
/* Adding Yellow Pencil Source Page 	 				*/
/* ---------------------------------------------------- */
add_action('admin_menu', 'register_yp_source_page');

function register_yp_source_page() {
	add_submenu_page( 'options-general.php', __('Yellow Pencil Source','yp'), __('Yellow Pencil Source','yp'), 'edit_theme_options', 'yp-options', 'yp_options' );
}



/* ---------------------------------------------------- */
/* Export CSS as style.css 	 							*/
/* ---------------------------------------------------- */
if(isset($_GET['yp_exportCSS'])){

	if($_GET['yp_exportCSS'] == 'true'){

	$data = yp_get_export_css();

	header('Content-Disposition: attachment; filename="style-'.strtolower(date("M-d")).'.css"');
	header("Content-type: text/css; charset: UTF-8");
	header('Content-Length: ' . strlen($data));
	header('Connection: close');

	echo $data;

	die();

	}

}



/* ---------------------------------------------------- */
/* YP Source Page 	 									*/
/* ---------------------------------------------------- */
function yp_options() {

	// Can?
	if(current_user_can("edit_theme_options") == true){

		// Reset global data.
		if(isset($_GET['yp_reset_global'])){
			delete_option('wt_css');
			delete_option('wt_styles');
		}

		if(isset($_GET['yp_delete_animate'])){
			delete_option($_GET['yp_delete_animate']);
		}

		// Reset Post type.
		if(isset($_GET['yp_reset_type'])){
			delete_option('wt_'.$_GET['yp_reset_type'].'_css');
			delete_option('wt_'.$_GET['yp_reset_type'].'_styles');
		}

		// Reset by id.
		if(isset($_GET['yp_reset_id'])){
			delete_post_meta($_GET['yp_reset_id'],'_wt_css');
			delete_post_meta($_GET['yp_reset_id'],'_wt_styles');
		}

		// Updated.
		if(isset($_GET['yp_reset_global']) || isset($_GET['yp_reset_id']) || isset($_GET['yp_reset_type']) || isset($_GET['yp_delete_animate'])){
			echo "<script type='text/javascript'>window.location = '".admin_url('options-general.php?page=yp-options&yp_updated=true')."';</script>";
		}

		// Import the data
		if(isset($_POST['yp_json_import_data'])){

			$data = $_POST['yp_json_import_data'];

			if(empty($data) == false){
				yp_import_data($data);
			}

		}

	}

	// Updated message.
	if(isset($_GET['yp_updated'])){
		?>
			<div id="message" class="updated">
		        <p><strong><?php _e('Settings saved.') ?></strong></p>
		    </div>
		<?php
	}

	?>

	<div class="wrap">
	 
		<h2>Yellow Pencil CSS Source</h2>

		<p><?php _e('You will see all customized pages at below. You can delete and customize to them.','yp'); ?></p>

		<div class="yp-code-group">

		<ul>

			<?php $count = 0; if(get_option("wt_css") != ''){ $count = 1; ?>
				<li>
						<span class="yp-title"><?php _e('Global','yp'); ?></span>
						<a class="yp-remove" onclick="return confirm('<?php _e("Are you sure?","yp"); ?>')" href="<?php echo admin_url('options-general.php?page=yp-options&yp_reset_global=true'); ?>"><span class="dashicons dashicons-no"></span> <?php _e('Delete','yp'); ?></a>

						<a class="yp-customize" href="<?php echo admin_url('admin.php?page=yellow-pencil-editor&href='.yp_urlencode(get_home_url().'/')); ?>"><span class="dashicons dashicons-edit"></span> <?php _e('Customize','yp'); ?></a>

						<span class="yp-clearfix"></span>
					</li>
			<?php } ?>

			<?php

				$post_types = get_post_types();
				foreach ($post_types as $post_type){

					if(get_option("wt_".$post_type."_css") != ''){

					$count = 1;

					$last_post = wp_get_recent_posts(array("post_status" => "publish","numberposts" => 1, "post_type" => $post_type));
					if(empty($last_post) == false){
						$last_post_id = $last_post['0']['ID'];
					}
				?>
					<li>
						<span class="yp-title"><?php _e('Single','yp'); ?> <?php echo ucfirst($post_type); ?> <?php _e('Template','yp'); ?></span>
						<a class="yp-remove" onclick="return confirm('<?php _e("Are you sure?","yp"); ?>')" href="<?php echo admin_url('options-general.php?page=yp-options&yp_reset_type='.$post_type.''); ?>"><span class="dashicons dashicons-no"></span> <?php _e('Delete','yp'); ?></a>

						<a class="yp-customize" href="<?php echo admin_url('admin.php?page=yellow-pencil-editor&href='.yp_urlencode(get_the_permalink($last_post_id)).'&yp_type='.$post_type.''); ?>"><span class="dashicons dashicons-edit"></span> <?php _e('Customize','yp'); ?></a>

						<span class="yp-clearfix"></span>
					</li>

				<?php
					}

				}
			?>

			<?php if(get_option("wt_home_css") != ''){

			$frontpage_id = get_option('page_on_front');
			if($frontpage_id == 0 || $frontpage_id == null){ ?>
			<li>
				<span class="yp-title">Home Page</span>
				<a class="yp-remove" onclick="return confirm('<?php _e("Are you sure?","yp"); ?>')" href="<?php echo admin_url('options-general.php?page=yp-options&yp_reset_type=home'); ?>"><span class="dashicons dashicons-no"></span> <?php _e('Delete','yp'); ?></a>
				<a class="yp-customize" href="<?php echo admin_url('admin.php?page=yellow-pencil-editor&href='.yp_urlencode(esc_url(get_home_url().'/')).'&yp_type=home'); ?>"><span class="dashicons dashicons-edit"></span> <?php _e('Customize','yp'); ?></a>
				<span class="yp-clearfix"></span>
			</li>
			<?php } } ?>

			<?php if(get_option("wt_search_css") != ''){ ?>
			<li>
				<span class="yp-title">Search Template</span>
				<a class="yp-remove" onclick="return confirm('<?php _e("Are you sure?","yp"); ?>')" href="<?php echo admin_url('options-general.php?page=yp-options&yp_reset_type=search'); ?>"><span class="dashicons dashicons-no"></span> <?php _e('Delete','yp'); ?></a>
				<a class="yp-customize" href="<?php echo admin_url('admin.php?page=yellow-pencil-editor&href='.yp_urlencode(esc_url(get_home_url().'/?s='.yp_getting_last_post_title().'')).'&yp_type=search'); ?>"><span class="dashicons dashicons-edit"></span> <?php _e('Customize','yp'); ?></a>
				<span class="yp-clearfix"></span>
			</li>
			<?php } ?>

			<?php if(get_option("wt_404_css") != ''){ ?>
			<li>
				<span class="yp-title">404 Template</span>
				<a class="yp-remove" onclick="return confirm('<?php _e("Are you sure?","yp"); ?>')" href="<?php echo admin_url('options-general.php?page=yp-options&yp_reset_type=404'); ?>"><span class="dashicons dashicons-no"></span> <?php _e('Delete','yp'); ?></a>
				<a class="yp-customize" href="<?php echo admin_url('admin.php?page=yellow-pencil-editor&href='.yp_urlencode(esc_url(get_home_url().'/?p=987654321')).'&yp_type=404'); ?>"><span class="dashicons dashicons-edit"></span> <?php _e('Customize','yp'); ?></a>
				<span class="yp-clearfix"></span>
			</li>
			<?php } ?>

			<?php if(get_option("wt_tag_css") != ''){ ?>
			<?php

			$tag_id = '';
			$tags = get_tags(array('orderby' => 'count', 'order' => 'DESC','number'=> 1 ));
			if(empty($tags) == false){
				$tag_id = $tags[0];
			}

			?>
			<li>
				<span class="yp-title">Tag Template</span>
				<a class="yp-remove" onclick="return confirm('<?php _e("Are you sure?","yp"); ?>')" href="<?php echo admin_url('options-general.php?page=yp-options&yp_reset_type=tag'); ?>"><span class="dashicons dashicons-no"></span> <?php _e('Delete','yp'); ?></a>
				<a class="yp-customize" href="<?php echo admin_url('admin.php?page=yellow-pencil-editor&href='.yp_urlencode(esc_url(get_tag_link($tag_id))).'&yp_type=tag'); ?>"><span class="dashicons dashicons-edit"></span> <?php _e('Customize','yp'); ?></a>
				<span class="yp-clearfix"></span>
			</li>
			<?php } ?>

			<?php if(get_option("wt_category_css") != ''){ ?>
			<?php

			$cat_id = '';
			$cats = get_categories(array('orderby' => 'count', 'order' => 'DESC','number'=> 1 ));
			if(empty($cats) == false){
				$cat_id = $cats[0];
			}

			?>
			<li>
				<span class="yp-title">Category Template</span>
				<a class="yp-remove" onclick="return confirm('<?php _e("Are you sure?","yp"); ?>')" href="<?php echo admin_url('options-general.php?page=yp-options&yp_reset_type=category'); ?>"><span class="dashicons dashicons-no"></span> <?php _e('Delete','yp'); ?></a>
				<a class="yp-customize" href="<?php echo admin_url('admin.php?page=yellow-pencil-editor&href='.yp_urlencode(esc_url(get_category_link($cat_id))).'&yp_type=category'); ?>"><span class="dashicons dashicons-edit"></span> <?php _e('Customize','yp'); ?></a>
				<span class="yp-clearfix"></span>
			</li>
			<?php } ?>

			<?php if(get_option("wt_author_css") != ''){ ?>
			<li>
				<span class="yp-title">Author Template</span>
				<a class="yp-remove" onclick="return confirm('<?php _e("Are you sure?","yp"); ?>')" href="<?php echo admin_url('options-general.php?page=yp-options&yp_reset_type=author'); ?>"><span class="dashicons dashicons-no"></span> <?php _e('Delete','yp'); ?></a>
				<a class="yp-customize" href="<?php echo admin_url('admin.php?page=yellow-pencil-editor&href='.yp_urlencode(esc_url(get_author_posts_url(1))).'&yp_type=author'); ?>"><span class="dashicons dashicons-edit"></span> <?php _e('Customize','yp'); ?></a>
				<span class="yp-clearfix"></span>
			</li>
			<?php } ?>

			<?php

				global $wpdb;
				$querystr = "SELECT * FROM `$wpdb->postmeta` WHERE `meta_key` LIKE '_wt_css'";
				$pageposts = $wpdb->get_results($querystr, OBJECT);

				if($pageposts):

					global $post;

					foreach ($pageposts as $post):

					$id = $post->post_id;
					$title = "'".ucfirst(get_the_title($id))."'";

					if($title == "''"){
						$title = '(Unknown)';
					}

					if(get_post_meta($id, '_wt_css', true) != ''){
					$count = 1;
					?>

						<li>
							<span class="yp-title"><?php echo $title; ?> <?php echo ucfirst(get_post_type($id)); ?></span>
							<a class="yp-remove" onclick="return confirm('<?php _e("Are you sure?","yp"); ?>')" href="<?php echo admin_url('options-general.php?page=yp-options&yp_reset_id='.$id.''); ?>"><span class="dashicons dashicons-no"></span> <?php _e('Delete','yp'); ?></a>

							<a class="yp-customize" href="<?php echo admin_url('admin.php?page=yellow-pencil-editor&href='.yp_urlencode(get_the_permalink($id)).'&yp_id='.$id.''); ?>"><span class="dashicons dashicons-edit"></span> <?php _e('Customize','yp'); ?></a>

							<span class="yp-clearfix"></span>
						</li>

					<?php
					}

					endforeach;

				endif;
				wp_reset_query();

			?>

			<?php

				if(0 == $count){
					echo '<li>'.__("No CSS Source! First, customize something on your website.","yp").'</li>';
				}

			?>

		</ul>

		<?php if($count > 0){ ?>
		<p><a href="<?php echo admin_url('options-general.php?page=yp-options&yp_exportCSS=true'); ?>" class="button">Download</a> all style codes as ready to use.</p>	
		<?php } ?>

		</div>

		<hr style="margin-top: 50px;margin-bottom: 25px;">

		<h2>Custom Animations</h2>

		<p><?php _e('You will see all custom animations pages at below. You can delete and customize to them.','yp'); ?></p>

		<div class="yp-code-group">

		<ul>

			<?php

				$countAnim = 0;

				$all_options =  wp_load_alloptions();
				foreach($all_options as $name => $value){
					if(stristr($name, 'yp_anim')){
						$countAnim = $countAnim+1;
						$name = str_replace("yp_anim_", "", $name);
						?>
						<li>
						<span class="yp-title"><?php echo ucwords(strtolower($name)); ?></span>
						<a class="yp-remove" onclick="return confirm('<?php _e("Are you sure?","yp"); ?>')" href="<?php echo admin_url('options-general.php?page=yp-options&yp_delete_animate=yp_anim_'.$name.''); ?>"><span class="dashicons dashicons-no"></span> <?php _e('Delete','yp'); ?></a>
						<span class="yp-clearfix"></span>
						</li>
						<?php
					}
				}

				if(0 == $countAnim){
					echo '<li>'.__("No custom animations! First, create several animations!","yp").'</li>';
				}

			?>
			

		</ul>

		
		</div>

		<hr style="margin-top: 50px;margin-bottom: 25px;">

		<h2>Export</h2>
		<p>Copy what appears to be a random string of alpha numeric characters into this textarea<br />and paste to Import textarea on another web site.</p>
		<div class="yp-export-section">
			<textarea rows="6" class="yp-admin-textarea"><?php echo yp_get_export_data(); ?></textarea>
		</div>

		<hr style="margin-top: 50px;margin-bottom: 25px;">

		<h2>Import</h2>
		<p>Paste your exported data into this textarea and click "Import Styles" button.</p>
		<form method="POST">
			<div class="yp-import-section">
				<textarea name="yp_json_import_data" rows="6" class="yp-admin-textarea"></textarea>
			</div>
			<input type="submit" class="button-primary" value="Import Styles" />
		</form>

		</div>
	<?php

}



/* ---------------------------------------------------- */
/* Adding welcome screen Hook							*/
/* ---------------------------------------------------- */
function welcome_screen_activate() {
  set_transient( '_welcome_screen_activation_redirect', true, 30 );
}

register_activation_hook( __FILE__, 'welcome_screen_activate' );



/* ---------------------------------------------------- */
/* Automatic redirect after active						*/
/* ---------------------------------------------------- */
function welcome_screen_do_activation_redirect() {
  // Bail if no activation redirect
    if ( ! get_transient( '_welcome_screen_activation_redirect' ) ) {
    return;
  }

  // Delete the redirect transient
  delete_transient( '_welcome_screen_activation_redirect' );

  // Bail if activating from network, or bulk
  if ( is_network_admin() || isset( $_GET['activate-multi'] ) ) {
    return;
  }

  // Redirect to bbPress about page
  wp_safe_redirect( add_query_arg( array( 'page' => 'yp-welcome-screen' ), admin_url( 'index.php' ) ) );

}

add_action( 'admin_init', 'welcome_screen_do_activation_redirect' );



/* ---------------------------------------------------- */
/* Adding a Hidden Welcome page							*/
/* ---------------------------------------------------- */
function welcome_screen_pages() {
  add_dashboard_page(
    'Welcome To Yellow Pencil',
    'Welcome To Yellow Pencil',
    'read',
    'yp-welcome-screen',
    'yp_welcome_screen_content'
  );
}

add_action('admin_menu', 'welcome_screen_pages');



/* ---------------------------------------------------- */
/* Welcome Screen Content 								*/
/* ---------------------------------------------------- */
function yp_welcome_screen_content(){
  ?>
  <div class="wrap yp-page-welcome about-wrap">
	<h1>Welcome to Yellow Pencil <?php echo YP_VERSION; ?></h1>

	<div class="about-text">
		Congratulations! You are about to use most powerful design tool for WordPress ever - Yellow Pencil Style Editor Plugin by WaspThemes.</div>
	<div class="wp-badge yp-badge">Version <?php echo YP_VERSION; ?></div>
	<p>
		<a href="<?php echo admin_url('themes.php?page=yellow-pencil'); ?>" class="button button-primary button-large">let's start!</a>
	</p>
	<h2 class="nav-tab-wrapper">
			<a href="<?php echo admin_url('index.php?page=yp-welcome-screen'); ?>" class="nav-tab<?php if(isset($_GET['tab']) == false){ ?> nav-tab-active<?php } ?>">Hello</a>
			<a href="<?php echo admin_url('index.php?page=yp-welcome-screen&tab=resources'); ?>" class="nav-tab<?php if(isset($_GET['tab']) == true){ ?> nav-tab-active<?php } ?>">Resources</a>
	</h2>

	<?php if(isset($_GET['tab']) == false){ ?>
	<div class="yp-welcome-tab">

		<img class="yp-featured-img" src="<?php echo WT_PLUGIN_URL; ?>images/promo.png" />

		<div class="yp-right-content">
			<h3>Front-End Design Tool For WordPress!</h3>
			<p>Yellow Pencil is advanced visual CSS style editor wordpress plugin that you can customize your website in real-time with a few clicks. Yellow Pencil works with any theme and plugin.</p>

			<p>Today become a professional web designer and personalizing your website in a few mins!</p>

			<h3>What's New?</h3>
			Take a look to <a href="http://waspthemes.com/yellow-pencil/inc/changelog.txt" target="_blank">changelog</a> for see all details about new updates.
		</div>
		<div class="clear"></div>

	</div>
	<?php }else{ ?>
	<div class="yp-welcome-tab">

		<div class="yp-resources-left">
			<h3>Resources</h3>
			<ul>
				<li><a href="http://waspthemes.com/yellow-pencil/" target="_blank">Official Website</a></li>
				<li><a href="http://waspthemes.com/yellow-pencil/documentation/" target="_blank">Documentation</a></li>
				<li><a href="https://www.youtube.com/channel/UCKGdPyfmphEdBWPXR91GnYQ" target="_blank">Tutorials</a></li>
				<li><a href="http://waspthemes.com/yellow-pencil/inc/changelog.txt" target="_blank">Changelogs</a></li>
				<li><a href="https://www.facebook.com/waspthemes/" target="_blank">Facebook page</a></li>
				<li><a href="https://waspthemes.ticksy.com/" target="_blank">Official support</a></li>
			</ul>
		</div>

		<div class="yp-resources-right">
			<h3>Versions</h3>
			
			<ul>
				<li><a href="https://wordpress.org/plugins/yellow-pencil-visual-theme-customizer/" target="_blank">Get Lite Version (Free)</a></li>
				<li><a href="http://codecanyon.net/item/yellow-pencil-visual-customizer-for-wordpress/11322180?ref=WaspThemes" target="_blank">Get Pro Version</a></li>
			</ul>

			<h3>Licenses</h3>
			
			<ul>
				<li><a href="http://themeforest.net/licenses/terms/regular" target="_blank">Regular Licence</a></li>
				<li><a href="http://themeforest.net/licenses/terms/extended" target="_blank">Extended Licence</a></li>
			</ul>

		</div>
		<div class="clear"></div>

	</div>
	<?php } ?>

	<?php if(isset($_GET['tab']) == false){ ?>
	<div class="yp-welcome-feature feature-section">

		<div class="yp-column">
			<img class="yp-img-center" src="<?php echo WT_PLUGIN_URL; ?>images/promo-1.png">
			
			<div class="yp-feature-column">
				<h4>Customize Your Website!</h4>
				<p>Edit colors, fonts, sizes and others with a few click for all screen sizes. <a href="<?php echo admin_url('themes.php?page=yellow-pencil'); ?>">start to visual customizing</a>.</p>
			</div>

		</div>

		<div class="yp-column">
			<img class="yp-img-center" src="<?php echo WT_PLUGIN_URL; ?>images/promo-2.png">
			
			<div class="yp-feature-column">
				<h4>Manage Style Sources</h4>
				<p>Keep site design in your control, You always can manage style sources from <a href="<?php echo admin_url('options-general.php?page=yp-options'); ?>">this page</a>.</p>
			</div>

		</div>

		<div class="yp-column">
			<img class="yp-img-center" src="<?php echo WT_PLUGIN_URL; ?>images/promo-3.png">
			
			<div class="yp-feature-column">
				<h4>Help & Support!</h4>
				<p>You must read all plugin documentation for learn how this works, Only 3 mins to read! <a target="_blank" href="http://waspthemes.com/yellow-pencil/documentation/">plugin docs</a>.</p>
			</div>

		</div>

		<div class="clear"></div>

	</div>

	<p class="yp-thank-you">Thank you for choosing Yellow Pencil,<br>Made By WaspThemes.</p>
	<?php } ?>

</div>
  <?php
}


/* ---------------------------------------------------- */
/* Remove Welcome Screen menu 							*/
/* ---------------------------------------------------- */
function welcome_screen_remove_menus() {
    remove_submenu_page( 'index.php', 'yp-welcome-screen' );
}

add_action( 'admin_head', 'welcome_screen_remove_menus' );


// @WaspThemes.
// Coded With Love..