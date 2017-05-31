<?php
if (file_exists(get_template_directory() . '/azexo/woocommerce-deals.php')) {
    require_once(trailingslashit(get_template_directory()) . 'azexo/woocommerce-deals.php');
}


global $azwoo_templates;
$azwoo_templates = array(
    'single_product' => esc_html__('Single product', 'medican'), //default template
    'shop_product' => esc_html__('Shop product', 'medican'), //default template
    'detailed_shop_product' => esc_html__('Detailed shop product', 'medican'), //fixed in shop modes
    'related_product' => esc_html__('Related product', 'medican'), //fixed in related template
    'upsells_product' => esc_html__('Up-sells product', 'medican'), //fixed in up-sells template
);
add_filter('azexo_templates', 'azwoo_templates');

function azwoo_templates($azexo_templates) {
    global $azwoo_templates;
    return array_merge($azexo_templates, $azwoo_templates);
}

global $azwoo_fields;
$azwoo_fields = array(
    'purchased' => esc_html__('Product purchased', 'medican'),
    'discount' => esc_html__('Product discount', 'medican'),
    'sale_time_left' => esc_html__('Product sale time left', 'medican'),
    'availability' => esc_html__('Product availability', 'medican'),
    'last_review_rating' => esc_html__('Product last review rating', 'medican'),
    'loop_sale_flash' => esc_html__('Loop product sale flash', 'medican'),
    'loop_rating' => esc_html__('Loop product average rating', 'medican'),
    'loop_price' => esc_html__('Loop product price', 'medican'),
    'loop_add_to_cart' => esc_html__('Loop product add to cart link', 'medican'),
    'single_sale_flash' => esc_html__('Single product sale flash', 'medican'),
    'single_add_to_cart' => esc_html__('Single product add to cart', 'medican'),
    'single_rating' => esc_html__('Single product average rating', 'medican'),
    'single_price' => esc_html__('Single product price', 'medican'),
    'single_meta' => esc_html__('Single product meta', 'medican'),
    'single_sharing' => esc_html__('Single product sharing', 'medican'),
    'single_data_tabs' => esc_html__('Single data tabs', 'medican'),
    'single_related_products' => esc_html__('Single related products', 'medican'),
    'single_upsell_display' => esc_html__('Single upsell display', 'medican'),
    'single_description' => esc_html__('Single description', 'medican'),
    'single_reviews' => esc_html__('Single reviews', 'medican'),
    'single_additional_information' => esc_html__('Single additional information', 'medican'),
);

if (class_exists('WCV_Vendor_Shop')) {
    $azexo_fields['loop_sold_by'] = esc_html__('Loop product sold by', 'medican');
}
if (class_exists('YITH_WCWL_Shortcode')) {
    $azexo_fields['add_to_wishlist'] = esc_html__('Add product to wishlist', 'medican');
}
if (class_exists('YITH_WCQV_Frontend')) {
    $azexo_fields['quick_view'] = esc_html__('Product quick view', 'medican');
}

add_filter('azexo_fields', 'azwoo_fields');

function azwoo_fields($azexo_fields) {
    global $azwoo_fields;
    return array_merge($azexo_fields, $azwoo_fields);
}

add_filter('azexo_fields_post_types', 'azwoo_fields_post_types');

function azwoo_fields_post_types($azexo_fields_post_types) {
    global $azwoo_fields;
    $azexo_fields_post_types = array_merge($azexo_fields_post_types, array_combine(array_keys($azwoo_fields), array_fill(0, count(array_keys($azwoo_fields)), 'product')));
    return $azexo_fields_post_types;
}

add_action('after_setup_theme', 'azwoo_after_setup_theme');

function azwoo_after_setup_theme() {
    add_theme_support('woocommerce');
}

add_filter('azexo_settings_sections', 'azwoo_settings_sections');

