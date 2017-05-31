<?php get_header(); ?>

<div class="container">
    <div id="primary" class="content-area">
        <div id="content" class="site-content" role="main">

            <?php $options = get_option(AZEXO_THEME_NAME); if($options['show_page_title']) get_template_part('template-parts/general', 'title') ?>
            <div class="page-wrapper">
                <div class="page-content">
                    <h2><?php esc_html_e('This is somewhat embarrassing, isn&rsquo;t it?', 'medican'); ?></h2>
                    <p><?php esc_html_e('It looks like nothing was found at this location.', 'medican'); ?></p>
                </div><!-- .page-content -->
            </div><!-- .page-wrapper -->

        </div><!-- #content -->
    </div><!-- #primary -->
</div>
<?php get_footer(); ?>