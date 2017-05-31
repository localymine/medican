<?php
/*
  ReduxFramework Config File
 */

if (!class_exists('AZEXO_Redux_Framework_config')) {

    class AZEXO_Redux_Framework_config {

        public $args = array();
        public $sections = array();
        public $theme;
        public $ReduxFramework;

        public function __construct() {

            if (!class_exists('ReduxFramework')) {
                return;
            }

            add_action('init', array($this, 'initSettings'), 11); // after woocommerce.php
        }

        public function initSettings() {
            $this->theme = wp_get_theme();
            $this->setArguments();
            $this->setSections();

            if (!isset($this->args['opt_name'])) { // No errors please
                return;
            }
            add_action('redux/loaded', array($this, 'remove_demo'));
            add_filter('redux/options/' . $this->args['opt_name'] . '/args', array($this, 'change_arguments'));
            $this->ReduxFramework = new ReduxFramework($this->sections, $this->args);
        }

        function change_arguments($args) {
            $args['dev_mode'] = false;

            return $args;
        }

        function remove_demo() {
            if (class_exists('ReduxFrameworkPlugin')) {
                remove_filter('plugin_row_meta', array(ReduxFrameworkPlugin::instance(), 'plugin_metalinks'), null, 2);
                remove_action('admin_notices', array(ReduxFrameworkPlugin::instance(), 'admin_notices'));
            }
        }

        public function setSections() {

            ob_start();

            $ct = wp_get_theme();
            $this->theme = $ct;
            $item_name = $this->theme->get('Name');
            $tags = $this->theme->Tags;
            $screenshot = $this->theme->get_screenshot();
            $class = $screenshot ? 'has-screenshot' : '';

            $customize_title = sprintf(esc_html__('Customize &#8220;%s&#8221;', 'medican'), $this->theme->display('Name'));
            ?>
            <div id="current-theme" class="<?php echo esc_attr($class); ?>">
                <?php if ($screenshot) : ?>
                    <?php if (current_user_can('edit_theme_options')) : ?>
                        <a href="<?php echo wp_customize_url(); ?>" class="load-customize hide-if-no-customize" title="<?php echo esc_attr($customize_title); ?>">
                            <img src="<?php echo esc_url($screenshot); ?>" alt="<?php esc_attr_e('Current theme preview', 'medican'); ?>" />
                        </a>
                    <?php endif; ?>
                    <img class="hide-if-customize" src="<?php echo esc_url($screenshot); ?>" alt="<?php esc_attr_e('Current theme preview', 'medican'); ?>" />
                <?php endif; ?>

                <h4><?php print esc_html($this->theme->display('Name')); ?></h4>

                <div>
                    <ul class="theme-info">
                        <li><?php printf(esc_html__('By %s', 'medican'), $this->theme->display('Author')); ?></li>
                        <li><?php printf(esc_html__('Version %s', 'medican'), $this->theme->display('Version')); ?></li>
                        <li><?php echo '<strong>' . esc_html__('Tags', 'medican') . ':</strong> '; ?><?php printf($this->theme->display('Tags')); ?></li>
                    </ul>
                    <p class="theme-description"><?php print esc_html($this->theme->display('Description')); ?></p>
                    <?php
                    if ($this->theme->parent()) {
                        printf(' <p class="howto">' . wp_kses(__('This <a href="%1$s">child theme</a> requires its parent theme, %2$s.', 'medican'), array('a' => array('href' => array()))) . '</p>', esc_html__('http://codex.wordpress.org/Child_Themes', 'medican'), $this->theme->parent()->display('Name'));
                    }
                    ?>

                </div>
            </div>

            <?php
            $item_info = ob_get_contents();

            ob_end_clean();

            $options = get_option(AZEXO_THEME_NAME);
            global $azexo_templates;
            if (!isset($azexo_templates)) {
                $azexo_templates = array();
            }
            $azexo_templates = array_merge($azexo_templates, array(
                'post' => esc_html__('Post', 'medican'), //default template
                'masonry_post' => esc_html__('Masonry post', 'medican'), //fixed selector in JS
                'related_post' => esc_html__('Related post', 'medican'), //fixed in YARP template
            ));

            if (isset($options['templates']) && is_array($options['templates'])) {
                $options['templates'] = array_filter($options['templates']);
                if (!empty($options['templates'])) {
                    $azexo_templates = array_merge($azexo_templates, array_combine(array_map('sanitize_title', $options['templates']), $options['templates']));
                }
            }

            $azexo_templates = apply_filters('azexo_templates', $azexo_templates);

            global $azexo_fields;
            if (!isset($azexo_fields)) {
                $azexo_fields = array();
            }
            global $azexo_post_fields;
            $azexo_post_fields = array(
                'post_title' => esc_html__('Post title', 'medican'),
                'post_summary' => esc_html__('Post summary', 'medican'),
                'post_content' => esc_html__('Post content', 'medican'),
                'post_thumbnail' => esc_html__('Post thumbnail', 'medican'),
                'post_video' => esc_html__('Post video', 'medican'),
                'post_gallery' => esc_html__('Post gallery', 'medican'),
                'post_sticky' => esc_html__('Post sticky', 'medican'),
                'post_date' => esc_html__('Post date', 'medican'),
                'post_splitted_date' => esc_html__('Post splitted date', 'medican'),
                'post_author' => esc_html__('Post author', 'medican'),
                'post_author_avatar' => esc_html__('Post author avatar', 'medican'),
                'post_category' => esc_html__('Post category', 'medican'),
                'post_tags' => esc_html__('Post tags', 'medican'),
                'post_like' => esc_html__('Post like', 'medican'),
                'post_last_comment' => esc_html__('Post last comment', 'medican'),
                'post_last_comment_author' => esc_html__('Post last comment author', 'medican'),
                'post_last_comment_date' => esc_html__('Post last comment date', 'medican'),
                'post_comments_count' => esc_html__('Post comments count', 'medican'),
                'post_read_more' => esc_html__('Post read more link', 'medican'),
                'post_share' => esc_html__('Post social share', 'medican'),
                'post_comments' => esc_html__('Post comments', 'medican'),
            );

            $azexo_fields = array_merge($azexo_fields, $azexo_post_fields);


            $field_templates = azexo_get_field_templates();
            $azexo_fields = array_merge($azexo_fields, $field_templates);


            $taxonomy_fields = array();
            $taxonomies = get_taxonomies(array(), 'objects');
            foreach ($taxonomies as $slug => $taxonomy) {
                $taxonomy_fields['taxonomy_' . $slug] = esc_html__('Taxonomy: ', 'medican') . $taxonomy->label;
            }
            $azexo_fields = array_merge($azexo_fields, $taxonomy_fields);

            $meta_fields = array();
            if (isset($options['meta_fields']) && is_array($options['meta_fields'])) {
                $options['meta_fields'] = array_filter($options['meta_fields']);
                if (!empty($options['meta_fields'])) {
                    $meta_fields = array_combine($options['meta_fields'], $options['meta_fields']);
                }
            }
            $azexo_fields = array_merge($azexo_fields, $meta_fields);


            $azexo_fields = apply_filters('azexo_fields', $azexo_fields);

            $vc_widgets = get_posts(array(
                'numberposts' => -1,
                'post_type' => 'vc_widget',
                'post_status' => 'publish',
                'orderby' => 'title',
                'order' => 'ASC',
            ));
            if (is_array($vc_widgets)) {
                foreach ($vc_widgets as $vc_widget) {
                    $azexo_fields[$vc_widget->ID] = $vc_widget->post_title . ' ' . esc_html__('VC Widget', 'medican');
                }
            }

            $general_settings_fields = array();
            $general_settings_fields[] = array(
                'id' => 'logo',
                'type' => 'media',
                'title' => esc_html__('Logo', 'medican'),
                'subtitle' => esc_html__('Upload any media using the WordPress native uploader', 'medican'),
                'required' => array('header', 'contains', 'logo')
            );
            if (class_exists('WPLessPlugin')) {
                $general_settings_fields[] = array(
                    'id' => 'brand-color',
                    'type' => 'color',
                    'title' => esc_html__('Brand color', 'medican'),
                    'validate' => 'color',
                    'default' => '#000',
                );
                $general_settings_fields[] = array(
                    'id' => 'accent-1-color',
                    'type' => 'color',
                    'title' => esc_html__('Accent 1 color', 'medican'),
                    'validate' => 'color',
                    'default' => '#000',
                );
                $general_settings_fields[] = array(
                    'id' => 'accent-2-color',
                    'type' => 'color',
                    'title' => esc_html__('Accent 2 color', 'medican'),
                    'validate' => 'color',
                    'default' => '#000',
                );
            }
            if (class_exists('Infinite_Scroll')) {
                $general_settings_fields[] = array(
                    'id' => 'infinite_scroll',
                    'type' => 'checkbox',
                    'title' => esc_html__('Infinite Scroll', 'medican'),
                    'default' => '0'
                );
            }
            $general_settings_fields[] = array(
                'id' => 'default_post_template',
                'type' => 'select',
                'title' => esc_html__('Default blog template', 'medican'),
                'options' => $azexo_templates,
                'default' => 'post',
            );
            global $azwoo_templates;
            if (isset($azwoo_templates)) {
                $general_settings_fields[] = array(
                    'id' => 'default_product_template',
                    'type' => 'select',
                    'title' => esc_html__('Default shop template', 'medican'),
                    'options' => $azexo_templates,
                    'default' => 'shop_product',
                );
            }
            $general_settings_fields[] = array(
                'id' => 'show_sidebar',
                'type' => 'select',
                'title' => esc_html__('Show sidebar', 'medican'),
                'options' => array(
                    'hidden' => esc_html__('Hidden', 'medican'),
                    'left' => esc_html__('Left side', 'medican'),
                    'right' => esc_html__('Right side', 'medican'),
                ),
                'default' => 'right',
            );
            $general_settings_fields[] = array(
                'id' => 'favicon',
                'type' => 'media',
                'title' => esc_html__('Favicon', 'medican'),
                'subtitle' => esc_html__('Upload any media using the WordPress native uploader', 'medican'),
            );
            $general_settings_fields[] = array(
                'id' => 'custom-css',
                'type' => 'ace_editor',
                'title' => esc_html__('CSS Code', 'medican'),
                'subtitle' => esc_html__('Paste your CSS code here.', 'medican'),
                'mode' => 'css',
                'theme' => 'monokai',
                'default' => "#header{\nmargin: 0 auto;\n}"
            );
            $general_settings_fields[] = array(
                'id' => 'custom-js',
                'type' => 'ace_editor',
                'title' => esc_html__('JS Code', 'medican'),
                'subtitle' => esc_html__('Paste your JS code here.', 'medican'),
                'mode' => 'javascript',
                'theme' => 'chrome',
                'default' => "jQuery(document).ready(function(){\n\n});"
            );

            $skins = azexo_get_skins();

            // ACTUAL DECLARATION OF SECTIONS
            $this->sections[] = array(
                'icon' => 'el-icon-cogs',
                'title' => esc_html__('General settings', 'medican'),
                'fields' => $general_settings_fields
            );

            $post_types = get_post_types(array('_builtin' => false, 'publicly_queryable' => true), 'objects');
            if (is_array($post_types) && !empty($post_types)) {
                $this->sections[] = array(
                    'icon' => 'el-icon-cogs',
                    'title' => esc_html__('Post types settings', 'medican'),
                );
                foreach ($post_types as $slug => $post_type) {
                    $this->sections[] = array(
                        'icon' => 'el-icon-cogs',
                        'subsection' => true,
                        'title' => $post_type->label,
                        'fields' => array(
                            array(
                                'id' => $slug . '_show_sidebar',
                                'type' => 'select',
                                'title' => esc_html__('Show sidebar', 'medican'),
                                'options' => array(
                                    'hidden' => esc_html__('Hidden', 'medican'),
                                    'left' => esc_html__('Left side', 'medican'),
                                    'right' => esc_html__('Right side', 'medican'),
                                ),
                                'default' => 'hidden',
                            )
                        )
                    );
                }
            }

            $header_parts = array(
                'logo' => esc_html__('Logo', 'medican'),
                'search' => esc_html__('Search', 'medican'),
                'primary_menu' => esc_html__('Primary menu', 'medican'),
                'secondary_menu' => esc_html__('Secondary menu', 'medican'),
                'mobile_menu_button' => esc_html__('Mobile menu button', 'medican'),
                'mobile_menu' => esc_html__('Mobile menu', 'medican'),
            );
            $files = scandir(get_template_directory() . '/template-parts');
            if (is_array($files)) {
                foreach ($files as $file) {
                    $matches = array();
                    if (preg_match('/header\-([\w\-]+)\.php/', $file, $matches)) {
                        $header_parts[$matches[1]] = $matches[0];
                    }
                }
            }

            $this->sections[] = array(
                'icon' => 'el-icon-cogs',
                'title' => esc_html__('Templates configuration', 'medican'),
                'fields' => array(
                    array(
                        'id' => 'skin',
                        'type' => 'select',
                        'title' => esc_html__('Select skin', 'medican'),
                        'options' => array_combine($skins, $skins),
                    ),
                    array(
                        'id' => 'header_sidebar_fullwidth',
                        'type' => 'checkbox',
                        'title' => esc_html__('Header sidebar fullwidth', 'medican'),
                        'default' => '1'
                    ),
                    array(
                        'id' => 'middle_sidebar_fullwidth',
                        'type' => 'checkbox',
                        'title' => esc_html__('Middle sidebar fullwidth', 'medican'),
                        'default' => '1'
                    ),
                    array(
                        'id' => 'footer_sidebar_fullwidth',
                        'type' => 'checkbox',
                        'title' => esc_html__('Footer sidebar fullwidth', 'medican'),
                        'default' => '0'
                    ),
                    array(
                        'id' => 'show_page_title',
                        'type' => 'checkbox',
                        'title' => esc_html__('Show page title in templates', 'medican'),
                        'default' => '0'
                    ),
                    array(
                        'id' => 'show_breadcrumbs',
                        'type' => 'checkbox',
                        'title' => esc_html__('Show breadcrumb in templates', 'medican'),
                        'default' => '0'
                    ),
                    array(
                        'id' => 'header',
                        'type' => 'select',
                        'multi' => true,
                        'sortable' => true,
                        'title' => esc_html__('Header parts', 'medican'),
                        'options' => $header_parts,
                        'default' => array('mobile_menu_button', 'mobile_menu', 'primary_menu'),
                    ),
                    array(
                        'id' => 'author_bio',
                        'type' => 'checkbox',
                        'title' => esc_html__('Show author bio in templates', 'medican'),
                        'default' => '0'
                    ),
                    array(
                        'id' => 'post_navigation',
                        'type' => 'select',
                        'title' => esc_html__('Post navigation place', 'medican'),
                        'options' => array(
                            'hidden' => esc_html__('Hidden', 'medican'),
                            'before' => esc_html__('Before content', 'medican'),
                            'after' => esc_html__('After content', 'medican'),
                        ),
                        'default' => 'hidden',
                    ),
                    array(
                        'id' => 'post_navigation_previous',
                        'type' => 'text',
                        'title' => esc_html__('Post navigation previous text', 'medican'),
                        'default' => '',
                    ),
                    array(
                        'id' => 'post_navigation_next',
                        'type' => 'text',
                        'title' => esc_html__('Post navigation next text', 'medican'),
                        'default' => '',
                    ),
                    array(
                        'id' => 'related_posts',
                        'type' => 'checkbox',
                        'title' => esc_html__('Show related posts', 'medican'),
                        'default' => '0'
                    ),
                    array(
                        'id' => 'comments',
                        'type' => 'checkbox',
                        'title' => esc_html__('Show comments in templates', 'medican'),
                        'default' => '1'
                    ),
                    array(
                        'id' => 'default_title',
                        'type' => 'text',
                        'title' => esc_html__('Default page title', 'medican'),
                        'default' => 'Latest posts',
                    ),
                    array(
                        'id' => 'post_page_title',
                        'type' => 'select',
                        'title' => esc_html__('Post page title', 'medican'),
                        'options' => $azexo_fields,
                        'default' => '',
                    ),
                    array(
                        'id' => 'strip_excerpt',
                        'type' => 'checkbox',
                        'title' => esc_html__('Strip excerpt', 'medican'),
                        'default' => '1',
                    ),
                    array(
                        'id' => 'excerpt_length',
                        'type' => 'text',
                        'title' => esc_html__('Excerpt length', 'medican'),
                        'default' => '15',
                    ),
                    array(
                        'id' => 'comment_excerpt_length',
                        'type' => 'text',
                        'title' => esc_html__('Comment excerpt length', 'medican'),
                        'default' => '15',
                    ),
                    array(
                        'id' => 'author_avatar_size',
                        'type' => 'text',
                        'title' => esc_html__('Author avatar size', 'medican'),
                        'default' => '100',
                    ),
                    array(
                        'id' => 'avatar_size',
                        'type' => 'text',
                        'title' => esc_html__('Avatar size', 'medican'),
                        'default' => '60',
                    ),
                    array(
                        'id' => 'related_posts_carousel_margin',
                        'type' => 'text',
                        'title' => esc_html__('Related posts carousel margin', 'medican'),
                        'default' => '0',
                    ),
                    array(
                        'id' => 'templates',
                        'type' => 'multi_text',
                        'title' => esc_html__('Templates', 'medican'),
                    ),
                    array(
                        'id' => 'meta_fields',
                        'type' => 'multi_text',
                        'title' => esc_html__('Meta fields', 'medican'),
                    ),
                )
            );

            foreach ($azexo_templates as $template_slug => $template_name) {


                $places = array(
                    $template_slug . '_thumbnail' => esc_html__('Thumbnail DIV', 'medican'),
                    $template_slug . '_hover' => esc_html__('Thumbnail hover DIV', 'medican'),
                    $template_slug . '_extra' => esc_html__('Header extra DIV', 'medican'),
                    $template_slug . '_meta' => esc_html__('Header meta DIV', 'medican'),
                    $template_slug . '_header' => esc_html__('Header DIV', 'medican'),
                    $template_slug . '_footer' => esc_html__('Footer DIV', 'medican'),
                    $template_slug . '_data' => esc_html__('Data DIV', 'medican'),
                    $template_slug . '_additions' => esc_html__('Additions DIV', 'medican'),
                );
                $post_fields = array();
                foreach ($places as $id => $name) {
                    $post_fields[] = array(
                        'id' => $id,
                        'type' => 'select',
                        'multi' => true,
                        'sortable' => true,
                        'title' => $name,
                        'options' => $azexo_fields
                    );
                }

                $this->sections[] = array(
                    'icon' => 'el-icon-cogs',
                    'title' => $template_name,
                    'subsection' => true,
                    'fields' => array_merge(array(
                        array(
                            'id' => $template_slug . '_show_thumbnail',
                            'type' => 'checkbox',
                            'title' => esc_html__('Show thumbnail/gallery', 'medican'),
                            'default' => '1'
                        ),
                        array(
                            'id' => $template_slug . '_image_thumbnail',
                            'type' => 'checkbox',
                            'title' => esc_html__('Only image thumbnail (no gallery)', 'medican'),
                            'default' => '0',
                            'required' => array($template_slug . '_show_thumbnail', 'equals', '1')
                        ),
                        array(
                            'id' => $template_slug . '_zoom',
                            'type' => 'checkbox',
                            'title' => esc_html__('Zoom image on mouse hover', 'medican'),
                            'default' => '0',
                            'required' => array($template_slug . '_show_thumbnail', 'equals', '1')
                        ),
                        array(
                            'id' => $template_slug . '_lazy',
                            'type' => 'checkbox',
                            'title' => esc_html__('Lazy load images', 'medican'),
                            'default' => '0',
                            'required' => array($template_slug . '_show_thumbnail', 'equals', '1')
                        ),
                        array(
                            'id' => $template_slug . '_show_carousel',
                            'type' => 'checkbox',
                            'title' => esc_html__('Show gallery as carousel', 'medican'),
                            'default' => '0',
                            'required' => array(
                                array($template_slug . '_show_thumbnail', 'equals', '1'),
                                array($template_slug . '_image_thumbnail', '!=', '1')
                            )
                        ),
                        array(
                            'id' => $template_slug . '_gallery_slider_thumbnails',
                            'type' => 'checkbox',
                            'title' => esc_html__('Show gallery slider thumbnails', 'medican'),
                            'default' => '0',
                            'required' => array($template_slug . '_show_carousel', 'equals', '1')
                        ),
                        array(
                            'id' => $template_slug . '_gallery_slider_thumbnails_vertical',
                            'type' => 'checkbox',
                            'title' => esc_html__('Vertical gallery slider thumbnails', 'medican'),
                            'default' => '0',
                            'required' => array($template_slug . '_gallery_slider_thumbnails', 'equals', '1'),
                        ),
                        array(
                            'id' => $template_slug . '_thumbnail_size',
                            'type' => 'text',
                            'title' => esc_html__('Thumbnail size', 'medican'),
                            'default' => 'large',
                            'required' => array($template_slug . '_show_thumbnail', 'equals', '1')
                        ),
                        array(
                            'id' => $template_slug . '_show_title',
                            'type' => 'checkbox',
                            'title' => esc_html__('Show title', 'medican'),
                            'default' => '1'
                        ),
                        array(
                            'id' => $template_slug . '_show_content',
                            'type' => 'select',
                            'title' => esc_html__('Show content/excerpt', 'medican'),
                            'options' => array(
                                'hidden' => esc_html__('Hidden', 'medican'),
                                'content' => esc_html__('Show content', 'medican'),
                                'excerpt' => esc_html__('Show excerpt', 'medican'),
                            ),
                            'default' => 'content',
                        ),
                        array(
                            'id' => $template_slug . '_excerpt_length',
                            'type' => 'text',
                            'title' => esc_html__('Excerpt length', 'medican'),
                            'default' => '15',
                            'required' => array($template_slug . '_show_content', 'equals', 'excerpt')
                        ),
                        array(
                            'id' => $template_slug . '_excerpt_words_trim',
                            'type' => 'checkbox',
                            'title' => esc_html__('Excerpt trim by words', 'medican'),
                            'default' => '1',
                            'required' => array($template_slug . '_show_content', 'equals', 'excerpt')
                        ),
                        array(
                            'id' => $template_slug . '_more_inside_content',
                            'type' => 'checkbox',
                            'title' => esc_html__('Show more link inside content', 'medican'),
                            'default' => '1',
                            'required' => array($template_slug . '_show_content', 'equals', 'content')
                        ),
                            ), $post_fields)
                );
            }

            $this->sections[] = array(
                'icon' => 'el-icon-cogs',
                'title' => esc_html__('Fields configuration', 'medican'),
                'fields' => array()
            );

            foreach ($azexo_fields as $field_slug => $field_name) {
                $fields = array();
                if (isset($taxonomy_fields[$field_slug]) || isset($meta_fields[$field_slug])) {
                    $fields[] = array(
                        'id' => str_replace('.php', '', $field_slug) . '_image',
                        'type' => 'media',
                        'title' => esc_html__('Image', 'medican'),
                        'default' => '',
                    );
                }
                $fields[] = array(
                    'id' => str_replace('.php', '', $field_slug) . '_prefix',
                    'type' => 'textarea',
                    'title' => esc_html__('Prefix', 'medican'),
                    'default' => '',
                );
                if (isset($taxonomy_fields[$field_slug]) || isset($meta_fields[$field_slug])) {
                    $fields[] = array(
                        'id' => str_replace('.php', '', $field_slug) . '_suffix',
                        'type' => 'textarea',
                        'title' => esc_html__('Suffix', 'medican'),
                        'default' => '',
                    );
                }
                $this->sections[] = array(
                    'icon' => 'el-icon-cogs',
                    'title' => $field_name,
                    'subsection' => true,
                    'fields' => $fields,
                );
            }

            $this->sections = apply_filters('azexo_settings_sections', $this->sections);

            $theme_info = '<div class="redux-framework-section-desc">';
            $theme_info .= '<p class="redux-framework-theme-data description theme-uri">' . wp_kses(__('<strong>Theme URL:</strong> ', 'medican'), array('strong')) . '<a href="' . esc_url($this->theme->get('ThemeURI')) . '" target="_blank">' . $this->theme->get('ThemeURI') . '</a></p>';
            $theme_info .= '<p class="redux-framework-theme-data description theme-author">' . wp_kses(__('<strong>Author:</strong> ', 'medican'), array('strong')) . $this->theme->get('Author') . '</p>';
            $theme_info .= '<p class="redux-framework-theme-data description theme-version">' . wp_kses(__('<strong>Version:</strong> ', 'medican'), array('strong')) . $this->theme->get('Version') . '</p>';
            $theme_info .= '<p class="redux-framework-theme-data description theme-description">' . $this->theme->get('Description') . '</p>';
            $tabs = $this->theme->get('Tags');
            if (!empty($tabs)) {
                $theme_info .= '<p class="redux-framework-theme-data description theme-tags">' . wp_kses(__('<strong>Tags:</strong> ', 'medican'), array('strong')) . implode(', ', $tabs) . '</p>';
            }
            $theme_info .= '</div>';

            $this->sections[] = array(
                'title' => esc_html__('Import / Export', 'medican'),
                'desc' => esc_html__('Import and Export your Redux Framework settings from file, text or URL.', 'medican'),
                'icon' => 'el-icon-refresh',
                'fields' => array(
                    array(
                        'id' => 'import-export',
                        'type' => 'import_export',
                        'title' => 'Import Export',
                        'subtitle' => 'Save and restore your Redux options',
                        'full_width' => false,
                    ),
                ),
            );

            $this->sections[] = array(
                'type' => 'divide',
            );

            $this->sections[] = array(
                'icon' => 'el-icon-info-sign',
                'title' => esc_html__('Theme Information', 'medican'),
                'fields' => array(
                    array(
                        'id' => 'raw-info',
                        'type' => 'raw',
                        'content' => $item_info,
                    )
                ),
            );
        }

        public function setArguments() {

            $theme = wp_get_theme();

            $this->args = array(
                'opt_name' => AZEXO_THEME_NAME,
                'page_slug' => '_options',
                'page_title' => 'AZEXO Options',
                'update_notice' => true,
                'admin_bar' => false,
                'menu_type' => 'menu',
                'menu_title' => 'AZEXO Options',
                'allow_sub_menu' => true,
                'page_parent_post_type' => 'your_post_type',
                'customizer' => true,
                'default_mark' => '*',
                'hints' =>
                array(
                    'icon' => 'el-icon-question-sign',
                    'icon_position' => 'right',
                    'icon_color' => 'lightgray',
                    'icon_size' => 'normal',
                    'tip_style' =>
                    array(
                        'color' => 'light',
                    ),
                    'tip_position' =>
                    array(
                        'my' => 'top left',
                        'at' => 'bottom right',
                    ),
                    'tip_effect' =>
                    array(
                        'show' =>
                        array(
                            'duration' => '500',
                            'event' => 'mouseover',
                        ),
                        'hide' =>
                        array(
                            'duration' => '500',
                            'event' => 'mouseleave unfocus',
                        ),
                    ),
                ),
                'output' => true,
                'output_tag' => true,
                'page_icon' => 'icon-themes',
                'page_permissions' => 'manage_options',
                'save_defaults' => true,
                'show_import_export' => true,
                'transient_time' => '3600',
                'network_sites' => true,
            );

            $theme = wp_get_theme();
            $this->args["display_name"] = $theme->get("Name");
            $this->args["display_version"] = $theme->get("Version");
        }

    }

    global $reduxConfig;
    $reduxConfig = new AZEXO_Redux_Framework_config();
}