function azwoo_settings_sections($sections) {
    $sections[] = array(
        'icon' => 'el-icon-cogs',
        'title' => esc_html__('WooCommerce templates configuration', 'medican'),
        'fields' => array(
            array(
                'id' => 'shop_title',
                'type' => 'text',
                'title' => esc_html__('Shop page title', 'medican'),
                'default' => 'Shop',
            ),
            array(
                'id' => 'product_label',
                'type' => 'text',
                'title' => esc_html__('Alter "Product" labels', 'medican'),
                'default' => 'Product',
            ),
            array(
                'id' => 'products_label',
                'type' => 'text',
                'title' => esc_html__('Alter "Products" labels', 'medican'),
                'default' => 'Products',
            ),
            array(
                'id' => 'custom_sorting',
                'type' => 'select',
                'multi' => true,
                'sortable' => true,
                'title' => esc_html__('Custom sorting', 'medican'),
                'options' => array(
                    'menu_order' => esc_html__('Default sorting', 'medican'),
                    'popularity' => esc_html__('Sort by popularity', 'medican'),
                    'rating' => esc_html__('Sort by average rating', 'medican'),
                    'date' => esc_html__('Sort by newness', 'medican'),
                    'price' => esc_html__('Sort by price: low to high', 'medican'),
                    'price-desc' => esc_html__('Sort by price: high to low', 'medican'),
                ),
                'default' => array('menu_order', 'popularity', 'rating', 'date', 'price', 'price-desc'),
            ),
            array(
                'id' => 'custom_sorting_numeric_meta_keys',
                'type' => 'multi_text',
                'title' => esc_html__('Custom sorting numeric meta keys', 'medican'),
            ),
            array(
                'id' => 'shop_modes',
                'type' => 'checkbox',
                'title' => esc_html__('Show shop modes', 'medican'),
                'default' => '0',
            ),
            array(
                'id' => 'show_data_tabs',
                'type' => 'checkbox',
                'title' => esc_html__('Show data tabs', 'medican'),
                'default' => '1',
            ),
            array(
                'id' => 'show_related_products',
                'type' => 'checkbox',
                'title' => esc_html__('Show related products', 'medican'),
                'default' => '1',
            ),
            array(
                'id' => 'show_upsells_products',
                'type' => 'checkbox',
                'title' => esc_html__('Show upsells products', 'medican'),
                'default' => '1',
            ),
            array(
                'id' => 'upsells_products_carousel_margin',
                'type' => 'text',
                'title' => esc_html__('Up-sells products carousel margin', 'medican'),
                'default' => '0',
            ),
            array(
                'id' => 'related_products_carousel_margin',
                'type' => 'text',
                'title' => esc_html__('Related products carousel margin', 'medican'),
                'default' => '0',
            ),
            array(
                'id' => 'review_marks',
                'type' => 'multi_text',
                'title' => esc_html__('Review marks', 'medican'),
            ),
            array(
                'id' => 'review_likes',
                'type' => 'checkbox',
                'title' => esc_html__('Review likes', 'medican'),
                'default' => '0',
            ),
        )
    );

    return $sections;
}

function azwoo_tgmpa_register() {

    $plugins = array(
        array(
            'name' => 'WooCommerce',
            'slug' => 'woocommerce',
            'required' => true,
        ),
        array(
            'name' => 'WC Vendors',
            'slug' => 'wc-vendors',
        ),
//        array(
//            'name' => 'YITH WooCommerce Quick View',
//            'slug' => 'yith-woocommerce-quick-view',
//        ),
//        array(
//            'name' => 'YITH WooCommerce Wishlist',
//            'slug' => 'yith-woocommerce-wishlist',
//        ),
    );
    tgmpa($plugins, array());
}

add_action('tgmpa_register', 'azwoo_tgmpa_register');

add_filter('woocommerce_enqueue_styles', '__return_false');
add_action('widgets_init', 'azwoo_widgets_init');

function azwoo_widgets_init() {
    if (function_exists('register_sidebar')) {
        register_sidebar(array('name' => 'Shop sidebar', 'id' => "shop", 'before_widget' => '<div id="%1$s" class="widget %2$s">', 'after_widget' => '</div>', 'before_title' => '<div class="widget-title"><h3>', 'after_title' => '</h3></div>'));
    }
}

add_action('init', 'azwoo_init', 11);

