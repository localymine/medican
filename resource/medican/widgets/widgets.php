<?php
add_action('widgets_init', 'azexo_register_widgets');

function azexo_register_widgets() {
    register_widget('AZEXOTitle');
    register_widget('AZEXOPost');
    register_widget('AZEXOTaxonomy');
}

class AZEXOTitle extends WP_Widget {

    function AZEXOTitle() {
        parent::__construct('azexo_title', AZEXO_THEME_NAME . ' - Page title');
    }

    function widget($args, $instance) {
        $title = apply_filters('widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);
        print $args['before_widget'];
        if ($title) {
            print$args['before_title'] . $title . $args['after_title'];
        }


        global $post;
        $original = $post;
        if (azexo_get_closest_current_post('page')) {
            $post = azexo_get_closest_current_post('page');
        } else if (azexo_get_closest_current_post('vc_widget', false)) {
            $post = azexo_get_closest_current_post('vc_widget', false);
        }
        if ($original->ID != $post->ID) {
            setup_postdata($post);
        }

        get_template_part('template-parts/general', 'title');

        if ($original->ID != $post->ID) {
            wp_reset_postdata();
            $post = $original;
        }



        print $args['after_widget'];
    }

}

class AZEXOPost extends WP_Widget {

    function AZEXOPost() {
        parent::__construct('azexo_post', AZEXO_THEME_NAME . ' - One post');
    }

    function widget($args, $instance) {
        $title = apply_filters('widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);
        print $args['before_widget'];
        if ($title) {
            print$args['before_title'] . $title . $args['after_title'];
        }

        if (!empty($instance['post'])) {
            if ($instance['full'] == 'on') {
                global $post;
                $original = $post;
                $post = get_post($instance['post']);
                setup_postdata($post);
                $template_name = $instance['template'];
                print '<div class="scoped-style">' . azexo_get_post_wpb_css($instance['post']);
                include(locate_template('content.php'));
                print '</div>';
                wp_reset_postdata();
                $post = $original;
            } else {
                print azexo_get_post_content($instance['post']);
            }
        } else {
            if ($instance['full'] == 'on') {
                $template_name = $instance['template'];
                print '<div class="scoped-style">' . azexo_get_post_wpb_css();
                include(locate_template('content.php'));
                print '</div>';
            } else {
                print azexo_get_post_content();
            }
        }

        print $args['after_widget'];
    }

    function update($new_instance, $old_instance) {
        $instance = parent::update($new_instance, $old_instance);
        $instance['full'] = $new_instance['full'];
        return $instance;
    }

    function form($instance) {
        $defaults = array('post' => '', 'title' => '', 'template' => 'widget_post', 'full' => 'off');
        $instance = wp_parse_args((array) $instance, $defaults);
        global $azexo_templates;
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php esc_html_e('Title:', 'medican'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($instance['title']); ?>" />
        </p><p>            
            <label for="<?php echo esc_attr($this->get_field_id('post')); ?>"><?php esc_html_e('Post ID:', 'medican'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('post')); ?>" name="<?php echo esc_attr($this->get_field_name('post')); ?>" type="text" value="<?php echo esc_attr($instance['post']); ?>" />
        </p>
        <p>    
            <input class="checkbox" type="checkbox" <?php checked($instance['full'], 'on'); ?> id="<?php echo esc_attr($this->get_field_id('full')); ?>" name="<?php echo esc_attr($this->get_field_name('full')); ?>" /> 
            <label for="<?php echo esc_attr($this->get_field_id('full')); ?>"><?php esc_html_e('Full post', 'medican'); ?></label>
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('template')); ?>"><?php esc_html_e('Post template:', 'medican'); ?></label>
            <select class="widefat" id="<?php echo esc_attr($this->get_field_id('template')); ?>" name="<?php echo esc_attr($this->get_field_name('template')); ?>">
                <?php
                foreach ($azexo_templates as $slug => $name) :
                    ?>
                    <option value="<?php echo esc_attr($slug) ?>" <?php selected($slug, $instance['template']) ?>><?php echo esc_attr($name); ?></option>
                <?php endforeach; ?>
            </select>
        </p>        
        <?php
    }

}

class AZEXOTaxonomy extends WP_Widget {

    public function __construct() {
        $widget_ops = array('classname' => 'widget_categories', 'description' => esc_html__("A list or dropdown of categories.", 'medican'));
        parent::__construct('azexo_taxonomy', AZEXO_THEME_NAME . ' - Taxonomy', $widget_ops);
    }

