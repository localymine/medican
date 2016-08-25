<?php
$options = get_option(AZEXO_THEME_NAME);
$more_link_text = sprintf(esc_html__('Read more', 'medican'));
if (!isset($template_name)) {
    $template_name = apply_filters('azexo_template_name', 'post');
}
$default_post_template = isset($options['default_post_template']) ? $options['default_post_template'] : 'post';
$thumbnail_size = isset($options[$template_name . '_thumbnail_size']) && !empty($options[$template_name . '_thumbnail_size']) ? $options[$template_name . '_thumbnail_size'] : 'large';
$image_thumbnail = isset($options[$template_name . '_image_thumbnail']) ? $options[$template_name . '_image_thumbnail'] : false;

if ($template_name == 'masonry_post') {
    wp_enqueue_script('masonry');
}
?>

<div <?php post_class(array('entry', str_replace('_', '-', $template_name))); ?>>
    <?php if (isset($options[$template_name . '_show_thumbnail']) && $options[$template_name . '_show_thumbnail']) : ?>
        <?php if (!post_password_required() && !is_attachment()) : ?>
            <?php if (has_post_format('gallery') && !$image_thumbnail) : ?>
                <div class="entry-gallery">
                    <?php
                    azexo_post_gallery_field($template_name);
                    ?>
                    <?php
                    $hover = azexo_entry_meta($template_name, 'hover');
                    ?>
                    <?php if (!empty($hover)) : ?>
                        <div class="entry-hover">
                            <?php print $hover; ?>
                        </div>
                    <?php endif; ?>
                    <?php print azexo_entry_meta($template_name, 'thumbnail'); ?>
                </div>
            <?php elseif (has_post_format('video') && !$image_thumbnail) : ?>
                <div class="entry-video">
                    <?php
                    azexo_post_video_field();
                    ?>
                    <?php
                    $hover = azexo_entry_meta($template_name, 'hover');
                    ?>
                    <?php if (!empty($hover)) : ?>
                        <div class="entry-hover">
                            <?php print $hover; ?>
                        </div>
                    <?php endif; ?>
                    <?php print azexo_entry_meta($template_name, 'thumbnail'); ?>
                </div>
            <?php else: ?>
                <?php if (has_post_thumbnail()) : ?>
                    <div class="entry-thumbnail">
                        <?php
                        azexo_post_thumbnail_field($template_name);
                        ?>                
                        <?php
                        $hover = azexo_entry_meta($template_name, 'hover');
                        ?>
                        <?php if (!empty($hover)) : ?>
                            <div class="entry-hover">
                                <?php print $hover; ?>
                            </div>
                        <?php endif; ?>
                        <?php print azexo_entry_meta($template_name, 'thumbnail'); ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        <?php endif; ?>
    <?php endif; ?>
    <div class="entry-data">
        <div class="entry-header">
            <?php
            $extra = azexo_entry_meta($template_name, 'extra');
            ?>
            <?php if (!empty($extra)) : ?>
                <div class="entry-extra">
                    <?php print $extra; ?>
                </div>
            <?php endif; ?>
            <?php
            if (isset($options[$template_name . '_show_title']) && $options[$template_name . '_show_title']) {
                if (is_single() && $template_name == $default_post_template) :
                    the_title('<h2 class="entry-title">', '</h2>');
                else :
                    the_title('<h2 class="entry-title"><a href="' . esc_url(get_permalink()) . '" rel="bookmark">', '</a></h2>');
                endif;
            }
            ?>
            <?php
            $meta = azexo_entry_meta($template_name, 'meta');
            ?>
            <?php if (!empty($meta)) : ?>
                <div class="entry-meta">
                    <?php print $meta; ?>
                </div>
            <?php endif; ?>
            <?php print azexo_entry_meta($template_name, 'header'); ?>
        </div>


        <?php if (isset($options[$template_name . '_show_content']) && $options[$template_name . '_show_content'] != 'hidden'): ?>
            <?php if (is_search() || $options[$template_name . '_show_content'] == 'excerpt') :  ?>
                <?php
                $summary = azexo_excerpt(apply_filters('the_excerpt', get_the_excerpt()), isset($options[$template_name . '_excerpt_length']) ? $options[$template_name . '_excerpt_length'] : false, isset($options[$template_name . '_excerpt_words_trim']) ? $options[$template_name . '_excerpt_words_trim'] : true );
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
                if (!get_post_format() || has_post_format('gallery') || has_post_format('video')) {
                    if (has_post_format('gallery')) {
                        if (isset($options[$template_name . '_more_inside_content']) && $options[$template_name . '_more_inside_content'])
                            $content = azexo_strip_first_shortcode(get_the_content($more_link_text), 'gallery');
                        else
                            $content = azexo_strip_first_shortcode(get_the_content(''), 'gallery');
                    } elseif (has_post_format('video')) {
                        if (isset($options[$template_name . '_more_inside_content']) && $options[$template_name . '_more_inside_content'])
                            $content = azexo_strip_first_shortcode(get_the_content($more_link_text), 'embed');
                        else
                            $content = azexo_strip_first_shortcode(get_the_content(''), 'embed');
                    } else {
                        if (isset($options[$template_name . '_more_inside_content']) && $options[$template_name . '_more_inside_content'])
                            $content = get_the_content($more_link_text);
                        else
                            $content = get_the_content('');
                    }
                    $content = str_replace(']]>', ']]&gt;', apply_filters('the_content', $content));
                } else {
                    if (isset($options[$template_name . '_more_inside_content']) && $options[$template_name . '_more_inside_content']) {
                        ob_start();
                        the_content($more_link_text);
                        $content = ob_get_clean();
                    } else {
                        ob_start();
                        the_content('');
                        $content = ob_get_clean();
                    }
                }
                $content = trim($content);
                ?>
                <?php if (!empty($content)) : ?>
                    <div class="entry-content">
                        <?php
                        print $content;
                        wp_link_pages(array(
                            'before' => '<div class="page-links"><span class="page-links-title">' . esc_html__('Pages:', 'medican') . '</span>',
                            'after' => '</div>',
                            'link_before' => '<span>',
                            'link_after' => '</span>',
                        ));
                        ?>
                    </div>
                <?php endif; ?>        
            <?php endif; ?>        
        <?php endif; ?>

        <?php
        $footer = azexo_entry_meta($template_name, 'footer');
        ?>
        <?php if (!empty($footer)) : ?>
            <div class="entry-footer">
                <?php print $footer; ?>
            </div>
        <?php endif; ?>

        <?php print azexo_entry_meta($template_name, 'data'); ?>
    </div>    
    <?php
    $additions = azexo_entry_meta($template_name, 'additions');
    ?>
    <?php if (!empty($additions)) : ?>
        <div class="entry-additions">
            <?php print $additions; ?>
        </div>
    <?php endif; ?>
</div>
