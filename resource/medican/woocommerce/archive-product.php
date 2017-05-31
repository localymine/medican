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
$options = get_option(AZEXO_THEME_NAME);
if (!isset($show_sidebar)) {
    $show_sidebar = isset($options['show_sidebar']) ? $options['show_sidebar'] : 'right';
}
if (!isset($product_template)) {
    $product_template = isset($options['default_product_template']) ? $options['default_product_template'] : 'shop_product';
}
if (isset($_GET['template'])) {
    $product_template = $_GET['template'];
}
get_header('shop');
?>

<div class="container <?php print (is_active_sidebar('shop') && $show_sidebar ? 'active-sidebar ' . esc_attr($show_sidebar)  : ''); ?>">
    <?php
    /**
     * woocommerce_sidebar hook
     *
     * @hooked woocommerce_get_sidebar - 10
     */
    if ($show_sidebar == 'left') {
        do_action('woocommerce_sidebar');
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
            <header class="page-header">
                <h1 class="page-title"><?php woocommerce_page_title(); ?></h1>
            </header>            
        <?php endif; ?>
        <div id="content" class="site-content <?php print str_replace('_', '-', $product_template); ?> <?php print ((isset($options['infinite_scroll']) && $options['infinite_scroll']) ? 'infinite-scroll' : '') ?>" role="main">


            <?php do_action('woocommerce_archive_description'); ?>

            <?php
            if (have_posts()) :
                global $wp_query;
                $post_type = isset($wp_query->query['post_type']) ? $wp_query->query['post_type'] : 'post';
                if (is_array($post_type)) {
                    $post_type = $post_type[0];
                }
                update_meta_cache($post_type, array_keys($wp_query->posts));
                ?>

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

                <?php woocommerce_product_loop_start(); ?>

                <?php woocommerce_product_subcategories(); ?>

                <?php while (have_posts()) : the_post(); ?>

                    <?php
                    $located = wc_locate_template('content-product.php');
                    if (file_exists($located)) {
                        include( $located );
                    }
                    ?>

                <?php endwhile; // end of the loop.  ?>

                <?php woocommerce_product_loop_end(); ?>

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
    }
    ?>
</div>
<?php get_footer('shop'); ?>