    public function widget($args, $instance) {

        /** This filter is documented in wp-includes/default-widgets.php */
        $title = apply_filters('widget_title', empty($instance['title']) ? esc_html__('Categories', 'medican') : $instance['title'], $instance, $this->id_base);

        $c = !empty($instance['count']) ? '1' : '0';
        $h = !empty($instance['hierarchical']) ? '1' : '0';
        $d = !empty($instance['dropdown']) ? '1' : '0';

        print $args['before_widget'];
        if ($title) {
            print $args['before_title'] . $title . $args['after_title'];
        }

        $cat_args = array(
            'orderby' => 'name',
            'show_count' => $c,
            'hierarchical' => $h,
            'taxonomy' => $instance['taxonomy']
        );

        if ($d) {
            static $first_dropdown = true;

            $dropdown_id = ( $first_dropdown ) ? 'cat' : "{$this->id_base}-dropdown-{$this->number}";
            $first_dropdown = false;

            echo '<label class="screen-reader-text" for="' . esc_attr($dropdown_id) . '">' . $title . '</label>';

            $cat_args['show_option_none'] = esc_html__('Please select', 'medican');
            $cat_args['id'] = $dropdown_id;

            /**
             * Filter the arguments for the Categories widget drop-down.
             *
             * @since 2.8.0
             *
             * @see wp_dropdown_categories()
             *
             * @param array $cat_args An array of Categories widget drop-down arguments.
             */
            wp_dropdown_categories(apply_filters('widget_categories_dropdown_args', $cat_args));
            ?>

            <script type='text/javascript'>
                /* <![CDATA[ */
                (function() {
                    var dropdown = document.getElementById("<?php echo esc_js($dropdown_id); ?>");
                    function onCatChange() {
                        if (dropdown.options[ dropdown.selectedIndex ].value > 0) {
                            location.href = "<?php echo esc_url(home_url('/')); ?>/?cat=" + dropdown.options[ dropdown.selectedIndex ].value;
                        }
                    }
                    dropdown.onchange = onCatChange;
                })();
                /* ]]> */
            </script>

            <?php
        } else {
            ?>
            <ul>
                <?php
                $cat_args['title_li'] = '';

                /**
                 * Filter the arguments for the Categories widget.
                 *
                 * @since 2.8.0
                 *
                 * @param array $cat_args An array of Categories widget options.
                 */
                wp_list_categories(apply_filters('widget_categories_args', $cat_args));
                ?>
            </ul>
            <?php
        }

        print $args['after_widget'];
    }

    public function update($new_instance, $old_instance) {
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['count'] = !empty($new_instance['count']) ? 1 : 0;
        $instance['hierarchical'] = !empty($new_instance['hierarchical']) ? 1 : 0;
        $instance['dropdown'] = !empty($new_instance['dropdown']) ? 1 : 0;
        $instance['taxonomy'] = $new_instance['taxonomy'];
        return $instance;
    }

    public function form($instance) {
        //Defaults
        $instance = wp_parse_args((array) $instance, array('title' => '', 'taxonomy' => 'category'));
        $title = esc_attr($instance['title']);
        $count = isset($instance['count']) ? (bool) $instance['count'] : false;
        $hierarchical = isset($instance['hierarchical']) ? (bool) $instance['hierarchical'] : false;
        $dropdown = isset($instance['dropdown']) ? (bool) $instance['dropdown'] : false;

        $taxonomies = get_taxonomies(array(), 'objects');
        ?>
        <p><label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php esc_html_e('Title:', 'medican'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
        </p>

        <p>
            <label for="<?php echo esc_attr($this->get_field_id('taxonomy')); ?>"><?php esc_html_e('Taxonomy:', 'medican'); ?></label>
            <select class="widefat" id="<?php echo esc_attr($this->get_field_id('taxonomy')); ?>" name="<?php echo esc_attr($this->get_field_name('taxonomy')); ?>">
                <?php
                foreach ($taxonomies as $slug => $taxonomy) :
                    ?>
                    <option value="<?php echo esc_attr($slug) ?>" <?php selected($slug, $instance['taxonomy']) ?>><?php echo esc_attr($taxonomy->label); ?></option>
                <?php endforeach; ?>
            </select>
        </p>        

        <p>
            <input type="checkbox" class="checkbox" id="<?php echo esc_attr($this->get_field_id('dropdown')); ?>" name="<?php echo esc_attr($this->get_field_name('dropdown')); ?>"<?php checked($dropdown); ?> />
            <label for="<?php echo esc_attr($this->get_field_id('dropdown')); ?>"><?php esc_html_e('Display as dropdown', 'medican'); ?></label><br />

            <input type="checkbox" class="checkbox" id="<?php echo esc_attr($this->get_field_id('count')); ?>" name="<?php echo esc_attr($this->get_field_name('count')); ?>"<?php checked($count); ?> />
            <label for="<?php echo esc_attr($this->get_field_id('count')); ?>"><?php esc_html_e('Show post counts', 'medican'); ?></label><br />

            <input type="checkbox" class="checkbox" id="<?php echo esc_attr($this->get_field_id('hierarchical')); ?>" name="<?php echo esc_attr($this->get_field_name('hierarchical')); ?>"<?php checked($hierarchical); ?> />
            <label for="<?php echo esc_attr($this->get_field_id('hierarchical')); ?>"><?php esc_html_e('Show hierarchy', 'medican'); ?></label>
        </p>
        <?php
    }

}