function azwoo_init() {
    $options = get_option(AZEXO_THEME_NAME);


    if (class_exists('WooCommerce')) {
        $lightbox = get_option('woocommerce_enable_lightbox');
        if ($lightbox == 'yes') {
            update_option('woocommerce_enable_lightbox', '');
        }

        remove_action('woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
        remove_action('woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);

        if (isset($options['show_breadcrumbs']) && !$options['show_breadcrumbs']) {
            remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20);
        }

        remove_action('woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10);
        remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5);
        remove_action('woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10);
        remove_action('woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10);

        remove_action('woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5);
        remove_action('woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10);

        remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10);

        remove_action('woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10);
        remove_action('woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20);

        remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_title', 5);
        remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10);
        remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_price', 10);
        remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20);
        remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30);
        remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40);
        remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 50);


        remove_action('woocommerce_before_subcategory', 'woocommerce_template_loop_category_link_open', 10);
        remove_action('woocommerce_before_subcategory_title', 'woocommerce_subcategory_thumbnail', 10);
        remove_action('woocommerce_after_subcategory', 'woocommerce_template_loop_category_link_close', 10);


        remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10);
        if (class_exists('Jetpack_Widget_Conditions')) {
            remove_filter('sidebars_widgets', array('Jetpack_Widget_Conditions', 'sidebars_widgets')); //FIX is_active_widget wrong result
        }
        if (is_active_widget(false, false, 'azwoo_related_products') || (isset($options['show_related_products']) && !$options['show_related_products'])) {
            remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20);
        }
        if (is_active_widget(false, false, 'azwoo_upsell_products') || (isset($options['show_upsells_products']) && !$options['show_upsells_products'])) {
            remove_action('woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15);
        }
    }

    if (class_exists('WCV_Vendor_Shop')) {
        remove_action('woocommerce_after_shop_loop_item', array('WCV_Vendor_Shop', 'template_loop_sold_by'), 9);

        remove_filter('post_type_archive_link', array('WCV_Vendor_Shop', 'change_archive_link')); //function make infinite recursion
        add_filter('admin_init', array('WCV_Vendor_Shop', 'change_archive_link')); //FIX from https://wordpress.org/support/topic/nesting-level
    }
    if (class_exists('YITH_WCWL_Shortcode')) {
        $position = get_option('yith_wcwl_button_position');
        if ($position != 'shortcode') {
            update_option('yith_wcwl_button_position', 'shortcode');
        }
    }
}

add_filter('woocommerce_register_post_type_product', 'azwoo_register_post_type_product');

function azwoo_register_post_type_product($args) {
    if (!is_admin()) {
        $options = get_option(AZEXO_THEME_NAME);
        if (isset($options['product_label']) && isset($options['products_label']) && !empty($options['product_label']) && !empty($options['products_label'])) {
            $args['labels']['name'] = $options['products_label'];
            $args['labels']['singular_name'] = $options['product_label'];
        }
    }
    return $args;
}

add_filter('woocommerce_taxonomy_args_product_cat', 'azwoo_taxonomy_args_product_cat');

function azwoo_taxonomy_args_product_cat($args) {
    if (!is_admin()) {
        $options = get_option(AZEXO_THEME_NAME);
        if (isset($options['product_label']) && !empty($options['product_label'])) {
            $args['label'] = $options['product_label'] . ' ' . esc_html__('Categories', 'medican');
            $args['labels']['name'] = $options['product_label'] . ' ' . esc_html__('Categories', 'medican');
            $args['labels']['singular_name'] = $options['product_label'] . ' ' . esc_html__('Category', 'medican');
        }
    }
    return $args;
}

add_filter('woocommerce_taxonomy_args_product_tag', 'azwoo_taxonomy_args_product_tag');

function azwoo_taxonomy_args_product_tag($args) {
    if (!is_admin()) {
        $options = get_option(AZEXO_THEME_NAME);
        if (isset($options['product_label']) && !empty($options['product_label'])) {
            $args['label'] = $options['product_label'] . ' ' . esc_html__('Tags', 'medican');
            $args['labels']['name'] = $options['product_label'] . ' ' . esc_html__('Tags', 'medican');
            $args['labels']['singular_name'] = $options['product_label'] . ' ' . esc_html__('Tag', 'medican');
        }
    }
    return $args;
}

add_action('wp_enqueue_scripts', 'azwoo_scripts');

function azwoo_scripts() {
    wp_register_script('azexo-woo', get_template_directory_uri() . '/js/azwoo.js', array('jquery', 'select2'), AZEXO_THEME_VERSION, true);
    wp_enqueue_script('azexo-woo');
}

add_action('wp_enqueue_scripts', 'azwoo_styles');

function azwoo_styles() {
    //move styles to header for HTML5 validation
    if (class_exists('WooCommerce')) {
        wp_enqueue_style('select2', str_replace(array('http:', 'https:'), '', WC()->plugin_url()) . '/assets/' . 'css/select2.css');
    }
    wp_enqueue_style('vc_linecons');
}

function azwoo_sale_time_left() {
    $sale_expire = get_post_meta(get_the_ID(), '_sale_price_dates_to', true);
    azexo_time_left($sale_expire);
}

add_action('after_setup_theme', 'azwoo_remove_quick_view_button');

