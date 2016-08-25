<?php

if (!function_exists('azexo_buildQuery')) {

    function azexo_buildQuery($atts) {

        $atts['items_per_page'] = $atts['query_items_per_page'] = isset($atts['max_items']) ? $atts['max_items'] : '';
        $atts['query_offset'] = isset($atts['offset']) ? $atts['offset'] : '';

        $defaults = array(
            'post_type' => 'post',
            'orderby' => '',
            'order' => 'DESC',
            'meta_key' => '',
            'max_items' => '10',
            'offset' => '0',
            'taxonomies' => '',
            'custom_query' => '',
            'include' => '',
            'exclude' => '',
        );
        $atts = wp_parse_args($atts, $defaults);

        // Set include & exclude
        if ($atts['post_type'] !== 'ids' && !empty($atts['exclude'])) {
            $atts['exclude'] .= ',' . $atts['exclude'];
        } else {
            $atts['exclude'] = $atts['exclude'];
        }
        if ($atts['post_type'] !== 'ids') {
            $settings = array(
                'posts_per_page' => $atts['query_items_per_page'],
                'offset' => $atts['query_offset'],
                'orderby' => $atts['orderby'],
                'order' => $atts['order'],
                'meta_key' => $atts['orderby'] == 'meta_value' ? $atts['meta_key'] : '',
                'post_type' => $atts['post_type'],
                'exclude' => $atts['exclude'],
            );
            if (!empty($atts['taxonomies'])) {
                $vc_taxonomies_types = get_taxonomies(array('public' => true));
                $terms = get_terms(array_keys($vc_taxonomies_types), array(
                    'hide_empty' => false,
                    'include' => $atts['taxonomies'],
                ));
                $settings['tax_query'] = array();
                $tax_queries = array(); // List of taxnonimes
                foreach ($terms as $t) {
                    if (!isset($tax_queries[$t->taxonomy])) {
                        $tax_queries[$t->taxonomy] = array(
                            'taxonomy' => $t->taxonomy,
                            'field' => 'id',
                            'terms' => array($t->term_id),
                            'relation' => 'IN'
                        );
                    } else {
                        $tax_queries[$t->taxonomy]['terms'][] = $t->term_id;
                    }
                }
                $settings['tax_query'] = array_values($tax_queries);
                $settings['tax_query']['relation'] = 'OR';
            }
        } else {
            if (empty($atts['include'])) {
                $atts['include'] = - 1;
            } elseif (!empty($atts['exclude'])) {
                $atts['include'] = preg_replace(
                        '/(('
                        . preg_replace(
                                array('/^\,\*/', '/\,\s*$/', '/\s*\,\s*/'), array('', '', '|'), $atts['exclude']
                        )
                        . ')\,*\s*)/', '', $atts['include']);
            }
            $settings = array(
                'include' => $atts['include'],
                'posts_per_page' => $atts['query_items_per_page'],
                'offset' => $atts['query_offset'],
                'post_type' => 'any',
                'orderby' => 'post__in',
            );
        }

        return $settings;
    }

}
if (!function_exists('azexo_filterQuerySettings')) {

    function azexo_filterQuerySettings($args) {
        $defaults = array(
            'numberposts' => 5,
            'offset' => 0,
            'category' => 0,
            'orderby' => 'date',
            'order' => 'DESC',
            'include' => array(),
            'exclude' => array(),
            'meta_key' => '',
            'meta_value' => '',
            'post_type' => 'post',
            'public' => true
        );

        $r = wp_parse_args($args, $defaults);
        if (empty($r['post_status'])) {
            $r['post_status'] = ( 'attachment' == $r['post_type'] ) ? 'inherit' : 'publish';
        }
        if (!empty($r['numberposts']) && empty($r['posts_per_page'])) {
            $r['posts_per_page'] = $r['numberposts'];
        }
        if (!empty($r['category'])) {
            $r['cat'] = $r['category'];
        }
        if (!empty($r['include'])) {
            $incposts = wp_parse_id_list($r['include']);
            $r['posts_per_page'] = count($incposts);  // only the number of posts included
            $r['post__in'] = $incposts;
        } elseif (!empty($r['exclude'])) {
            $r['post__not_in'] = wp_parse_id_list($r['exclude']);
        }

        $r['ignore_sticky_posts'] = true;
        $r['no_found_rows'] = true;

        return azexo_array_filter_recursive($r);
    }

}

