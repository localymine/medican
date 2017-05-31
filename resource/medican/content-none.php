<?php
$options = get_option(AZEXO_THEME_NAME);
if (!isset($template_name)) {
    $template_name = 'post';
}
$default_post_template = isset($options['default_post_template']) ? $options['default_post_template'] : 'post';

if ($template_name == 'masonry_post') {
    wp_enqueue_script('masonry');
}
?>

<div <?php post_class(array('entry', 'no-results', 'not-found')); ?>>
    <div class="entry-data">
        <div class="entry-content">
            <?php if (is_home() && current_user_can('publish_posts')) : ?>

                <p><?php printf(wp_kses(__('Ready to publish your first post? <a href="%1$s">Get started here</a>.', 'medican'), array('a' => array('href' => array()))), esc_url(admin_url('post-new.php'))); ?></p>

            <?php elseif (is_search()) : ?>

                <h2><?php esc_html_e('Sorry, but nothing matched your search terms.', 'medican'); ?></h2>
                <p><?php esc_html_e('Please try again with some different keywords.', 'medican'); ?></p>

            <?php else : ?>

                <h2><?php esc_html_e('It seems we can&rsquo;t find what you&rsquo;re looking for.', 'medican'); ?></h2>
                <p><?php esc_html_e('Perhaps searching can help.', 'medican'); ?></p>

            <?php endif; ?>
        </div><!-- .entry-content -->
    </div>    
</div><!-- #post -->
