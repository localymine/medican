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
$options = get_option(AZEXO_THEME_NAME);
if (!isset($show_sidebar)) {
    $show_sidebar = isset($options[get_post_type() . '_show_sidebar']) ? $options[get_post_type() . '_show_sidebar'] : 'right';
    if ($show_sidebar == 'hidden') {
        $show_sidebar = false;
    }
}
get_header('shop');
?>

<div class="container <?php print (is_active_sidebar('shop') && $show_sidebar ? 'active-sidebar ' . esc_attr($show_sidebar) : ''); ?>">
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
        <div id="content" class="site-content" role="main">
            <?php
            /**
             * woocommerce_before_main_content hook
             *
             * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
             * @hooked woocommerce_breadcrumb - 20
             */
            do_action('woocommerce_before_main_content');
            ?>

            <?php while (have_posts()) : the_post(); ?>

                <?php wc_get_template_part('content', 'single-product'); ?>

            <?php endwhile; // end of the loop. ?>

            <?php
            /**
             * woocommerce_after_main_content hook
             *
             * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
             */
            do_action('woocommerce_after_main_content');
            ?>
        </div><!-- #content -->
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