function azwoo_remove_quick_view_button() {
    if (class_exists('YITH_WCQV_Frontend')) {
        remove_action('woocommerce_after_shop_loop_item', array(YITH_WCQV_Frontend(), 'yith_add_quick_view_button'), 15);

        remove_action('yith_wcqv_product_image', 'woocommerce_show_product_sale_flash', 10);
        remove_action('yith_wcqv_product_image', 'woocommerce_show_product_images', 20);
        remove_action('yith_wcqv_product_summary', 'woocommerce_template_single_title', 5);
        remove_action('yith_wcqv_product_summary', 'woocommerce_template_single_rating', 10);
        remove_action('yith_wcqv_product_summary', 'woocommerce_template_single_price', 15);
        remove_action('yith_wcqv_product_summary', 'woocommerce_template_single_excerpt', 20);
        remove_action('yith_wcqv_product_summary', 'woocommerce_template_single_add_to_cart', 25);
        remove_action('yith_wcqv_product_summary', 'woocommerce_template_single_meta', 30);
        add_action('yith_wcqv_product_summary', 'azwoo_product_summary');
    }
}

add_action('woocommerce_after_subcategory_title', 'azwoo_after_subcategory_title');

function azwoo_after_subcategory_title($category) {
    print '<div class="description">' . esc_html($category->description) . '</div>';
}

function azwoo_product_summary() {
    $located = wc_locate_template('content-product.php');
    if (file_exists($located)) {
        $product_template = 'single_product';
        $azwoo_base_tag = 'div';
        include( $located );
    }
}

add_filter('azexo_entry_field', 'azwoo_entry_field', 10, 2);

function azwoo_entry_field($output, $name) {
    global $product, $post;
    $options = get_option(AZEXO_THEME_NAME);
    switch ($name) {
        case 'purchased':
            return '<span class="purchased">' . esc_html(get_post_meta(get_the_ID(), 'total_sales', true)) . '</span>';
            break;
        case 'discount':
            if ($product->is_on_sale() && $product->get_regular_price() > 0) {
                $discount = round(($product->get_display_price() / $product->get_display_price($product->get_regular_price()) - 1) * 100);
                if (!empty($discount)) {
                    $discount = $discount . '%';
                    return '<span class="discount">' . $discount . '</span>';
                }
            }
            break;
        case 'sale_time_left':
            ob_start();
            azwoo_sale_time_left();
            return ob_get_clean();
            break;
        case 'availability':
            $availability = $product->get_availability();
            $availability_html = empty($availability['availability']) ? '' : '<p class="stock ' . esc_attr($availability['class']) . '">' . esc_html($availability['availability']) . '</p>';
            return apply_filters('woocommerce_stock_html', $availability_html, $availability['availability'], $product);
            break;
        case 'last_review_rating':
            $args = array(
                'post_id' => get_the_ID(),
                'number' => '1',
            );
            $comments = get_comments($args); //get_comments have caching
            $comment = reset($comments);
            if ($comment) {
                $rating = intval(get_comment_meta($comment->comment_ID, 'rating', true));
                if ($rating) {
                    return $product->get_rating_html($rating);
                }
            }
            break;
        case 'loop_sale_flash':
            ob_start();
            woocommerce_show_product_loop_sale_flash();
            return ob_get_clean();
            break;
        case 'loop_rating':
            ob_start();
            woocommerce_template_loop_rating();
            return ob_get_clean();
            break;
        case 'loop_price':
            ob_start();
            woocommerce_template_loop_price();
            return ob_get_clean();
            break;
        case 'loop_add_to_cart':
            ob_start();
            woocommerce_template_loop_add_to_cart();
            return ob_get_clean();
            break;
        case 'loop_sold_by':
            ob_start();
            WCV_Vendor_Shop::template_loop_sold_by($product->ID);
            return ob_get_clean();
            break;
        case 'single_sale_flash':
            ob_start();
            woocommerce_show_product_sale_flash();
            return ob_get_clean();
            break;
        case 'single_rating':
            ob_start();
            woocommerce_template_single_rating();
            return ob_get_clean();
            break;
        case 'single_price':
            ob_start();
            woocommerce_template_single_price();
            return ob_get_clean();
            break;
        case 'single_add_to_cart':
            ob_start();
            woocommerce_template_single_add_to_cart();
            return ob_get_clean();
            break;
        case 'single_meta':
            ob_start();
            woocommerce_template_single_meta();
            return ob_get_clean();
            break;
        case 'single_sharing':
            ob_start();
            woocommerce_template_single_sharing();
            return ob_get_clean();
            break;
        case 'single_data_tabs':
            ob_start();
            woocommerce_output_product_data_tabs();
            return ob_get_clean();
            break;
        case 'single_related_products':
            ob_start();
            woocommerce_output_related_products();
            return ob_get_clean();
            break;
        case 'single_upsell_display':
            ob_start();
            woocommerce_upsell_display();
            return ob_get_clean();
            break;
        case 'single_description':
            ob_start();
            woocommerce_product_description_tab();
            return ob_get_clean();
            break;
        case 'single_reviews':
            ob_start();
            if (comments_open()) {
                comments_template();
            }
            return ob_get_clean();
            break;
        case 'single_additional_information':
            ob_start();
            woocommerce_product_additional_information_tab();
            return ob_get_clean();
            break;
        case 'add_to_wishlist':
            if (class_exists('YITH_WCWL_Shortcode')) {
                return YITH_WCWL_Shortcode::add_to_wishlist(array());
            }
            break;
        case 'quick_view':
            if (class_exists('YITH_WCQV_Frontend')) {
                wp_enqueue_script('azexo-flexslider');
                ob_start();
                YITH_WCQV_Frontend()->yith_add_quick_view_button();
                return ob_get_clean();
            }
            break;
    }
    return $output;
}

