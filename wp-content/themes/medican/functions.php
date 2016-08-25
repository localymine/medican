<?php
define('AZEXO_THEME_NAME', 'AZEXO');
define('AZEXO_THEME_VERSION', '1.19');

function azexo_return_bytes($val) {
    $val = trim($val);
    $last = strtolower($val[strlen($val) - 1]);
    switch ($last) {
        case 'g':
            $val *= 1024;
        case 'm':
            $val *= 1024;
        case 'k':
            $val *= 1024;
    }
    return $val;
}

function azexo_admin_notices() {
    if (version_compare(phpversion(), '5.3.6', '<')) {
        ?>
        <div class="error">
            <p><?php esc_html_e('PHP version must be >= 5.3.6', 'medican'); ?></p>
        </div>
        <?php
    }
    if (azexo_return_bytes(ini_get('memory_limit')) < (96 * 1024 * 1024)) {
        ?>
        <div class="error">
            <p><?php esc_html_e('Memory limit must be >= 96MB', 'medican'); ?></p>
        </div>
        <?php
    }
}

add_action('admin_notices', 'azexo_admin_notices');


add_action('after_setup_theme', 'azexo_after_setup_theme');

function azexo_after_setup_theme() {
    load_theme_textdomain('medican', get_template_directory() . '/languages');
    add_theme_support('post-formats', array('gallery', 'video'));
    add_theme_support('post-thumbnails');
    add_theme_support('automatic-feed-links');
    add_theme_support('title-tag');
}

add_filter('upload_mimes', 'azexo_upload_mimes');

function azexo_upload_mimes($mimes) {
    $mimes['svg'] = 'image/svg+xml';
    return $mimes;
}

$options = get_option(AZEXO_THEME_NAME);


add_action('admin_enqueue_scripts', 'azexo_admin_scripts');

function azexo_admin_scripts() {
    wp_register_script('azexo-admin', get_template_directory_uri() . '/js/admin.js', array('jquery'), AZEXO_THEME_VERSION, true);
    wp_enqueue_script('azexo-admin');
}

add_action('wp_enqueue_scripts', 'azexo_scripts');

function azexo_scripts() {
    wp_register_script('azexo-azexo', get_template_directory_uri() . '/js/azexo.js', array('jquery'), AZEXO_THEME_VERSION, true);
    wp_enqueue_script('azexo-azexo');

    if (file_exists(get_template_directory() . '/js/jquery.sticky-kit.min.js')) {
        wp_register_script('azexo-jquery.sticky-kit', get_template_directory_uri() . '/js/jquery.sticky-kit.min.js', array('jquery'), AZEXO_THEME_VERSION, true);
        wp_enqueue_script('azexo-jquery.sticky-kit');
    }

    if (file_exists(get_template_directory() . '/js/imagesloaded.pkgd.min.js')) {
        wp_register_script('azexo-imagesloaded', get_template_directory_uri() . '/js/imagesloaded.pkgd.min.js', array('jquery'), AZEXO_THEME_VERSION, true);
        wp_enqueue_script('azexo-imagesloaded');
    }

    if (file_exists(get_template_directory() . '/js/background-check.min.js')) {
        wp_register_script('azexo-background-check', get_template_directory_uri() . '/js/background-check.min.js', array(), AZEXO_THEME_VERSION, true);
        wp_enqueue_script('azexo-background-check');
    }

    if (file_exists(get_template_directory() . '/js/owl.carousel.min.js')) {
        wp_register_script('azexo-owl.carousel', get_template_directory_uri() . '/js/owl.carousel.min.js', array('jquery'), AZEXO_THEME_VERSION, true);
        wp_register_style('azexo-owl.carousel', get_template_directory_uri() . '/css/owl.carousel.min.css');
    }

    if (file_exists(get_template_directory() . '/js/jquery.magnific-popup.min.js')) {
        wp_register_script('azexo-magnific-popup', get_template_directory_uri() . '/js/jquery.magnific-popup.min.js', array('jquery'), AZEXO_THEME_VERSION, true);
        wp_register_style('azexo-magnific-popup', get_template_directory_uri() . '/css/magnific-popup.css');
    }

    if (file_exists(get_template_directory() . '/js/scrollReveal.min.js')) {
        wp_register_script('azexo-scrollReveal', get_template_directory_uri() . '/js/scrollReveal.min.js', AZEXO_THEME_VERSION, true);
    }

    if (file_exists(get_template_directory() . '/js/jquery.fitvids.js')) {
        wp_register_script('azexo-fitvids', get_template_directory_uri() . '/js/jquery.fitvids.js', AZEXO_THEME_VERSION, true);
        wp_enqueue_script('azexo-fitvids');
    }

    if (file_exists(get_template_directory() . '/js/jquery.waypoints.min.js')) {
        wp_register_script('azexo-waypoints', get_template_directory_uri() . '/js/jquery.waypoints.min.js', AZEXO_THEME_VERSION, true);
    }

    if (file_exists(get_template_directory() . '/js/stacktable.js')) {
        wp_register_script('azexo-stacktable', get_template_directory_uri() . '/js/stacktable.js', AZEXO_THEME_VERSION, true);
        wp_enqueue_script('azexo-stacktable');
    }

    if (file_exists(get_template_directory() . '/js/jquery.countdown.min.js')) {
        wp_register_script('azexo-countdown', get_template_directory_uri() . '/js/jquery.countdown.min.js', array('jquery'), AZEXO_THEME_VERSION, true);
        wp_enqueue_script('azexo-countdown');
    }


    wp_deregister_script('flexslider');
    wp_deregister_style('flexslider');
    if (file_exists(get_template_directory() . '/js/jquery.flexslider-min.js')) {
        wp_register_script('azexo-flexslider', get_template_directory_uri() . '/js/jquery.flexslider-min.js');
        wp_register_style('azexo-flexslider', get_template_directory_uri() . '/css/flexslider.css');
    }


    //move styles to header for HTML5 validation
    wp_enqueue_style('azexo-owl.carousel');
    wp_enqueue_style('azexo-flexslider');
    wp_enqueue_style('azexo-magnific-popup');
    wp_enqueue_style('js_composer_front');
    wp_enqueue_style('yarppRelatedCss');

    if (is_singular() && comments_open() && get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }

    if (class_exists('WC_Bookings')) {
        wp_dequeue_style('jquery-ui-style');
    }
}

function azexo_get_dir_files($src) {
    $files = array();
    $dir = opendir($src);
    if (is_resource($dir))
        while (false !== ( $file = readdir($dir))) {
            if (( $file != '.' ) && ( $file != '..' )) {
                $files[$file] = realpath($src . DIRECTORY_SEPARATOR . $file);
            }
        }
    closedir($dir);
    return $files;
}

function azexo_get_skins() {
    $skins = array();
    $files = azexo_get_dir_files(get_template_directory() . '/less');
    foreach ($files as $name => $path) {
        if (is_dir($path)) {
            $skin_files = azexo_get_dir_files($path);
            if (isset($skin_files['azexo.less'])) {
                $skins[] = $name;
            }
        }
    }
    return $skins;
}

function azexo_get_skin() {
    $options = get_option(AZEXO_THEME_NAME);
    $skin = '';
    if (isset($options['skin'])) {
        if (file_exists(get_template_directory() . '/less/' . $options['skin'] . '/azexo.less')) {
            $skin = $options['skin'];
        }
    }
    if (empty($skin)) {
        $skins = azexo_get_skins();
        $skin = reset($skins);
    }
    return $skin;
}

add_action('init', 'azexo_load_default_skin_options', 12); // after options-init.php

