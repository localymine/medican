<?php /*
  YARPP Template: Thumbnails
  Description: Requires a theme which supports post thumbnails
  Author: mitcho (Michael Yoshitaka Erlewine)
 */ ?>

<?php
$options = get_option(AZEXO_THEME_NAME);
$template_name = 'related_post';
$thumbnail_size = isset($options[$template_name . '_thumbnail_size']) && !empty($options[$template_name . '_thumbnail_size']) ? $options[$template_name . '_thumbnail_size'] : 'large';
azexo_add_image_size($thumbnail_size);
$size = azexo_get_image_sizes($thumbnail_size);
wp_enqueue_script('azexo-owl.carousel');
wp_enqueue_style('azexo-owl.carousel');
?>
<?php if (have_posts()): ?>
    <div class="related-posts">
        <h3><?php esc_html_e('Related posts', 'medican') ?></h3>
        <div class="owl-carousel posts-list <?php print str_replace('_', '-', $template_name); ?>" data-width="<?php print $size['width']; ?>" data-height="<?php print $size['height']; ?>" data-margin="<?php print $options['related_posts_carousel_margin']; ?>" data-center="yes">
            <?php while (have_posts()) : the_post(); ?>
                <div class="item">
                    <?php
                    include(locate_template('content.php'));
                    ?>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
<?php endif; ?>