$output = $template = $title = $filter = $loop = $posts_clauses = $only_content = $carousel = $full_width = $center = $carousel_stagePadding = $item_margin = $posts_per_item = $el_class = $css = '';
extract(shortcode_atts(array(
    'title' => '',
    'filter' => '',
    'posts_clauses' => '',
    'template' => 'post',
    'only_content' => false,
    'item_wrapper' => false,
    'carousel' => false,
    'center' => false,
    'loop' => false,
    'carousel_stagePadding' => 0,
    'item_margin' => 0,
    'posts_per_item' => 1,
    'full_width' => false,
    'el_class' => '',
    'css' => '',
                ), $atts));

$el_class = $this->getExtraClass($el_class);
$css_class = apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $el_class . vc_shortcode_custom_css_class($css, ' '), $this->settings['base'], $atts);

global $vc_posts_grid_exclude_id;
$vc_posts_grid_exclude_id[] = get_the_ID();

$loop_args = azexo_filterQuerySettings(azexo_buildQuery($atts));

if (!empty($posts_clauses) && function_exists($posts_clauses)) {
    add_filter('posts_clauses', $posts_clauses, 10, 2);
}

$loop_args = apply_filters('azexo_posts_list_loop_args', $loop_args);
$query = new WP_Query($loop_args);
$post_type = isset($query->query['post_type']) ? $query->query['post_type'] : 'post';
if (is_array($post_type)) {
    $post_type = $post_type[0];
}
update_meta_cache($post_type, array_keys($query->posts));


if (!empty($posts_clauses) && function_exists($posts_clauses))
    remove_filter('posts_clauses', $posts_clauses);

if ($carousel) {
    wp_enqueue_script('azexo-owl.carousel');
    wp_enqueue_style('azexo-owl.carousel');
}

if ($query->have_posts()) {
    $options = get_option(AZEXO_THEME_NAME);

    if ($only_content) {
        $size = array('width' => '', 'height' => '');
    } else {
        $thumbnail_size = isset($options[$template . '_thumbnail_size']) && !empty($options[$template . '_thumbnail_size']) ? $options[$template . '_thumbnail_size'] : 'large';
        azexo_add_image_size($thumbnail_size);
        $size = azexo_get_image_sizes($thumbnail_size);
    }

    print '<div class="posts-list-wrapper ' . esc_attr($css_class) . '">';
    if (!empty($title) || !empty($title)) {
        print '<div class="list-header">';
    }
    if (!empty($title)) {
        print '<div class="list-title"><h3>' . $title . '</h3></div>';
    }
    if (!empty($filter)) {
        $filter_all_terms = get_terms($filter);
        if (is_array($filter_all_terms)) {
            print '<div class="list-filter">';
            print '<div class="filter-term">' . esc_html__('All', 'medican') . '</div>';
            foreach ($filter_all_terms as $term) {
                print '<div class="filter-term" data-term="' . esc_attr($term->slug) . '">' . esc_html($term->name) . '</div>';
            }
            print '</div>';
        }
    }
    if (!empty($title) || !empty($title)) {
        print '</div>';
    }
    print '<div class="posts-list ' . ($only_content ? '' : str_replace('_', '-', $template)) . ' ' . ($carousel ? 'owl-carousel' : '') . ' ' . esc_attr($el_class) . '" data-contents-per-item="' . esc_attr($posts_per_item) . '" data-width="' . $size['width'] . '" data-height="' . $size['height'] . '" data-stagePadding="' . esc_attr($carousel_stagePadding) . '" data-margin="' . esc_attr($item_margin) . '" data-full-width="' . esc_attr($full_width) . '" data-center="' . esc_attr($center) . '" data-loop="' . esc_attr($loop) . '">';
    global $post;
    $original = $post;
    while ($query->have_posts()) {
        $query->the_post();

        if ($only_content) {
            print azexo_get_post_content($post->ID);
        } else {
            $template_name = $template;
            $azwoo_base_tag = 'div';
            if (!empty($filter)) {
                $filter_terms = wp_get_post_terms($post->ID, $filter);
                if (is_array($filter_terms)) {
                    $filter_terms = array_map(function($term) {
                        return $term->slug;
                    }, $filter_terms);
                    print '<div class="filterable ' . esc_attr(implode(' ', $filter_terms)) . '">';
                }                
            }
            if ($item_wrapper) {
                print '<div class="item">';
            }
            include(locate_template(apply_filters('azexo_post_template_path', 'content.php', $template)));
            if ($item_wrapper) {
                print '</div>';
            }
            if (!empty($filter)) {
                print '</div>';
            }
        }
    }
    wp_reset_postdata();
    $post = $original;
    print '</div></div>';
}
