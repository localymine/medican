<?php
$options = get_option(AZEXO_THEME_NAME);
?>

<?php if (is_404()) : ?>
    <div class="page-header">
        <h1 class="page-title"><?php esc_html_e('Not Found', 'medican'); ?></h1>
    </div>
<?php elseif (is_category()): ?>
    <div class="archive-header">
        <h1 class="archive-title"><?php echo single_cat_title('', false); ?></h1>
        <div class="archive-subtitle"><?php echo esc_html__('Category archives', 'medican'); ?></div>
        <?php if (category_description()) : // Show an optional category description ?>
            <div class="archive-meta"><?php echo category_description(); ?></div>
        <?php endif; ?>
    </div><!-- .archive-header -->
<?php elseif (is_tag()): ?>
    <div class="archive-header">
        <h1 class="archive-title"><?php echo single_tag_title('', false); ?></h1>
        <div class="archive-subtitle"><?php echo esc_html__('Tag archives', 'medican'); ?></div>
        <?php if (tag_description()) : // Show an optional tag description  ?>
            <div class="archive-meta"><?php echo tag_description(); ?></div>
        <?php endif; ?>
    </div><!-- .archive-header -->
<?php elseif (is_archive()): ?>
    <div class="archive-header">
        <h1 class="archive-title">
            <?php
            if (is_day()) :
                echo get_the_date();
            elseif (is_month()) :
                echo get_the_date(_x('F Y', 'monthly archives date format', 'medican'));
            elseif (is_year()) :
                echo get_the_date(_x('Y', 'yearly archives date format', 'medican'));
            else :
                if (function_exists('is_shop') && is_shop()) {
                    if(isset($options['shop_title'])) {
                        print esc_html($options['shop_title']);
                    } else {
                        esc_html_e('Shop', 'medican');
                    }                    
                } else {
                    esc_html_e('Archives', 'medican');
                }
            endif;
            ?>
        </h1>
        <?php if (is_day() || is_month() || is_year()) : ?>
            <div class="archive-subtitle">
                <?php
                if (is_day()) :
                    esc_html_e('Daily Archives', 'medican');
                elseif (is_month()) :
                    esc_html_e('Monthly Archives', 'medican');
                elseif (is_year()) :
                    esc_html_e('Yearly Archives', 'medican');
                endif;
                ?>
            </div>
        <?php endif; ?>
    </div><!-- .archive-header -->
<?php elseif (is_search()): ?>
    <div class="page-header">
        <h1 class="page-title"><?php echo esc_html__('Search Results for', 'medican'); ?></h1>
        <div class="page-subtitle"><?php echo get_search_query(); ?></div>
    </div>
<?php elseif (is_page()): ?>
    <div class="page-header">
        <h1 class="page-title">
            <?php
            $post = get_post();
            if ($post) {
                print esc_html($post->post_title);
            }
            ?>
        </h1>
        <?php if (isset($options['show_breadcrumbs']) && $options['show_breadcrumbs']): ?>
            <div class="page-subtitle">
                <?php azexo_breadcrumbs(); ?>
            </div>        
        <?php endif; ?>
    </div>
<?php elseif (is_single()): ?>
    <?php if (isset($options['post_page_title']) && !empty($options['post_page_title'])) { ?>
        <div class="page-header">
            <?php print azexo_entry_field($options['post_page_title']); ?>
        </div>
    <?php } ?>
<?php else: ?>
    <div class="page-header">
        <h1 class="page-title">
            <?php
            print isset($options['default_title']) ? esc_html($options['default_title']) : '';
            ?>
        </h1>
    </div>
<?php endif; ?>
