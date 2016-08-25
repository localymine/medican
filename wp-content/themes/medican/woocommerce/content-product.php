<?php
/**
 * The template for displaying product content within loops.
 *
 * Override this template by copying it to yourtheme/woocommerce/content-product.php
 *
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 10.0.0
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

global $product, $post, $woocommerce_loop;

if ( empty( $woocommerce_loop['loop'] ) ) {
	$woocommerce_loop['loop'] = 0;
}
if ( empty( $woocommerce_loop['columns'] ) ) {
	$woocommerce_loop['columns'] = apply_filters( 'loop_shop_columns', 4 );
}

if (!$product) {
    return;
}

$options = get_option(AZEXO_THEME_NAME);
if (!isset($product_template)) {
    if (isset($template_name)) {
        $product_template = $template_name;
    } else {
        $product_template = 'shop_product';
    }
}

if (isset($options[$product_template . '_is_visible']) && $options[$product_template . '_is_visible']) {
    if (!$product->is_visible()) {
        return;
    }
}

$woocommerce_loop['loop']++;

if (!isset($azwoo_base_tag))
    $azwoo_base_tag = 'li';
$single = ($product_template == 'single_product');
$more_link_text = sprintf(wp_kses(__('Read more<span class="meta-nav"> &rsaquo;</span>', 'medican'), array('span' => array('class' => array()))));
$thumbnail_size = isset($options[$product_template . '_thumbnail_size']) && !empty($options[$product_template . '_thumbnail_size']) ? $options[$product_template . '_thumbnail_size'] : 'large';
azexo_add_image_size($thumbnail_size);
$image_thumbnail = isset($options[$product_template . '_image_thumbnail']) ? $options[$product_template . '_image_thumbnail'] : false;

$images_links = azwoo_get_images_links($thumbnail_size);
$full_images_links = azwoo_get_images_links('full');

$size = azexo_get_image_sizes($thumbnail_size);
$zoom = isset($options[$product_template . '_zoom']) && !empty($options[$product_template . '_zoom']) ? 'zoom' : '';
$lazy = isset($options[$product_template . '_lazy']) && !empty($options[$product_template . '_lazy']) ? $options[$product_template . '_lazy'] : false;
if ($lazy) {
    wp_enqueue_script('azexo-waypoints');
}
?>
<<?php print $azwoo_base_tag; ?> <?php post_class(array(str_replace('_', '-', $product_template))); ?>>

<div class="entry" itemscope itemtype="<?php echo woocommerce_get_product_schema(); ?>">
    <meta itemprop="url" content="<?php print esc_url(get_permalink()); ?>" />
    <meta itemprop="image" content="<?php print esc_url(wp_get_attachment_url(get_post_thumbnail_id($post->ID))); ?>" />
    <?php
    if (!$single) {
        do_action('woocommerce_before_shop_loop_item');
    }
    ?>
    <?php if (isset($options[$product_template . '_show_thumbnail']) && $options[$product_template . '_show_thumbnail']): ?>
        <?php if ((count($images_links) > 1) && !$image_thumbnail): ?>
            <div class="entry-gallery">
                <div id="images-<?php the_ID(); ?>" 
                     class="images <?php print (isset($options[$product_template . '_gallery_slider_thumbnails']) && esc_attr($options[$product_template . '_gallery_slider_thumbnails']) ? 'thumbnails' : ''); ?> <?php print (isset($options[$product_template . '_show_carousel']) && esc_attr($options[$product_template . '_show_carousel']) ? 'carousel' : ''); ?>" 
                     data-width="<?php print esc_attr($size['width']); ?>" 
                     data-height="<?php print esc_attr($size['height']); ?>" 
                     data-vertical="<?php print esc_attr(isset($options[$product_template . '_gallery_slider_thumbnails_vertical']) && $options[$product_template . '_gallery_slider_thumbnails_vertical']); ?>">
                         <?php
                         if (isset($options[$product_template . '_show_carousel']) && esc_attr($options[$product_template . '_show_carousel'])) {
                             wp_enqueue_script('azexo-owl.carousel');
                             wp_enqueue_style('azexo-owl.carousel');
                             wp_enqueue_script('azexo-magnific-popup');
                             wp_enqueue_style('azexo-magnific-popup');
                         } else {
                             wp_enqueue_style('azexo-flexslider');
                             wp_enqueue_script('azexo-flexslider');
                         }
                         $i = 0;
                         foreach ($images_links as $image_link):
                             ?>                        
                             <?php if ($lazy) { ?>
                                 <?php if ($thumbnail_size == 'full') { ?>
                                <img class="image lazy" data-src="<?php print esc_url($image_link); ?>" alt="" data-popup="<?php print esc_url($full_images_links[$i]); ?>">
                            <?php } else { ?>
                                <div class="image lazy <?php print esc_attr($zoom); ?>" data-popup="<?php print esc_url($full_images_links[$i]); ?>" data-src="<?php print esc_url($image_link); ?>" style="height: <?php print esc_attr($size['height']); ?>px;">
                                </div>
                            <?php }; ?>
                        <?php } else { ?>
                            <?php if ($thumbnail_size == 'full') { ?>
                                <img class="image" src="<?php print esc_url($image_link); ?>" alt="" data-popup="<?php print esc_url($full_images_links[$i]); ?>">
                            <?php } else { ?>
                                <div class="image <?php print esc_attr($zoom); ?>" data-popup="<?php print esc_url($full_images_links[$i]); ?>" style='background-image: url("<?php print esc_url($image_link); ?>"); height: <?php print esc_attr($size['height']); ?>px;'>
                                </div>
                            <?php }; ?>
                        <?php }; ?>
                        <?php $i++;
                    endforeach;
                    ?>
                </div>
                <?php
                $hover = azexo_entry_meta($product_template, 'hover');
                ?>
                    <?php if (!empty($hover)): ?>
                    <div class="entry-hover">
                    <?php print $hover; ?>
                    </div>
                <?php endif; ?>
            <?php print azexo_entry_meta($product_template, 'thumbnail'); ?>
            </div>
        <?php else: ?>  
            <?php
            $url = azexo_get_the_post_thumbnail(get_the_ID(), $thumbnail_size, true);
            if ($url):
                ?>   
                <div class="entry-thumbnail">
                    <a href="<?php the_permalink(); ?>">
                        <?php if ($lazy) { ?>
                            <?php if ($thumbnail_size == 'full') { ?>
                                <img class="image lazy" data-src="<?php print esc_url($url[0]); ?>" alt="">
                <?php } else { ?>
                                <div class="image lazy <?php print esc_attr($zoom); ?>" data-src="<?php print esc_url($url[0]); ?>" style="height: <?php print esc_attr($size['height']); ?>px;" data-width="<?php print esc_attr($size['width']); ?>" data-height="<?php print esc_attr($size['height']); ?>">
                                </div>
                            <?php }; ?>
                        <?php } else { ?>
                            <?php if ($thumbnail_size == 'full') { ?>
                                <img class="image" src="<?php print esc_url($url[0]); ?>" alt="">
                <?php } else { ?>
                                <div class="image <?php print esc_attr($zoom); ?>" style='background-image: url("<?php print esc_url($url[0]); ?>"); height: <?php print esc_attr($size['height']); ?>px;' data-width="<?php print esc_attr($size['width']); ?>" data-height="<?php print esc_attr($size['height']); ?>">
                                </div>
                            <?php }; ?>
                    <?php }; ?>
                    </a>        
                    <?php
                    $hover = azexo_entry_meta($product_template, 'hover');
                    ?>
                        <?php if (!empty($hover)): ?>
                        <div class="entry-hover">
                        <?php print $hover; ?>
                        </div>
                    <?php endif; ?>
                <?php print azexo_entry_meta($product_template, 'thumbnail'); ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    <?php endif; ?>   
    <?php
    if ($single) {
        do_action('woocommerce_before_single_product_summary');
    }
    ?>
    <div class="entry-data">
        <div class="entry-header">
            <?php
            $extra = azexo_entry_meta($product_template, 'extra');
            if (!$single) {
                ob_start();
                do_action('woocommerce_before_shop_loop_item_title');
                $extra .= ob_get_clean();
            }
            $extra = trim($extra);
            ?>
                <?php if (!empty($extra)) : ?>
                <div class="entry-extra">
                <?php print $extra; ?>
                </div>
            <?php endif; ?>

            <?php
            if (isset($options[$product_template . '_show_title']) && $options[$product_template . '_show_title']) {
                if ($single) {
                    woocommerce_template_single_title();
                } else {
                    ?>
                    <a class="entry-title" href="<?php the_permalink(); ?>">
                        <h3 itemprop="name"><?php the_title(); ?></h3>
                    </a>                    
                    <?php
                }
            }
            ?>
            <?php
            $meta = azexo_entry_meta($product_template, 'meta');
            if (!$single) {
                ob_start();
                do_action('woocommerce_after_shop_loop_item_title');
                $meta .= ob_get_clean();
            }
            $meta = trim($meta);
            ?>
                <?php if (!empty($meta)) : ?>
                <div class="entry-meta">
                <?php print $meta; ?>
                </div>
            <?php endif; ?>

        <?php print azexo_entry_meta($product_template, 'header'); ?>
        </div>
        <?php if (isset($options[$product_template . '_show_content']) && $options[$product_template . '_show_content'] != 'hidden'): ?>
            <?php if ($options[$product_template . '_show_content'] == 'excerpt') : ?>
                <?php
                $summary = '';
                if ($single) {
                    ob_start();
                    woocommerce_template_single_excerpt();
                    $summary = ob_get_clean();
                } else {
                    $summary = azexo_excerpt(apply_filters('woocommerce_short_description', $post->post_excerpt), isset($options[$product_template . '_excerpt_length']) ? $options[$product_template . '_excerpt_length'] : false, isset($options[$product_template . '_excerpt_words_trim']) ? $options[$product_template . '_excerpt_words_trim'] : true);
                }
                $summary = trim($summary);
                ?>
                    <?php if (!empty($summary)) : ?>
                    <div class="entry-summary">
                    <?php print $summary; ?>
                    </div>
                <?php endif; ?>        
            <?php else : ?>
                <?php
                $content = '';
                if (isset($options[$product_template . '_more_inside_content']) && $options[$product_template . '_more_inside_content']) {
                    ob_start();
                    the_content($more_link_text);
                    $content = ob_get_clean();
                } else {
                    ob_start();
                    the_content('');
                    $content = ob_get_clean();
                }
                $content = trim($content);
                ?>                    
                    <?php if (!empty($content)) : ?>
                    <div class="entry-content">
                    <?php print $content; ?>
                    </div>
                <?php endif; ?>        
            <?php endif; ?>  
        <?php endif; ?>   

        <?php
        $footer = azexo_entry_meta($product_template, 'footer');
        ?>
            <?php if (!empty($footer)) : ?>
            <div class="entry-footer">
            <?php print $footer; ?>
            </div>
<?php endif; ?>


        <?php print azexo_entry_meta($product_template, 'data'); ?>
        <?php
        if ($single) {
            do_action('woocommerce_single_product_summary');
        }
        ?>
        <?php
        if (!$single) {
            do_action('woocommerce_after_shop_loop_item');
        }
        ?>
    </div>
    <?php
    $additions = azexo_entry_meta($product_template, 'additions');
    ?>
        <?php if (!empty($additions)) : ?>
        <div class="entry-additions">
        <?php print $additions; ?>
        </div>
<?php endif; ?>
</div>
</<?php print $azwoo_base_tag; ?>>