function azexo_load_default_skin_options() {
    $options = get_option(AZEXO_THEME_NAME);
    if (!isset($options['skin'])) {
        $skins = azexo_get_skins();
        $skin = reset($skins);
        $file = get_template_directory() . '/azexo/options/' . $skin . '.json';
        if (file_exists($file)) {
            require_once(ABSPATH . 'wp-admin/includes/file.php');
            require_once(ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php');
            require_once(ABSPATH . 'wp-admin/includes/class-wp-filesystem-direct.php');
            $wp_filesystem = new WP_Filesystem_Direct(array());
            $file_contents = $wp_filesystem->get_contents($file);
            $options = json_decode($file_contents, true);
            if (is_admin() && function_exists('get_redux_instance')) {
                $redux = get_redux_instance(AZEXO_THEME_NAME);
                $redux->set_options($options);
            } else {
                update_option(AZEXO_THEME_NAME, $options);
            }
        }
    }
}

add_action('wp_enqueue_scripts', 'azexo_styles');

function azexo_styles() {
    if (function_exists('visual_composer')) {
        visual_composer()->frontCss();
    }

    if (file_exists(get_template_directory() . '/css/animate.css/animate.min.css')) {
        wp_register_style('azexo-animate-css', get_template_directory_uri() . '/css/animate.css/animate.min.css');
        wp_enqueue_style('azexo-animate-css');
    }

    if (!wp_style_is('font-awesome', 'register') && file_exists(get_template_directory() . '/css/font-awesome.min.css')) {
        wp_register_style('font-awesome', get_template_directory_uri() . '/css/font-awesome.min.css');
    }
    wp_enqueue_style('font-awesome');

    if (file_exists(get_template_directory() . '/css/themify-icons.css')) {
        wp_register_style('azexo-themify-icons', get_template_directory_uri() . '/css/themify-icons.css');
        wp_enqueue_style('azexo-themify-icons');
    }

    if (class_exists('WPLessPlugin')) {
        require_once(trailingslashit(get_template_directory()) . 'azexo/less-variables.php');
        $less = WPLessPlugin::getInstance();
        $less->dispatch();
        $skin_style = get_template_directory_uri() . '/less/' . azexo_get_skin() . '/azexo.less';
        if (is_child_theme()) {
            if (file_exists(get_stylesheet_directory() . '/less/' . azexo_get_skin() . '/azexo.less')) {
                $skin_style = get_stylesheet_directory_uri() . '/less/' . azexo_get_skin() . '/azexo.less';
            }
        }
        wp_enqueue_style('azexo', $skin_style);
    } else {
        $skin_style = get_template_directory_uri() . '/css/' . azexo_get_skin() . '/azexo.css';
        if (is_child_theme()) {
            if (file_exists(get_stylesheet_directory() . '/css/' . azexo_get_skin() . '/azexo.css')) {
                $skin_style = get_stylesheet_directory_uri() . '/css/' . azexo_get_skin() . '/azexo.css';
            }
        }
        wp_enqueue_style('azexo', $skin_style);
    }

    wp_enqueue_style('azexo-style', get_stylesheet_uri(), array('azexo'));
}

add_action('wp_footer', 'azexo_inline_js');

function azexo_inline_js() {
    $options = get_option(AZEXO_THEME_NAME);
    if (!empty($options['custom-js'])) {
        print '<script type="text/javascript">';
        print $options['custom-js'];
        print 'var templateurl = "' . esc_js(get_template_directory_uri()) . '";';
        print '</script>';
    }
}

add_action('wp_head', 'azexo_inline_css');

function azexo_inline_css() {
    $options = get_option(AZEXO_THEME_NAME);
    if (!empty($options['custom-css'])) {
        print '<style type="text/css">';
        print $options['custom-css'];
        print '</style>';
    }
}

add_action('wp_head', 'azexo_dynamic_css');

if (!function_exists('azexo_dynamic_css')) {

    function azexo_dynamic_css() {
        echo '<!--CUSTOM STYLE--><style type="text/css">';

        $post_categories = get_categories();
        global $azexo_category_fields;

        if (!empty($post_categories)) {
            foreach ($post_categories as $cat) {
                $cat_color = $azexo_category_fields->get_category_meta($cat->cat_ID, 'color');
                print $cat_color ? 'a.' . esc_attr(str_replace('_', '-', $cat->slug)) . '[rel="category tag"], a.' . esc_attr(str_replace('_', '-', $cat->slug)) . '[rel="category"] { background-color:' . esc_attr($cat_color) . ' !important;}' : '';
            }
        }

        echo '</style><!--/CUSTOM STYLE-->';
    }

}

add_filter('embed_defaults', 'azexo_embed_defaults');

function azexo_embed_defaults() {
    return array('width' => 1000, 'height' => 500);
}

function azexo_excerpt($content, $excerpt_length = false, $trim_by_words = true) {
    if (empty($excerpt_length)) {
        $excerpt_length = isset($options['excerpt_length']) ? $options['excerpt_length'] : 15;
    }
    $options = get_option(AZEXO_THEME_NAME);
    if (isset($options['strip_excerpt']) && $options['strip_excerpt'] && is_numeric($excerpt_length)) {
        $excerpt = wp_strip_all_tags(strip_shortcodes($content));
        if ($trim_by_words) {
            $excerpt = wp_trim_words($excerpt, $excerpt_length);
        } else {
            $excerpt = substr($excerpt, 0, $excerpt_length) . '...';
        }
        return $excerpt;
    } else {
        return $content;
    }
}

function azexo_comment_excerpt($content) {
    $options = get_option(AZEXO_THEME_NAME);
    $excerpt = wp_trim_words(wp_strip_all_tags(strip_shortcodes($content)), isset($options['comment_excerpt_length']) ? $options['comment_excerpt_length'] : 15);
    return $excerpt;
}

if (!isset($content_width)) {
    $content_width = 1;
}

class Azexo_Walker_Comment extends Walker_Comment {

    protected function comment($comment, $depth, $args) {
        if ('div' == $args['style']) {
            $tag = 'div';
            $add_below = 'comment';
        } else {
            $tag = 'li';
            $add_below = 'div-comment';
        }
        ?>
        <<?php print $tag; ?> <?php comment_class($this->has_children ? 'parent' : '' ); ?> id="comment-<?php comment_ID(); ?>">
        <?php if ('div' != $args['style']) : ?>
            <div id="div-comment-<?php comment_ID(); ?>" class="comment-body">
            <?php endif; ?>
            <div class="comment-author">
                <?php if (0 != $args['avatar_size']) echo get_avatar($comment, $args['avatar_size']); ?>                
            </div>
            <div class="comment-data">
                <?php printf(wp_kses(__('<cite class="fn">%s</cite>', 'medican'), array('cite' => array('class' => array()))), get_comment_author_link()); ?>
                <?php if ('0' == $comment->comment_approved) : ?>
                    <em class="comment-awaiting-moderation"><?php esc_html_e('Your comment is awaiting moderation.', 'medican') ?></em>
                    <br />
                <?php endif; ?>
                <div class="comment-meta commentmetadata"><a href="<?php echo esc_url(get_comment_link($comment->comment_ID, $args)); ?>">
                        <?php
                        /* translators: 1: date, 2: time */
                        printf(esc_html__('%1$s at %2$s', 'medican'), get_comment_date(), get_comment_time());
                        ?></a><?php edit_comment_link(esc_html__('(Edit)', 'medican'), '&nbsp;&nbsp;', '');
                        ?>
                </div>
                <?php comment_text(get_comment_id(), array_merge($args, array('add_below' => $add_below, 'depth' => $depth, 'max_depth' => $args['max_depth']))); ?>
                <div class="reply">
                    <?php comment_reply_link(array_merge($args, array('reply_text' => esc_html__('Reply', 'medican'), 'add_below' => $add_below, 'depth' => $depth, 'max_depth' => $args['max_depth']))); ?>
                </div>
            </div>
            <?php if ('div' != $args['style']) : ?>
            </div>
        <?php endif; ?>
        <?php
    }

}

if (function_exists('register_nav_menus')) {
    register_nav_menus(array(
        'primary' => esc_html__('Top primary menu', 'medican'),
        'secondary' => esc_html__('Secondary menu', 'medican'),
    ));
}

function azexo_get_post_wpb_css($id = NULL) {
    if ($id == NULL) {
        $id = get_the_ID();
    }
    $shortcodes_custom_css = get_post_meta($id, '_wpb_shortcodes_custom_css', true);
    if (!empty($shortcodes_custom_css)) {
        return '<style type="text/css" data-type="vc_shortcodes-custom-css" scoped>' . $shortcodes_custom_css . '</style>';
    }
    return '';
}

function azexo_replace_vc_ids($content) {
    $matches = array();
    preg_match_all('/tab\_id\=\"([^\"]+)\"/', $content, $matches);
    foreach ($matches[0] as $match) {
        $content = str_replace($match, 'tab_id="azexo-' . rand(0, 99999999) . '"', $content);
    }
    return $content;
}

global $azexo_current_post_stack;
$azexo_current_post_stack = array();
add_action('the_post', 'azexo_the_post');

function azexo_the_post($post) {
    global $azexo_current_post_stack;
    $index = count($azexo_current_post_stack);
    while ($index) {
        $index--;
        if ($azexo_current_post_stack[$index]->ID == $post->ID) {
            array_splice($azexo_current_post_stack, $index);
        }
    }
    $azexo_current_post_stack[] = $post;
}

add_action('get_header', 'azexo_get_header');

function azexo_get_header() {
    if (is_singular()) {
        global $azexo_current_post_stack, $wp_query;
        $azexo_current_post_stack = array($wp_query->get_queried_object());
    }
}

function azexo_get_closest_current_post($post_type, $equal = true) {
    global $azexo_current_post_stack;
    $post = null;
    $index = count($azexo_current_post_stack);
    $post_type = (array) $post_type;
    while ($index) {
        $index--;
        if ($equal) {
            if (in_array($azexo_current_post_stack[$index]->post_type, $post_type)) {
                $post = $azexo_current_post_stack[$index];
                break;
            }
        } else {
            if (!in_array($azexo_current_post_stack[$index]->post_type, $post_type)) {
                $post = $azexo_current_post_stack[$index];
                break;
            }
        }
    }
    return $post;
}

function azexo_get_post_content($id = NULL) {
    if ($id == NULL) {
        $content = get_the_content('');
        $content = azexo_replace_vc_ids($content);
        $content = '<div class="scoped-style">' . azexo_get_post_wpb_css(get_the_ID()) . apply_filters('the_content', $content) . '</div>';
    } else {
        global $post;
        $original = $post;
        $post = get_post($id);
        setup_postdata($post);
        $content = get_the_content('');
        $content = azexo_replace_vc_ids($content);
        $content = '<div class="scoped-style">' . azexo_get_post_wpb_css($id) . apply_filters('the_content', $content) . '</div>';
        wp_reset_postdata();
        $post = $original;
    }

    return $content;
}

add_filter('nav_menu_link_attributes', 'azexo_nav_menu_link_attributes', 10, 4);

function azexo_nav_menu_link_attributes($atts, $item, $args, $depth) {
    if (strpos($atts['title'], 'mega') !== false) {
        $atts['title'] = str_replace('mega', '', $atts['title']);
        $atts['href'] = '#';
    }
    $atts['class'] = 'menu-link';
    return $atts;
}

add_filter('nav_menu_css_class', 'azexo_nav_menu_css_class', 10, 4);

function azexo_nav_menu_css_class($classes, $item, $args, $depth) {
    if (strpos($item->attr_title, 'mega') !== false && $depth == 0) {
        $classes[] = 'mega';
    }
    return $classes;
}

add_filter('widget_nav_menu_args', 'azexo_widget_nav_menu_args', 10, 3);

function azexo_widget_nav_menu_args($nav_menu_args, $nav_menu, $args) {
    $nav_menu_args['walker'] = new Azexo_Walker_Nav_Menu();
    if (isset($args['vc']) && $args['vc']) {
        $nav_menu_args['vc'] = true;
        $nav_menu_args['menu_class'] = 'menu vc';
    }
    return $nav_menu_args;
}

class Azexo_Walker_Nav_Menu extends Walker_Nav_Menu {

    public function start_lvl(&$output, $depth = 0, $args = array()) {
        $indent = str_repeat("\t", $depth);
        if (is_array($args) && isset($args['vc']) && $args['vc'] || is_object($args) && isset($args->vc) && $args->vc) {
            $output .= "\n$indent<ul class=\"sub-menu vc\">\n";
        } else {
            $output .= "\n$indent<ul class=\"sub-menu\">\n";
        }
    }

    public function start_el(&$output, $item, $depth = 0, $args = array(), $id = 0) {
        $item->additions = '';
        $item = apply_filters('azexo_menu_start_el', $item, $args);
        if (preg_match('/icon\(([^\)]*)\)/i', $item->attr_title, $icon)) {
            $item->attr_title = str_replace($icon[0], '', $item->attr_title);
            $args->link_before = ' <span class="' . $icon[1] . '"></span>';
        }
        if (is_array($item->classes)) {
            if (in_array('fa', $item->classes)) {
                $item->classes = array_diff($item->classes, array('fa'));
                $searchword = 'fa-';
                $matches = array_filter($item->classes, function($var) use ($searchword) {
                    return preg_match("/\b$searchword\b/i", $var);
                });
                foreach ($matches as $match) {
                    $item->classes = array_diff($item->classes, array($match));
                    $args->link_before = ' <span class="fa ' . $match . '"></span>';
                }
            }
            $searchword = 'ti-';
            $matches = array_filter($item->classes, function($var) use ($searchword) {
                return preg_match("/\b$searchword\b/i", $var);
            });
            foreach ($matches as $match) {
                $item->classes = array_diff($item->classes, array($match));
                $args->link_before = ' <span class="' . $match . '"></span>';
            }
        }
        if (isset($item->description) && !empty($item->description)) {
            $args->link_after = '<span class="description">' . $item->description . '</span>';
        }
        parent::start_el($output, $item, $depth, $args, $id);
        $args->link_before = '';
        $args->link_after = '';
    }

    public function end_el(&$output, $item, $depth = 0, $args = array()) {
        if (strpos($item->attr_title, 'mega') !== false && $depth == 0) {
            $output .= '<div class="page">' . azexo_get_post_content($item->object_id) . '</div>';
        }
        $output .= $item->additions;
        $item->additions = '';
        $output .= "</li>\n";
    }

}

add_filter('widget_categories_args', 'azexo_widget_categories_args');

function azexo_widget_categories_args($args) {
    $args['walker'] = new Azexo_Walker_Category();
    return $args;
}

class Azexo_Walker_Category extends Walker_Category {

    public function start_el(&$output, $category, $depth = 0, $args = array(), $id = 0) {
        $show_count = 0;
        if (isset($args['show_count'])) {
            $show_count = $args['show_count'];
            $args['show_count'] = 0;
        }

        parent::start_el($output, $category, $depth, $args, $id);

        $args['show_count'] = $show_count;

        if ($show_count) {
            $output .= ' <span class="count">' . number_format_i18n($category->count) . '</span>';
        }
    }

}

if (file_exists(get_template_directory() . '/azexo/woocommerce.php')) {
    require_once(trailingslashit(get_template_directory()) . 'azexo/woocommerce.php');
}
require_once(trailingslashit(get_template_directory()) . 'azexo/class.category-custom-fields.php');
if (is_admin()) {
    require_once(trailingslashit(get_template_directory()) . 'redux-extensions/loader.php');
    require_once(trailingslashit(get_template_directory()) . 'azexo/options-init.php');
    require_once(trailingslashit(get_template_directory()) . 'tgm/class-tgm-plugin-activation.php');
    require_once(trailingslashit(get_template_directory()) . 'azexo/tgm-init.php');
}
require_once(trailingslashit(get_template_directory()) . 'post-like-system/post-like.php');
require_once(trailingslashit(get_template_directory()) . 'widgets/widgets.php');

add_action('init', 'azexo_init', 12); // after options-init.php

function azexo_init() {
    require_once(trailingslashit(get_template_directory()) . 'azexo/vc_extend.php');
    global $azexo_fields_post_types;
    if (!isset($azexo_fields_post_types)) {
        $azexo_fields_post_types = array();
    }
    global $azexo_post_fields;
    if (!isset($azexo_post_fields)) {
        $azexo_post_fields = array();
    }
    if (!empty($azexo_post_fields)) {
        $azexo_fields_post_types = array_merge($azexo_fields_post_types, array_combine(array_keys($azexo_post_fields), array_fill(0, count(array_keys($azexo_post_fields)), 'post')));
    }

    $azexo_fields_post_types = apply_filters('azexo_fields_post_types', $azexo_fields_post_types);
}

add_action('widgets_init', 'azexo_widgets_init');

function azexo_widgets_init() {
    if (function_exists('register_sidebar')) {
        register_sidebar(array('name' => esc_html__('Right/Left sidebar', 'medican'), 'id' => "sidebar", 'before_widget' => '<div id="%1$s" class="widget %2$s">', 'after_widget' => '</div>', 'before_title' => '<div class="widget-title"><h3>', 'after_title' => '</h3></div>'));
        register_sidebar(array('name' => esc_html__('Footer sidebar', 'medican'), 'id' => "footer_sidebar", 'before_widget' => '<div id="%1$s" class="widget %2$s">', 'after_widget' => '</div>', 'before_title' => '<div class="widget-title"><h3>', 'after_title' => '</h3></div>'));
        register_sidebar(array('name' => esc_html__('Header sidebar', 'medican'), 'id' => "header_sidebar", 'before_widget' => '<div id="%1$s" class="widget %2$s">', 'after_widget' => '</div>', 'before_title' => '<div class="widget-title"><h3>', 'after_title' => '</h3></div>'));
        register_sidebar(array('name' => esc_html__('Middle sidebar', 'medican'), 'id' => "middle_sidebar", 'before_widget' => '<div id="%1$s" class="widget %2$s">', 'after_widget' => '</div>', 'before_title' => '<div class="widget-title"><h3>', 'after_title' => '</h3></div>'));
    }
}

function azexo_removeDemoModeLink() {
    if (class_exists('ReduxFrameworkPlugin')) {
        remove_filter('plugin_row_meta', array(ReduxFrameworkPlugin::get_instance(), 'plugin_metalinks'), null, 2);
    }
    if (class_exists('ReduxFrameworkPlugin')) {
        remove_action('admin_notices', array(ReduxFrameworkPlugin::get_instance(), 'admin_notices'));
    }
}

add_action('init', 'azexo_removeDemoModeLink');

add_filter('use_default_gallery_style', '__return_false');

if (is_admin()) {
    require_once(trailingslashit(get_template_directory()) . 'azexo/exporter/export.php');
    require_once(trailingslashit(get_template_directory()) . 'azexo/importer/import.php');
}

add_action('wp_insert_comment', 'azexo_insert_comment', 10, 2);

function azexo_insert_comment($id, $comment) {
    delete_post_meta($comment->comment_post_ID, 'last_comment_date');
    add_post_meta($comment->comment_post_ID, 'last_comment_date', $comment->comment_date);
}

function azexo_paging_nav() {
    global $wp_query, $wp_rewrite;

    // Don't print empty markup if there's only one page.
    if ($wp_query->max_num_pages < 2) {
        return;
    }

    $paged = get_query_var('paged') ? intval(get_query_var('paged')) : 1;
    $pagenum_link = html_entity_decode(get_pagenum_link());
    $query_args = array();
    $url_parts = explode('?', $pagenum_link);

    if (isset($url_parts[1])) {
        wp_parse_str($url_parts[1], $query_args);
    }

    $pagenum_link = remove_query_arg(array_keys($query_args), $pagenum_link);
    $pagenum_link = trailingslashit($pagenum_link) . '%_%';

    $format = $wp_rewrite->using_index_permalinks() && !strpos($pagenum_link, 'index.php') ? 'index.php/' : '';
    $format .= $wp_rewrite->using_permalinks() ? user_trailingslashit($wp_rewrite->pagination_base . '/%#%', 'paged') : '?paged=%#%';

    // Set up paginated links.
    $links = paginate_links(array(
        'base' => $pagenum_link,
        'format' => $format,
        'total' => $wp_query->max_num_pages,
        'current' => $paged,
        'mid_size' => 1,
        'add_args' => array_map('urlencode', $query_args),
        'prev_text' => '<i class="prev"></i>' . '<span>' . esc_html__('Previous', 'medican') . '</span>',
        'next_text' => '<span>' . esc_html__('Next', 'medican') . '</span>' . '<i class="next"></i>',
    ));

    if ($links) :
        ?>
        <nav class="navigation paging-navigation">
            <div class="pagination loop-pagination">
                <?php print $links; ?>
            </div><!-- .pagination -->
        </nav><!-- .navigation -->
        <?php
    endif;
}

function azexo_post_nav() {
    global $post;

    // Don't print empty markup if there's nowhere to navigate.
    $previous = ( is_attachment() ) ? get_post($post->post_parent) : get_adjacent_post(false, '', true);
    $next = get_adjacent_post(false, '', false);
    $options = get_option(AZEXO_THEME_NAME);
    if (!$next && !$previous)
        return;
    ?>
    <nav class="navigation post-navigation clearfix">
        <div class="nav-links">

            <?php previous_post_link('%link', '<i class="prev"></i><div class="prev-post"><span class="helper">' . (isset($options['post_navigation_previous']) ? $options['post_navigation_previous'] : '') . '</span><span class="title">%title</span></div>'); ?>
            <?php next_post_link('%link', '<i class="next"></i><div class="next-post"><span class="helper">' . (isset($options['post_navigation_next']) ? $options['post_navigation_next'] : '') . '</span><span class="title">%title</span></div>'); ?>

        </div><!-- .nav-links -->
    </nav><!-- .navigation -->
    <?php
}

function azexo_get_the_category_list($separator = '', $parents = '', $post_id = false) {
    global $wp_rewrite;
    if (!is_object_in_taxonomy(get_post_type($post_id), 'category')) {
        /** This filter is documented in wp-includes/category-template.php */
        return apply_filters('the_category', '', $separator, $parents);
    }

    $categories = get_the_category($post_id);
    if (empty($categories)) {
        /** This filter is documented in wp-includes/category-template.php */
        return apply_filters('the_category', esc_html__('Uncategorized', 'medican'), $separator, $parents);
    }

    $rel = ( is_object($wp_rewrite) && $wp_rewrite->using_permalinks() ) ? 'rel="category tag"' : 'rel="category"';

    $thelist = '';
    if ('' == $separator) {
        $thelist .= '<ul class="post-categories">';
        foreach ($categories as $category) {
            $thelist .= "\n\t<li>";
            switch (strtolower($parents)) {
                case 'multiple':
                    if ($category->parent)
                        $thelist .= get_category_parents($category->parent, true, $separator);
                    $thelist .= '<a class="' . str_replace('_', '-', $category->slug) . '" href="' . esc_url(get_category_link($category->term_id)) . '" ' . $rel . '>' . $category->name . '</a></li>';
                    break;
                case 'single':
                    $thelist .= '<a class="' . str_replace('_', '-', $category->slug) . '" href="' . esc_url(get_category_link($category->term_id)) . '"  ' . $rel . '>';
                    if ($category->parent)
                        $thelist .= get_category_parents($category->parent, false, $separator);
                    $thelist .= $category->name . '</a></li>';
                    break;
                case '':
                default:
                    $thelist .= '<a class="' . str_replace('_', '-', $category->slug) . '" href="' . esc_url(get_category_link($category->term_id)) . '" ' . $rel . '>' . $category->name . '</a></li>';
            }
        }
        $thelist .= '</ul>';
    } else {
        $i = 0;
        foreach ($categories as $category) {
            if (0 < $i)
                $thelist .= $separator;
            switch (strtolower($parents)) {
                case 'multiple':
                    if ($category->parent)
                        $thelist .= get_category_parents($category->parent, true, $separator);
                    $thelist .= '<a class="' . str_replace('_', '-', $category->slug) . '" href="' . esc_url(get_category_link($category->term_id)) . '" ' . $rel . '>' . $category->name . '</a>';
                    break;
                case 'single':
                    $thelist .= '<a class="' . str_replace('_', '-', $category->slug) . '" href="' . esc_url(get_category_link($category->term_id)) . '" ' . $rel . '>';
                    if ($category->parent)
                        $thelist .= get_category_parents($category->parent, false, $separator);
                    $thelist .= "$category->name</a>";
                    break;
                case '':
                default:
                    $thelist .= '<a class="' . str_replace('_', '-', $category->slug) . '" href="' . esc_url(get_category_link($category->term_id)) . '" ' . $rel . '>' . $category->name . '</a>';
            }
            ++$i;
        }
    }
    return apply_filters('the_category', $thelist, $separator, $parents);
}

function azexo_post_thumbnail_field($template_name = false) {
    if ($template_name === false) {
        $template_name = azexo_get_closest_current_post('vc_widget', false);
        $template_name = $template_name->post_type;
    }
    $options = get_option(AZEXO_THEME_NAME);
    $thumbnail_size = isset($options[$template_name . '_thumbnail_size']) && !empty($options[$template_name . '_thumbnail_size']) ? $options[$template_name . '_thumbnail_size'] : 'large';
    $lazy = isset($options[$template_name . '_lazy']) && !empty($options[$template_name . '_lazy']) ? $options[$template_name . '_lazy'] : false;
    if ($lazy) {
        wp_enqueue_script('azexo-waypoints');
    }
    $url = azexo_get_the_post_thumbnail(get_the_ID(), $thumbnail_size, true);
    $size = azexo_get_image_sizes($thumbnail_size);
    $zoom = isset($options[$template_name . '_zoom']) && esc_attr($options[$template_name . '_zoom']) ? 'zoom' : '';
    ?>                
    <a href="<?php the_permalink(); ?>">
        <?php if ($lazy): ?>
            <?php if ($thumbnail_size == 'full'): ?>
                <img class="image lazy <?php print esc_url($zoom); ?>" data-src="<?php print esc_url($url[0]); ?>" alt="">
            <?php else: ?>
                <div class="image lazy <?php print esc_url($zoom); ?>" data-src="<?php print esc_url($url[0]); ?>" style="height: <?php print esc_attr($size['height']); ?>px;" data-width="<?php print esc_attr($size['width']); ?>" data-height="<?php print esc_attr($size['height']); ?>">
                </div>
            <?php endif; ?>
        <?php else: ?>
            <?php if ($thumbnail_size == 'full'): ?>
                <img class="image <?php print esc_url($zoom); ?>" src="<?php print esc_url($url[0]); ?>" alt="">
            <?php else: ?>
                <div class="image <?php print esc_url($zoom); ?>" style='background-image: url("<?php print esc_url($url[0]); ?>"); height: <?php print esc_attr($size['height']); ?>px;' data-width="<?php print esc_attr($size['width']); ?>" data-height="<?php print esc_attr($size['height']); ?>">
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </a>
    <?php
}

function azexo_post_gallery_field($template_name = false) {
    if ($template_name === false) {
        $template_name = azexo_get_closest_current_post('vc_widget', false);
        $template_name = $template_name->post_type;
    }
    $options = get_option(AZEXO_THEME_NAME);
    $thumbnail_size = isset($options[$template_name . '_thumbnail_size']) && !empty($options[$template_name . '_thumbnail_size']) ? $options[$template_name . '_thumbnail_size'] : 'large';
    $gallery = get_post_gallery(get_the_ID(), false);
    if (is_array($gallery)) {
        if (isset($gallery['ids'])) {
            $attachment_ids = explode(",", $gallery['ids']);
            print azexo_entry_gallery($attachment_ids, isset($options[$template_name . '_show_carousel']) && $options[$template_name . '_show_carousel'], isset($options[$template_name . '_gallery_slider_thumbnails']) && $options[$template_name . '_gallery_slider_thumbnails'], $thumbnail_size, isset($options[$template_name . '_gallery_slider_thumbnails_vertical']) && $options[$template_name . '_gallery_slider_thumbnails_vertical']);
        } else {
            if (isset($gallery['src']) && is_array($gallery['src'])) {
                print azexo_entry_gallery($gallery['src'], isset($options[$template_name . '_show_carousel']) && $options[$template_name . '_show_carousel'], isset($options[$template_name . '_gallery_slider_thumbnails']) && $options[$template_name . '_gallery_slider_thumbnails'], $thumbnail_size, isset($options[$template_name . '_gallery_slider_thumbnails_vertical']) && $options[$template_name . '_gallery_slider_thumbnails_vertical']);
            }
        }
    }
}

function azexo_post_video_field() {
    $embed = azexo_get_first_shortcode(get_the_content(''), 'embed');
    if ($embed) {
        global $wp_embed;
        print $wp_embed->run_shortcode($embed);
    }
}

function azexo_get_field_templates() {
    $cache_hash = md5(get_theme_root() . '/' . get_stylesheet());
    $field_templates = wp_cache_get('field_templates-' . $cache_hash, 'themes');

    if (!is_array($field_templates) && is_admin()) {
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php');
        require_once(ABSPATH . 'wp-admin/includes/class-wp-filesystem-direct.php');
        $wp_filesystem = new WP_Filesystem_Direct(array());

        $field_templates = array();
        foreach (array(get_template_directory(), get_stylesheet_directory()) as $path) {
            if (is_dir($path . '/fields')) {
                $directory_iterator = new RecursiveDirectoryIterator($path . '/fields');
                foreach ($directory_iterator as $fileInfo) {
                    if ($fileInfo->isFile() && $fileInfo->getExtension() == 'php') {

                        $file_contents = $wp_filesystem->get_contents($fileInfo->getPathname());

                        if (!preg_match('|Field Name:(.*)$|mi', $file_contents, $header)) {
                            continue;
                        }
                        $field_templates[$fileInfo->getFilename()] = _cleanup_header_comment($header[1]);
                    }
                }
            }
        }

        wp_cache_add('field_templates-' . $cache_hash, $field_templates, 'themes', 1800);
    }

    return $field_templates;
}

function azexo_entry_field($name) {
    $options = get_option(AZEXO_THEME_NAME);

    $output = apply_filters('azexo_entry_field', false, $name);
    if ($output)
        return $output;

    if (is_numeric($name)) {
        return azexo_get_post_content($name);
    }

    if (strpos($name, '.php') !== false) {
        ob_start();
        include(locate_template(array('fields/' . $name)));
        return ob_get_clean();
    }

    $image = (isset($options[$name . '_image']) && !empty($options[$name . '_image']['url'])) ? '<img src="' . esc_html($options[$name . '_image']['url']) . '" alt="">' : '';
    $label = (isset($options[$name . '_prefix']) && !empty($options[$name . '_prefix'])) ? '<label>' . esc_html($options[$name . '_prefix']) . '</label>' : '';

    switch ($name) {
        case 'post_title':
            return the_title('<h2 class="entry-title"><a href="' . esc_url(get_permalink()) . '" rel="bookmark">', '</a></h2>', false);
            break;
        case 'post_summary':
            return '<div class="entry-summary">' . get_the_excerpt() . '</div>';
            break;
        case 'post_content':
            return '<div class="entry-content">' . get_the_content('') . '</div>';
            break;
        case 'post_thumbnail':
            ob_start();
            azexo_post_thumbnail_field();
            return '<div class="entry-thumbnail">' . ob_get_clean() . '</div>';
            break;
        case 'post_video':
            ob_start();
            azexo_post_video_field();
            return '<div class="entry-video">' . ob_get_clean() . '</div>';
            break;
        case 'post_gallery':
            ob_start();
            azexo_post_gallery_field();
            return '<div class="entry-gallery">' . ob_get_clean() . '</div>';
            break;
        case 'post_sticky':
            if (is_sticky() && is_home() && !is_paged())
                return '<span class="featured-post">' . esc_html__('Sticky', 'medican') . '</span>';
            break;
        case 'post_splitted_date':
            return azexo_entry_splitted_date(false);
            break;
        case 'post_date':
            return azexo_entry_date(false);
            break;
        case 'post_category':
            $categories_list = azexo_get_the_category_list('<span class="delimiter">,</span> ');
            if ($categories_list) {
                return '<span class="categories-links">' . (isset($options['post_category_prefix']) ? '<span class="label">' . esc_html($options['post_category_prefix']) : '') . '</span>' . $categories_list . '</span>';
            }
            break;
        case 'post_tags':
            $tag_list = get_the_tag_list('', '<span class="delimiter">,</span> ');
            if ($tag_list) {
                return '<span class="tags-links">' . (isset($options['post_tags_prefix']) ? '<span class="label">' . esc_html($options['post_tags_prefix']) : '') . '</span>' . $tag_list . '</span>';
            }
            break;
        case 'post_author':
            return sprintf('<span class="author vcard">' . (isset($options['post_author_prefix']) ? '<span class="label">' . esc_html($options['post_author_prefix']) : '') . '</span>' . '<a class="url fn n" href="%1$s" title="%2$s" rel="author">%3$s</a></span>', esc_url(get_author_posts_url(get_the_author_meta('ID'))), esc_attr(sprintf(esc_html__('View all posts by %s', 'medican'), get_the_author())), get_the_author());
            break;
        case 'post_author_avatar':
            return '<span class="avatar">' . get_avatar(get_the_author_meta('ID')) . sprintf('<span class="author vcard"><a class="url fn n" href="%1$s" title="%2$s" rel="author">%3$s</a></span>', esc_url(get_author_posts_url(get_the_author_meta('ID'))), esc_attr(sprintf(esc_html__('View all posts by %s', 'medican'), get_the_author())), get_the_author()) . '</span>';
            break;
        case 'post_like':
            return '<span class="like">' . get_simple_likes_button(get_the_ID()) . '</span>';
            break;
        case 'post_last_comment':
            $args = array(
                'post_id' => get_the_ID(),
                'number' => '1',
            );
            $comments = get_comments($args); //get_comments have caching
            $comment = reset($comments);
            if ($comment) {
                return '<div class="last-comment">' . esc_html(azexo_comment_excerpt($comment->comment_content)) . '</div>';
            }
            break;
        case 'post_last_comment_author':
            $args = array(
                'post_id' => get_the_ID(),
                'number' => '1',
            );
            $comments = get_comments($args); //get_comments have caching
            $comment = reset($comments);
            if ($comment) {
                return '<div class="last-comment-author">' . $label . '<a href="' . esc_attr($comment->comment_author_url) . '">' . esc_html($comment->comment_author) . '</a></div>';
            }
            break;
        case 'post_last_comment_date':
            $args = array(
                'post_id' => get_the_ID(),
                'number' => '1',
            );
            $comments = get_comments($args); //get_comments have caching
            $comment = reset($comments);
            if ($comment) {
                return '<div class="last-comment-date">' . azexo_comment_date(false, $comment) . '</div>';
            }
            break;
        case 'post_comments_count':
            $comment_count = get_comment_count(get_the_ID());
            $comments = '<a href="' . esc_url(get_comments_link()) . '"><span class="count">' . $comment_count['total_comments'] . '</span><span class="label">' . esc_html__('comments', 'medican') . '</span></a>';
            return '<span class="comments">' . $comments . '</span>';
            break;
        case 'post_read_more':
            $more_link_text = sprintf(esc_html__('Read more', 'medican'));
            return '<div class="entry-more">' . apply_filters('the_content_more_link', ' <a href="' . esc_url(get_permalink()) . "#more-" . get_the_ID() . "\" class=\"more-link\">" . $more_link_text . "</a>", $more_link_text) . '</div>';
            break;
        case 'post_share':
            ob_start();
            azexo_entry_share();
            return '<div class="entry-share">' . '<div class="helper">' . (isset($options['post_share_prefix']) ? esc_html($options['post_share_prefix']) : '') . '</div>' . ob_get_clean() . '</div>';
            break;
        case 'post_comments':
            ob_start();
            if (comments_open()) {
                comments_template();
            }
            return ob_get_clean();
            break;
        default:
            if (isset($options['meta_fields']) && in_array($name, array_filter($options['meta_fields']))) {
                $value = trim(get_post_meta(get_the_ID(), $name, true));
                if (!empty($value)) {
                    return '<span class="meta-field ' . str_replace('_', '-', strtolower($name)) . '">' . $image . ' ' . $label . ' <span class="value">' . $value . (isset($options[$name . '_suffix']) ? ' <span class="units">' . esc_html($options[$name . '_suffix']) . '</span>' : '') . '</span>' . '</span>';
                }
            } else {
                $taxonomies = get_taxonomies();
                $slug = str_replace('taxonomy_', '', $name);
                if (in_array($slug, $taxonomies)) {
                    $term_list = get_the_term_list(0, $slug, '', '<span class="delimiter">,</span> ', '');
                    if ($term_list) {
                        $term_list = trim($term_list);
                        if (!empty($term_list)) {
                            return '<span class="' . str_replace('_', '-', $slug) . ' taxonomy">' . $image . ' ' . $label . ' <span class="links">' . $term_list . '</span></span>';
                        }
                    }
                }

                return '';
            }
            break;
    }
    return '';
}

function azexo_entry_meta($template_name = 'post', $place = 'meta') {
    $options = get_option(AZEXO_THEME_NAME);
    $meta = '';
    if (isset($options[$template_name . '_' . $place]) && is_array($options[$template_name . '_' . $place])) {
        foreach ($options[$template_name . '_' . $place] as $field) {
            $meta .= azexo_entry_field($field);
        }
    }
    return $meta;
}

function azexo_entry_share() {
    global $post;
    $image = null;
    if (is_object($post)) {
        $image = wp_get_attachment_url(get_post_thumbnail_id($post->ID));
    }
    print '<a target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=' . rawurlencode(esc_url(apply_filters('the_permalink', get_permalink()))) . '"><span class="share-box"><i class="fa fa-facebook"></i></span></a>';
    print '<a target="_blank" href="https://twitter.com/home?status=' . rawurlencode('Check out this article: ') . rawurlencode(get_the_title()) . '%20-%20' . rawurlencode(esc_url(apply_filters('the_permalink', get_permalink()))) . '"><span class="share-box"><i class="fa fa-twitter"></i></span></a>';
    if (!empty($image)) {
        print '<a target="_blank" href="https://pinterest.com/pin/create/button/?url=' . rawurlencode(esc_url(apply_filters('the_permalink', get_permalink()))) . '&media=' . rawurlencode($image) . '&description=' . rawurlencode(get_the_title()) . '"><span class="share-box"><i class="fa fa-pinterest"></i></span></a>';
    }
    print '<a target="_blank" href="http://www.linkedin.com/shareArticle?mini=true&url=' . rawurlencode(esc_url(apply_filters('the_permalink', get_permalink()))) . '&title=' . rawurlencode(get_the_title()) . '&source=LinkedIn"><span class="share-box"><i class="fa fa-linkedin"></i></span></a>';
    print '<a target="_blank" href="https://plus.google.com/share?url=' . rawurlencode(esc_url(apply_filters('the_permalink', get_permalink()))) . '"><span class="share-box"><i class="fa fa-google-plus"></i></span></a>';
    if (comments_open() && !is_single() && !is_page()) {
        $comments = '<span class="share-box"><i class="fa fa-comment-o"></i></span>';
        comments_popup_link($comments, $comments, $comments, '', '');
    }
}

function azexo_entry_splitted_date($echo = true) {

    $date = '<div class="date"><div class="day">' . get_the_date('d') . '</div><div class="month">' . get_the_date('M') . '</div><div class="year">' . get_the_date('Y') . '</div></div>';

    if ($echo)
        print $date;

    return $date;
}

function azexo_entry_date($echo = true, $post = null) {
    if (has_post_format(array('chat', 'status'), $post))
        $format_prefix = _x('%1$s on %2$s', '1: post format name. 2: date', 'medican');
    else
        $format_prefix = '%2$s';

    $options = get_option(AZEXO_THEME_NAME);
    $date = sprintf('<span class="date">' . (isset($options['post_date_prefix']) ? esc_html($options['post_date_prefix']) : '') . '<a href="%1$s" title="%2$s" rel="bookmark"><time class="entry-date" datetime="%3$s">%4$s</time></a></span>', esc_url(get_permalink($post)), esc_attr(sprintf(esc_html__('Permalink to %s', 'medican'), the_title_attribute(array('echo' => false, 'post' => $post)))), esc_attr(get_the_date('c', $post)), esc_html(sprintf($format_prefix, get_post_format_string(get_post_format($post)), get_the_date('', $post)))
    );

    if ($echo) {
        print $date;
    }

    return $date;
}

function azexo_comment_date($echo = true, $comment = null) {

    $format_prefix = '%2$s';

    $options = get_option(AZEXO_THEME_NAME);
    $date = sprintf('<span class="date">' . (isset($options['post_date_prefix']) ? esc_html($options['post_date_prefix']) : '') . '<a href="%1$s" title="%2$s" rel="bookmark"><time class="comment-date" datetime="%3$s">%4$s</time></a></span>', esc_url(get_comment_link($comment)), esc_attr(sprintf(esc_html__('Permalink to %s', 'medican'), the_title_attribute(array('echo' => false, 'post' => $comment)))
            ), esc_attr(get_comment_date('c', $comment->comment_ID)), esc_html(sprintf($format_prefix, get_post_format_string(get_post_format($comment)), get_comment_date('', $comment->comment_ID))
            )
    );

    if ($echo) {
        print $date;
    }

    return $date;
}

function azexo_the_attached_image() {
    $attachment_size = apply_filters('azexo_attachment_size', array(724, 724));
    $next_attachment_url = wp_get_attachment_url();
    $post = get_post();

    $attachment_ids = get_posts(array(
        'post_parent' => $post->post_parent,
        'fields' => 'ids',
        'numberposts' => -1,
        'post_status' => 'inherit',
        'post_type' => 'attachment',
        'post_mime_type' => 'image',
        'order' => 'ASC',
        'orderby' => 'menu_order ID'
    ));

    // If there is more than 1 attachment in a gallery...
    if (count($attachment_ids) > 1) {
        foreach ($attachment_ids as $attachment_id) {
            if ($attachment_id == $post->ID) {
                $next_id = current($attachment_ids);
                break;
            }
        }

        // get the URL of the next image attachment...
        if ($next_id)
            $next_attachment_url = get_attachment_link($next_id);

        // or get the URL of the first image attachment.
        else
            $next_attachment_url = get_attachment_link(array_shift($attachment_ids));
    }

    printf('<a href="%1$s" title="%2$s" rel="attachment">%3$s</a>', esc_url($next_attachment_url), the_title_attribute(array('echo' => false)), wp_get_attachment_image($post->ID, $attachment_size)
    );
}

function azexo_entry_gallery($attachment_ids, $carousel, $thumbnails, $img_size, $vertical = 0) {
    $output = '';
    azexo_add_image_size($img_size);
    $size = azexo_get_image_sizes($img_size);
    if ($carousel) {
        wp_enqueue_script('azexo-owl.carousel');
        wp_enqueue_style('azexo-owl.carousel');
    } else {
        wp_enqueue_style('azexo-flexslider');
        wp_enqueue_script('azexo-flexslider');
    }
    $output .= '<div class="images ' . ($thumbnails ? 'thumbnails' : '') . ' ' . ($carousel ? 'carousel' : '') . '" data-width="' . esc_attr($size['width']) . '" data-height="' . esc_attr($size['height']) . '" data-vertical="' . esc_attr($vertical) . '">';
    foreach ($attachment_ids as $attachment_id) {
        $image_url = $attachment_id;
        if (is_numeric($attachment_id)) {
            $image_url = azexo_get_attachment_thumbnail($attachment_id, $img_size, true);
            $image_url = $image_url[0];
        }
        if (!empty($image_url)) {
            if ($img_size == 'full') {
                $output .= '<img class="image" src="' . esc_url($image_url) . '" alt="">';
            } else {
                $output .= '<div class="image" style=\'background-image: url("' . esc_url($image_url) . '"); height: ' . esc_attr($size['height']) . 'px;\'></div>';
            }
        }
    }
    $output .= "</div><!-- images -->\n";
    return $output;
}

function azexo_breadcrumbs() {
    /* === OPTIONS === */
    $text['home'] = esc_html__('Home', 'medican'); // text for the 'Home' link
    $text['category'] = esc_html__('Archive by Category "%s"', 'medican'); // text for a category page
    $text['tax'] = esc_html__('Archive for "%s"', 'medican'); // text for a taxonomy page
    $text['search'] = esc_html__('Search Results for "%s" Query', 'medican'); // text for a search results page
    $text['tag'] = esc_html__('Posts Tagged "%s"', 'medican'); // text for a tag page
    $text['author'] = esc_html__('Articles Posted by %s', 'medican'); // text for an author page
    $text['404'] = esc_html__('Error 404', 'medican'); // text for the 404 page

    $showCurrent = 1; // 1 - show current post/page title in breadcrumbs, 0 - don't show
    $showOnHome = 1; // 1 - show breadcrumbs on the homepage, 0 - don't show
    $delimiter = ' &raquo; '; // delimiter between crumbs
    $before = '<span class="current">'; // tag before the current crumb
    $after = '</span>'; // tag after the current crumb
    /* === END OF OPTIONS === */

    global $post;
    $linkBefore = '<span typeof="v:Breadcrumb">';
    $linkAfter = '</span>';
    $linkAttr = ' rel="v:url" property="v:title"';
    $link = $linkBefore . '<a' . $linkAttr . ' href="%1$s">%2$s</a>' . $linkAfter;

    if (is_home() || is_front_page()) {

        if ($showOnHome == 1)
            echo '<div id="crumbs"><a href="' . esc_url(home_url('/')) . '">' . $text['home'] . '</a></div>';
    } else {

        echo '<div id="crumbs" xmlns:v="http://rdf.data-vocabulary.org/#">' . sprintf($link, esc_url(home_url('/')), $text['home']) . $delimiter;


        if (is_category()) {
            $thisCat = get_category(get_query_var('cat'), false);
            if ($thisCat->parent != 0) {
                $cats = get_category_parents($thisCat->parent, TRUE, $delimiter);
                $cats = str_replace('<a', $linkBefore . '<a' . $linkAttr, $cats);
                $cats = str_replace('</a>', '</a>' . $linkAfter, $cats);
                print $cats;
            }
            print $before . sprintf($text['category'], single_cat_title('', false)) . $after;
        } elseif (is_tax()) {
            $thisCat = get_category(get_query_var('cat'), false);
            if ($thisCat->parent != 0) {
                $cats = get_category_parents($thisCat->parent, TRUE, $delimiter);
                $cats = str_replace('<a', $linkBefore . '<a' . $linkAttr, $cats);
                $cats = str_replace('</a>', '</a>' . $linkAfter, $cats);
                print $cats;
            }
            print $before . sprintf($text['tax'], single_cat_title('', false)) . $after;
        } elseif (is_search()) {
            print $before . sprintf($text['search'], get_search_query()) . $after;
        } elseif (is_day()) {
            echo sprintf($link, get_year_link(get_the_time('Y')), get_the_time('Y')) . $delimiter;
            echo sprintf($link, get_month_link(get_the_time('Y'), get_the_time('m')), get_the_time('F')) . $delimiter;
            print $before . get_the_time('d') . $after;
        } elseif (is_month()) {
            echo sprintf($link, get_year_link(get_the_time('Y')), get_the_time('Y')) . $delimiter;
            print $before . get_the_time('F') . $after;
        } elseif (is_year()) {
            print $before . get_the_time('Y') . $after;
        } elseif (is_single() && !is_attachment()) {
            if (get_post_type() != 'post') {
                $post_type = get_post_type_object(get_post_type());
                $slug = $post_type->rewrite;
                printf($link, esc_url(home_url('/')) . $slug['slug'] . '/', $post_type->labels->singular_name);
                if ($showCurrent == 1)
                    print $delimiter . $before . get_the_title() . $after;
            } else {
                $cat = get_the_category();
                $cat = $cat[0];
                $cats = get_category_parents($cat, TRUE, $delimiter);
                if ($showCurrent == 0)
                    $cats = preg_replace("#^(.+)$delimiter$#", "$1", $cats);
                $cats = str_replace('<a', $linkBefore . '<a' . $linkAttr, $cats);
                $cats = str_replace('</a>', '</a>' . $linkAfter, $cats);
                print $cats;
                if ($showCurrent == 1)
                    print $before . get_the_title() . $after;
            }
        } elseif (!is_single() && !is_page() && get_post_type() != 'post' && !is_404()) {
            $post_type = get_post_type_object(get_post_type());
            print $before . $post_type->labels->singular_name . $after;
        } elseif (is_attachment()) {
            $parent = get_post($post->post_parent);
            $cat = get_the_category($parent->ID);
            $cat = $cat[0];
            $cats = get_category_parents($cat, TRUE, $delimiter);
            $cats = str_replace('<a', $linkBefore . '<a' . $linkAttr, $cats);
            $cats = str_replace('</a>', '</a>' . $linkAfter, $cats);
            print $cats;
            printf($link, esc_url(get_permalink($parent)), $parent->post_title);
            if ($showCurrent == 1)
                print $delimiter . $before . get_the_title() . $after;
        } elseif (is_page() && !$post->post_parent) {
            if ($showCurrent == 1)
                print $before . get_the_title() . $after;
        } elseif (is_page() && $post->post_parent) {
            $parent_id = $post->post_parent;
            $breadcrumbs = array();
            while ($parent_id) {
                $page = get_page($parent_id);
                $breadcrumbs[] = sprintf($link, esc_url(get_permalink($page->ID)), get_the_title($page->ID));
                $parent_id = $page->post_parent;
            }
            $breadcrumbs = array_reverse($breadcrumbs);
            for ($i = 0; $i < count($breadcrumbs); $i++) {
                print $breadcrumbs[$i];
                if ($i != count($breadcrumbs) - 1)
                    print $delimiter;
            }
            if ($showCurrent == 1)
                print $delimiter . $before . get_the_title() . $after;
        } elseif (is_tag()) {
            print $before . sprintf($text['tag'], single_tag_title('', false)) . $after;
        } elseif (is_author()) {
            global $author;
            $userdata = get_userdata($author);
            print $before . sprintf($text['author'], $userdata->display_name) . $after;
        } elseif (is_404()) {
            print $before . $text['404'] . $after;
        }

        if (get_query_var('paged')) {
            if (is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author())
                echo ' (';
            echo esc_html__('Page', 'medican') . ' ' . get_query_var('paged');
            if (is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author())
                echo ')';
        }

        echo '</div>';
    }
}

function azexo_get_link_url() {
    $content = get_the_content();
    $has_url = get_url_in_content($content);
    return ( $has_url ) ? $has_url : apply_filters('the_permalink', get_permalink());
}

function azexo_get_image_sizes($size = '') {

    global $_wp_additional_image_sizes;

    $sizes = array();
    $get_intermediate_image_sizes = get_intermediate_image_sizes();

    // Create the full array with sizes and crop info
    foreach ($get_intermediate_image_sizes as $_size) {

        if (in_array($_size, array('thumbnail', 'medium', 'large'))) {

            $sizes[$_size]['width'] = get_option($_size . '_size_w');
            $sizes[$_size]['height'] = get_option($_size . '_size_h');
            $sizes[$_size]['crop'] = (bool) get_option($_size . '_crop');
        } elseif (isset($_wp_additional_image_sizes[$_size])) {

            $sizes[$_size] = array(
                'width' => $_wp_additional_image_sizes[$_size]['width'],
                'height' => $_wp_additional_image_sizes[$_size]['height'],
                'crop' => $_wp_additional_image_sizes[$_size]['crop']
            );
        }
    }

    // Get only 1 size if found
    if ($size) {

        if (isset($sizes[$size])) {
            return $sizes[$size];
        } else {
            return false;
        }
    }

    return $sizes;
}

function azexo_add_image_size($size) {
    if (!has_image_size($size) && !in_array($size, array('thumb', 'thumbnail', 'medium', 'large', 'post-thumbnail'))) {
        $size_array = explode('x', $size);
        if (count($size_array) == 2) {
            add_image_size($size, $size_array[0], $size_array[1], true);
        }
    }
}

function azexo_get_attachment_thumbnail($attachment_id, $size, $url = false) {
    azexo_add_image_size($size);

    $metadata = wp_get_attachment_metadata($attachment_id);
    if (is_array($metadata)) {
        $regenerate = true;
        if (!in_array($size, array('thumb', 'thumbnail', 'medium', 'large', 'post-thumbnail'))) {
            $size_array = explode('x', $size);
            if (count($size_array) == 2) {
                if (isset($metadata['width']) && isset($metadata['height'])) {
                    if ((intval($metadata['width']) < intval($size_array[0])) && (intval($metadata['height']) < intval($size_array[1]))) {
                        $regenerate = false;
                    }
                } else {
                    $regenerate = false;
                }
            } else {
                $regenerate = false;
            }
        }
        if ($regenerate && (!isset($metadata['sizes']) || !isset($metadata['sizes'][$size]))) {
            if (isset($metadata['sizes']) && is_array($metadata['sizes'])) {
                foreach ($metadata['sizes'] as $meta => $data) {
                    azexo_add_image_size($meta);
                }
            }
            require_once(ABSPATH . 'wp-admin/includes/image.php');
            require_once(ABSPATH . 'wp-admin/includes/post.php');
            wp_update_attachment_metadata($attachment_id, wp_generate_attachment_metadata($attachment_id, get_attached_file($attachment_id)));
            $metadata = wp_get_attachment_metadata($attachment_id);
        }
    }
    if ($url) {
        $image = wp_get_attachment_image_src($attachment_id, $size);
        if (empty($image)) {
            $image = wp_get_attachment_image_src($attachment_id, 'full');
        }
        return $image;
    } else {
        $image = wp_get_attachment_image($attachment_id, $size);
        if (empty($image)) {
            $image = wp_get_attachment_image_src($attachment_id, 'full');
        }
        return $image;
    }
}

function azexo_get_the_post_thumbnail($post_id, $size, $url = false) {
    azexo_add_image_size($size);
    $post_thumbnail_id = get_post_thumbnail_id($post_id);
    if (empty($post_thumbnail_id)) {
        if ($url) {
            
        } else {
            
        }
    }
    return azexo_get_attachment_thumbnail($post_thumbnail_id, $size, $url);
}

function azexo_get_attachment_image_src($attachment_id, $size) {
    return azexo_get_attachment_thumbnail($attachment_id, $size, true);
}

function azexo_strip_first_shortcode($content, $first_shortcode) {
    preg_match_all('/' . get_shortcode_regex() . '/s', $content, $matches, PREG_SET_ORDER);
    if (!empty($matches)) {
        foreach ($matches as $shortcode) {
            if ($first_shortcode === $shortcode[2]) {
                $pos = strpos($content, $shortcode[0]);
                if ($pos !== false)
                    return substr_replace($content, '', $pos, strlen($shortcode[0]));
            }
        }
    }
    return $content;
}

function azexo_get_first_shortcode($content, $first_shortcode) {
    preg_match_all('/' . get_shortcode_regex() . '/s', $content, $matches, PREG_SET_ORDER);
    if (!empty($matches)) {
        foreach ($matches as $shortcode) {
            if ($first_shortcode === $shortcode[2]) {
                $pos = strpos($content, $shortcode[0]);
                if ($pos !== false)
                    return $shortcode[0];
            }
        }
    }
    return false;
}

function azexo_get_search_form($echo = true) {
    $result = '<div class="search-wrapper">';
    $result .= get_search_form(false);
    $result .= '<i class="fa fa-search"></i></div>';
    if ($echo) {
        print $result;
    } else {
        return $result;
    }
}

function azexo_unparse_url(array $parsed) {
    $scheme = & $parsed['scheme'];
    $host = & $parsed['host'];
    $port = & $parsed['port'];
    $user = & $parsed['user'];
    $pass = & $parsed['pass'];
    $path = & $parsed['path'];
    $query = & $parsed['query'];
    $fragment = & $parsed['fragment'];

    $userinfo = !strlen($pass) ? $user : "$user:$pass";
    $host = !"$port" ? $host : "$host:$port";
    $authority = !strlen($userinfo) ? $host : "$userinfo@$host";
    $hier_part = !strlen($authority) ? $path : "//$authority$path";
    $url = !strlen($scheme) ? $hier_part : "$scheme:$hier_part";
    $url = !strlen($query) ? $url : "$url?$query";
    $url = !strlen($fragment) ? $url : "$url#$fragment";

    return $url;
}

add_filter('embed_oembed_html', 'azexo_embed_oembed_html', 10, 4);

function azexo_embed_oembed_html($html, $url, $attr, $post_ID) {
    if (preg_match('/src="([^"]*)"/', $html, $matches)) {
        $oembed_src = parse_url($matches[1]);
        $user_src = parse_url($url);
        if (isset($user_src['query'])) {
            $oembed_src['query'] = $user_src['query'];
        }        
        //$html = preg_replace('/src="[^"]*"/', 'src="' . htmlentities(esc_url(azexo_unparse_url($oembed_src))) . '"', $html); //HTML5 query url fix
        $html = preg_replace('/src="[^"]*"/', 'src="' . esc_url(azexo_unparse_url($oembed_src)) . '"', $html);
    }
    $html = str_replace(array('frameborder="0"', 'webkitallowfullscreen', 'mozallowfullscreen', 'allowfullscreen'), '', $html);
    return $html;
}

function azexo_display_select_tree($term, $selected = '', $level = 0) {
    if (is_object($term)) {
        if (!empty($term->children)) {
            echo '<option value="" disabled>' . str_repeat('&nbsp;&nbsp;', $level) . '' . $term->name . '</option>';
            $level++;
            foreach ($term->children as $key => $child) {
                azexo_display_select_tree($child, $selected, $level);
            }
        } else {
            echo '<option value="' . $term->slug . '" ' . ( $term->slug == $selected ? 'selected="selected"' : '' ) . '>' . str_repeat('&nbsp;&nbsp;', $level) . '' . $term->name . '</option>';
        }
    }
}

function azexo_array_filter_recursive($input) {
    foreach ($input as &$value) {
        if (is_array($value)) {
            $value = azexo_array_filter_recursive($value);
        }
    }
    return array_filter($input);
}

function azexo_build_link_attributes($link) {
    $attributes = ' ';
    if (isset($link['url']) && !empty($link['url'])) {
        $attributes .= 'href="' . htmlentities(esc_url($link['url'])) . '" ';
    }
    if (isset($link['title']) && !empty($link['title'])) {
        $attributes .= 'title="' . esc_attr($link['title']) . '" ';
    }
    if (isset($link['target']) && !empty($link['target'])) {
        $attributes .= 'target="' . esc_attr($link['target']) . '" ';
    }
    return $attributes;
}

function azexo_time_left($date_to) {
    if (!empty($date_to)) {
        $expire = $date_to - current_time('timestamp');
        if ($expire < 0)
            $expire = 0;
        $days = floor($expire / 60 / 60 / 24);
        $hours = floor(($expire - $days * 60 * 60 * 24) / 60 / 60);
        $minutes = floor(($expire - $days * 60 * 60 * 24 - $hours * 60 * 60) / 60);
        $seconds = $expire - $days * 60 * 60 * 24 - $hours * 60 * 60 - $minutes * 60;
        wp_enqueue_script('azexo-countdown');
        ?>
        <div class="time-left">
            <div class="time" data-time="<?php print date('Y/m/d H:i:s', $date_to); ?>">
                <div class="days"><span class="count"><?php print $days; ?></span><span class="title"><?php print esc_html__('day', 'medican'); ?></span></div>
                <div class="hours"><span class="count"><?php print $hours; ?></span><span class="title"><?php print esc_html__('hrs', 'medican'); ?></span></div>
                <div class="minutes"><span class="count"><?php print $minutes; ?></span><span class="title"><?php print esc_html__('min', 'medican'); ?></span></div>
                <div class="seconds"><span class="count"><?php print $seconds; ?></span><span class="title"><?php print esc_html__('sec', 'medican'); ?></span></div>
            </div>
        </div>
        <?php
    }
}

function azexo_current_time_ordering_filter($args, $query) {
    if (isset($query->query['orderby'])) {
        if ($query->query['orderby'] == 'meta_value') {
            if (isset($query->meta_query) && isset($query->meta_query->meta_table)) {
                if ($query->query['order'] == 'DESC') {
                    $args['where'] .= " AND ( " . esc_sql($query->meta_query->meta_table) . ".meta_value <= unix_timestamp())  ";
                } else {
                    $args['where'] .= " AND ( " . esc_sql($query->meta_query->meta_table) . ".meta_value >= unix_timestamp())  ";
                }
            }
        }
    }
    return $args;
}

function azexo_current_user_author_filter($args, $query) {
    global $wpdb;

    $args['where'] .= " AND ( $wpdb->posts.post_author = " . esc_sql(get_current_user_id()) . ") ";

    return $args;
}

add_filter('comment_form_fields', 'azexo_comment_form_fields');

function azexo_comment_form_fields($comment_fields) {
    $comment = $comment_fields['comment'];
    unset($comment_fields['comment']);
    $comment_fields = $comment_fields + array('comment' => $comment);
    return $comment_fields;
}

function azexo_is_template_part_exists($slug, $name = null) {
    $templates = array();
    $name = (string) $name;
    if ('' !== $name)
        $templates[] = "{$slug}-{$name}.php";

    $templates[] = "{$slug}.php";

    return locate_template($templates);
}

add_filter('infinite_scroll_js_options', 'azexo_infinite_scroll_js_options');

function azexo_infinite_scroll_js_options($options) {
    $options['nextSelector'] = 'nav.navigation .loop-pagination a.next';
    $options['navSelector'] = 'nav.navigation .loop-pagination';
    $options['itemSelector'] = '#content > .entry.post';
    $options['contentSelector'] = '#content.infinite-scroll';
    $options['loading']['img'] = get_template_directory_uri() . "/images/infinitescroll-loader.svg";
    $options['loading']['msgText'] = '<em class="infinite-scroll-loading">' . esc_html__('Loading ...', 'medican') . '</em>';
    $options['loading']['finishedMsg'] = '<em class="infinite-scroll-done">' . esc_html__('Done', 'medican') . '</em>';
    return $options;
}