function azwoo_get_images_links($thumbnail_size) {
    global $product;
    $images_links = array();
    if (has_post_thumbnail()) {
        $image_link = azexo_get_attachment_thumbnail(get_post_thumbnail_id(), $thumbnail_size, true);
        $images_links[] = $image_link[0];
    }
    $attachment_ids = $product->get_gallery_attachment_ids();
    if ($attachment_ids) {
        foreach ($attachment_ids as $attachment_id) {
            $image_link = azexo_get_attachment_thumbnail($attachment_id, $thumbnail_size, true);
            if (!empty($image_link))
                $images_links[] = $image_link[0];
        }
    }
    $images_links = array_unique($images_links);
    return $images_links;
}

add_filter('woocommerce_price_format', 'azwoo_price_format', 10, 2);

function azwoo_price_format($format, $currency_pos) {
    $format = '%1$s%2$s';

    switch ($currency_pos) {
        case 'left' :
            $format = '<span class="currency">%1$s</span>%2$s';
            break;
        case 'right' :
            $format = '%2$s<span class="currency">%1$s</span>';
            break;
        case 'left_space' :
            $format = '<span class="currency">%1$s</span>&nbsp;%2$s';
            break;
        case 'right_space' :
            $format = '%2$s&nbsp;<span class="currency">%1$s</span>';
            break;
    }
    return $format;
}

add_filter('woocommerce_product_categories_widget_args', 'azwoo_product_categories_widget_args');

function azwoo_product_categories_widget_args($list_args) {

    if (!class_exists('AZEXO_Product_Cat_List_Walker')) {

        class AZEXO_Product_Cat_List_Walker extends WC_Product_Cat_List_Walker {

            public function start_el(&$output, $cat, $depth = 0, $args = array(), $current_object_id = 0) {
                $output .= '<li class="cat-item cat-item-' . $cat->term_id;

                if ($args['current_category'] == $cat->term_id) {
                    $output .= ' current-cat';
                }

                if ($args['has_children'] && $args['hierarchical']) {
                    $output .= ' cat-parent';
                }

                if ($args['current_category_ancestors'] && $args['current_category'] && in_array($cat->term_id, $args['current_category_ancestors'])) {
                    $output .= ' current-cat-parent';
                }

                $thumbnail_id = get_woocommerce_term_meta($cat->term_id, 'thumbnail_id', true);
                $image = wp_get_attachment_thumb_url($thumbnail_id);
                if ($image) {
                    $image = '<img alt="" src="' . esc_url($image) . '">';
                }

                $output .= '"><a href="' . esc_url(get_term_link((int) $cat->term_id, 'product_cat')) . '">' . $image . '<span>' . $cat->name . '</span></a>';

                if ($args['show_count']) {
                    $output .= ' <span class="count">(' . $cat->count . ')</span>';
                }
            }

        }

    }

    $list_args['walker'] = new AZEXO_Product_Cat_List_Walker;
    return $list_args;
}

add_filter('azexo_post_template_path', 'azwoo_post_template_path', 10, 2);

function azwoo_post_template_path($template, $template_name) {
    global $azwoo_templates;
    global $post;
    if (in_array($template_name, array_keys($azwoo_templates)) || $post->post_type == 'product') {
        return array("content-product.php", WC()->template_path() . "content-product.php");
    } else {
        return $template;
    }
}

add_filter('azexo_posts_list_loop_args', 'azwoo_posts_list_loop_args');

