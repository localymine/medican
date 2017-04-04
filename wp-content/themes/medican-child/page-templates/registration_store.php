<?php
/*
  Template Name: Store Registration template
 */
?>

<?php get_header(); $options = get_option(AZEXO_FRAMEWORK); ?>

    <div id="primary" class="content-area">
        <?php        
        if ($options['show_page_title']) {
            get_template_part('template-parts/general', 'title');
        }
        ?>
        <div id="content" class="site-content" role="main">
            <?php while (have_posts()) : the_post(); ?>
                <div id="post-<?php the_ID(); ?>" <?php post_class('entry'); ?>>
                    <div class="entry-content">
                         <div class="custom-registration-form  store_registration">
                             
                        <?php 
                       if(is_user_logged_in()) 
                        {
						$userid = get_current_user_id();
                        $caps = get_user_meta($userid, 'custom_capability', true);	
						if (strpos($caps, 'vendor') == false) {
							echo do_shortcode('[store_registration_form]');
						}
						else{
						wp_redirect( get_site_url());  
						}
                        } 
                        else {
                         wp_redirect( get_site_url());
                        }
                        ?>
                        </div>
                       
                        <?php the_content(); ?>
                        <?php wp_link_pages(array('before' => '<div class="page-links"><span class="page-links-title">' . esc_html__('Pages:', 'medican') . '</span>', 'after' => '</div>', 'link_before' => '<span>', 'link_after' => '</span>')); ?>
                    </div><!-- .entry-content -->
                </div><!-- #post -->
                <?php
                if (isset($options['comments']) && $options['comments']) {
                    if (comments_open()) {
                        comments_template();
                    }
                }
                ?>
            <?php endwhile; ?>
        </div><!-- #content -->
    </div><!-- #primary -->

<?php get_footer(); ?>