function azwoo_posts_list_loop_args($loop_args) {
    if ($loop_args['post_type'] == 'product') {
        if (!isset($loop_args['meta_query'])) {
            $loop_args['meta_query'] = array();
        }
        $loop_args['meta_query'][] = array(
            'key' => '_visibility',
            'value' => array('visible', 'catalog'),
            'compare' => 'IN',
        );
    }

    return $loop_args;
}

function azwoo_order_by_rating_post_clauses($args, $query) {
    global $wpdb;

    $args['fields'] .= ", AVG( $wpdb->commentmeta.meta_value ) as average_rating ";

    $args['where'] .= " AND ( $wpdb->commentmeta.meta_key = 'rating' OR $wpdb->commentmeta.meta_key IS null ) ";

    $args['join'] .= "
			LEFT OUTER JOIN $wpdb->comments ON($wpdb->posts.ID = $wpdb->comments.comment_post_ID)
			LEFT JOIN $wpdb->commentmeta ON($wpdb->comments.comment_ID = $wpdb->commentmeta.comment_id)
		";

    $args['orderby'] = "average_rating DESC, $wpdb->posts.post_date DESC";

    $args['groupby'] = "$wpdb->posts.ID";

    return $args;
}

function azwoo_featured_post_clauses($args, $query) {
    global $wpdb;

    $args['join'] .= " INNER JOIN {$wpdb->postmeta} as mf ON $wpdb->posts.ID = mf.post_id ";

    $args['where'] .= " AND ( mf.meta_key = '_featured') AND ( mf.meta_value = 'yes' ) ";

    return $args;
}

add_filter('wp_nav_menu_objects', 'azwoo_wp_nav_menu_objects', 10, 2);

function azwoo_wp_nav_menu_objects($sorted_menu_items, $args) {
    $woocommerce_myaccount_page_id = get_option('woocommerce_myaccount_page_id');
    if (!is_user_logged_in()) {
        foreach ($sorted_menu_items as $i => &$menu_item) {
            if ($menu_item->object_id == $woocommerce_myaccount_page_id && $menu_item->object == 'page') {
                $menu_item->title = esc_html__('Login/Register', 'medican');
            }
        }
    }
    $remove_array = array();
    if (class_exists('WC_Vendors')) {
        $remove_array[] = WC_Vendors::$pv_options->get_option('vendor_dashboard_page');
        $remove_array[] = WC_Vendors::$pv_options->get_option('shop_settings_page');
        $remove_array[] = WC_Vendors::$pv_options->get_option('wcv_orders');
    }
    if (class_exists('WCV_Vendors')) {
        if (!WCV_Vendors::is_vendor(get_current_user_id())) {
            foreach ($sorted_menu_items as $i => &$menu_item) {
                if (in_array($menu_item->object_id, $remove_array) && $menu_item->object == 'page') {
                    unset($sorted_menu_items[$i]);
                    prev($sorted_menu_items);
                }
            }
        }
    }
    return $sorted_menu_items;
}

add_filter('azexo_menu_start_el', 'azwoo_menu_start_el', 10, 2);

function azwoo_menu_start_el($item, $args) {
    $woocommerce_cart_page_id = get_option('woocommerce_cart_page_id');
    if ($item->object_id == $woocommerce_cart_page_id && $item->object == 'page') {
        global $woocommerce;
        $item->classes[] = 'cart';
        $args->link_before = '<span class="fa fa-shopping-cart"></span><span class="count">' . $woocommerce->cart->cart_contents_count . '</span>';
    }
    if (is_array($item->classes)) {
        if (in_array('hot', $item->classes)) {
            $item->classes = array_diff($item->classes, array('hot'));
            $item->additions .= '<span class="hot">' . esc_html__('Hot', 'medican') . '</span>';
        }
        if (in_array('new', $item->classes)) {
            $item->classes = array_diff($item->classes, array('new'));
            $item->additions .= '<span class="new">' . esc_html__('New', 'medican') . '</span>';
        }
    }
    return $item;
}

if (class_exists('WPLessPlugin')) {
    $less = WPLessPlugin::getInstance();
    $options = get_option(AZEXO_THEME_NAME);
    if (isset($options['accent-1-color']))
        $less->addVariable('accent-1-color', $options['accent-1-color']);
    if (isset($options['accent-2-color']))
        $less->addVariable('accent-2-color', $options['accent-2-color']);
}


add_action('widgets_init', 'azwoo_register_widgets');

function azwoo_register_widgets() {
    register_widget('AZWOOBbreadcrumb');
    register_widget('AZWOORelatedProducts');
    register_widget('AZWOOUpsellProducts');
}

class AZWOOBbreadcrumb extends WP_Widget {

    function AZWOOBbreadcrumb() {
        parent::__construct('azwoo_breadcrumb', AZEXO_THEME_NAME . ' - WooCommerce breadcrumb');
    }

    function widget($args, $instance) {
        print '<div class="widget azwoo-breadcrumb">';
        woocommerce_breadcrumb();
        print '</div>';
    }

}

class AZWOORelatedProducts extends WP_Widget {

    function AZWOORelatedProducts() {
        parent::__construct('azwoo_related_products', AZEXO_THEME_NAME . ' - WooCommerce related products');
    }

    function widget($args, $instance) {
        if (is_product()) {
            woocommerce_output_related_products();
        }
    }

}

class AZWOOUpsellProducts extends WP_Widget {

    function AZWOOUpsellProducts() {
        parent::__construct('azwoo_upsell_products', AZEXO_THEME_NAME . ' - WooCommerce upsell products');
    }

    function widget($args, $instance) {
        if (is_product()) {
            woocommerce_upsell_display();
        }
    }

}

if (class_exists('WPBakeryShortCode') && function_exists('vc_map')) {

    class WPBakeryShortCode_woocommerce_breadcrumb extends WPBakeryShortCode {
        
    }

    vc_map(array(
        "name" => "Breadcrumb",
        "base" => "woocommerce_breadcrumb",
        'icon' => 'icon-wpb-woocommerce',
        'category' => esc_html__('WooCommerce', 'medican'),
        'show_settings_on_create' => false,
    ));

    class WPBakeryShortCode_azexo_product_search_form extends WPBakeryShortCode {
        
    }

    vc_map(array(
        "name" => "AZEXO - Product Search Form",
        "base" => "azexo_product_search_form",
        'category' => esc_html__('AZEXO', 'medican'),
        'show_settings_on_create' => false,
    ));

    class WPBakeryShortCode_woocommerce_cart_widget extends WPBakeryShortCode {
        
    }

    vc_map(array(
        'name' => esc_html__('Cart widget', 'medican'),
        'base' => 'woocommerce_cart_widget',
        'icon' => 'icon-wpb-woocommerce',
        'category' => esc_html__('WooCommerce', 'medican'),
        'description' => esc_html__('Displays the cart contents', 'medican'),
        'show_settings_on_create' => false,
        'params' => array(
            array(
                'type' => 'textfield',
                'heading' => esc_html__('Widget title', 'medican'),
                'param_name' => 'title',
                'description' => esc_html__('What text use as a widget title. Leave blank to use default widget title.', 'medican'),
                'value' => esc_html__('Cart', 'medican'),
            ),
            array(
                'type' => 'checkbox',
                'heading' => esc_html__('Hide if cart is empty', 'medican'),
                'param_name' => 'hide_if_empty',
                'value' => array(
                    esc_html__('Yes, please', 'medican') => 1,
                ),
            ),
            array(
                'type' => 'textfield',
                'heading' => esc_html__('Extra class name', 'medican'),
                'param_name' => 'el_class',
                'description' => esc_html__('If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'medican'),
            ),
        )
    ));

    class WPBakeryShortCode_azexo_product_fields_wrapper extends WPBakeryShortCodesContainer {
        
    }

    vc_map(array(
        "name" => "AZEXO - Product Fields Wrapper",
        "base" => "azexo_product_fields_wrapper",
        'category' => esc_html__('AZEXO', 'medican'),
        "as_parent" => array('except' => 'azexo_product_fields_wrapper'),
        "content_element" => true,
        "controls" => "full",
        "show_settings_on_create" => true,
        "is_container" => true,
        'params' => array(
            array(
                'type' => 'textfield',
                'heading' => esc_html__('Extra class name', 'medican'),
                'param_name' => 'el_class',
                'description' => esc_html__('If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'medican'),
            ),
            array(
                'type' => 'css_editor',
                'heading' => esc_html__('Css', 'medican'),
                'param_name' => 'css',
                'group' => esc_html__('Design options', 'medican'),
            ),
        ),
        "js_view" => 'VcColumnView'
    ));
}


add_action('woocommerce_before_shop_loop', 'azwoo_before_shop_loop', 10);

function azwoo_before_shop_loop() {
    $options = get_option(AZEXO_THEME_NAME);
    if (isset($options['shop_modes']) && $options['shop_modes']) {
        ?> 
        <div class="modes">
            <a class="mode <?php print (isset($_GET['template']) && $_GET['template'] == 'shop_product') ? 'active' : ''; ?>" href="<?php echo esc_url(add_query_arg(array('template' => 'shop_product'))); ?>">
                <i class="fa fa-th"></i>
            </a>
            <a class="mode <?php print (isset($_GET['template']) && $_GET['template'] == 'detailed_shop_product') ? 'active' : ''; ?>" href="<?php echo esc_url(add_query_arg(array('template' => 'detailed_shop_product'))); ?>">
                <i class="fa fa-th-list"></i>
            </a>
        </div>
        <?php
    }
}

add_filter('woocommerce_show_page_title', 'azwoo_show_page_title');

function azwoo_show_page_title() {
    $options = get_option(AZEXO_THEME_NAME);
    return isset($options['show_page_title']) && $options['show_page_title'];
}

add_filter('woocommerce_product_description_heading', 'azwoo_product_description_heading');

function azwoo_product_description_heading() {
    return false;
}

function azwoo_review_marks() {
    $options = get_option(AZEXO_THEME_NAME);
    if (isset($options['review_marks']) && is_array($options['review_marks'])) {
        $options['review_marks'] = array_filter($options['review_marks']);
        if (!empty($options['review_marks'])) {
            return array_combine(array_map('sanitize_title', $options['review_marks']), $options['review_marks']);
        }
    }
    return array();
}

add_action('comment_post', 'azwoo_comment_post');

function azwoo_comment_post($comment_id) {
    if ('product' === get_post_type($_POST['comment_post_ID'])) {
        $review_marks = azwoo_review_marks();
        if (!empty($review_marks)) {
            $rating = 0;
            foreach ($review_marks as $slug => $label) {
                if (isset($_POST[$slug])) {
                    if (!$_POST[$slug] || $_POST[$slug] > 5 || $_POST[$slug] < 0) {
                        continue;
                    }
                    add_comment_meta($comment_id, $slug, (int) esc_attr($_POST[$slug]), true);
                    $rating += (int) $_POST[$slug];
                }
            }
            $rating = number_format($rating / count($review_marks), 1);
            if ('yes' === get_option('woocommerce_enable_review_rating') && 'yes' !== get_option('woocommerce_review_rating_required')) {
                delete_comment_meta($comment_id, 'rating');
                add_comment_meta($comment_id, 'rating', $rating, true);
            }
        }
    }
}

add_filter('woocommerce_get_catalog_ordering_args', 'azwoo_get_catalog_ordering_args');

function azwoo_get_catalog_ordering_args($args) {
    $options = get_option(AZEXO_THEME_NAME);
    if (isset($options['custom_sorting_numeric_meta_keys']) && is_array($options['custom_sorting_numeric_meta_keys'])) {
        $orderby_value = isset($_GET['orderby']) ? woocommerce_clean($_GET['orderby']) : apply_filters('woocommerce_default_catalog_orderby', get_option('woocommerce_default_catalog_orderby'));
        foreach ($options['custom_sorting_numeric_meta_keys'] as $meta_key) {
            if ($meta_key . '-asc' == $orderby_value) {
                $args['orderby'] = 'meta_value_num';
                $args['order'] = 'ASC';
                $args['meta_key'] = $meta_key;
            }
            if ($meta_key . '-desc' == $orderby_value) {
                $args['orderby'] = 'meta_value_num';
                $args['order'] = 'DESC';
                $args['meta_key'] = $meta_key;
            }
        }
    }
    return $args;
}

add_filter('woocommerce_default_catalog_orderby_options', 'azwoo_catalog_orderby');
add_filter('woocommerce_catalog_orderby', 'azwoo_catalog_orderby');

function azwoo_catalog_orderby($sortby) {
    $options = get_option(AZEXO_THEME_NAME);
    if (isset($options['custom_sorting']) && is_array($options['custom_sorting'])) {
        $sortby = array_intersect_key($sortby, array_combine($options['custom_sorting'], $options['custom_sorting']));
    }
    if (isset($options['custom_sorting_numeric_meta_keys']) && is_array($options['custom_sorting_numeric_meta_keys'])) {
        foreach ($options['custom_sorting_numeric_meta_keys'] as $meta_key) {
            $sortby[esc_attr($meta_key) . '-desc'] = sprintf(esc_attr__('Sort by %s: high to low', 'medican'), esc_attr($meta_key));
            $sortby[esc_attr($meta_key) . '-asc'] = sprintf(esc_attr__('Sort by %s: low to high', 'medican'), esc_attr($meta_key));
        }
    }
    return $sortby;
}
