<?php
add_filter('azexo_templates', 'azexo_azlp_templates');

function azexo_azlp_templates($azexo_templates) {
    $azexo_templates['single_profile'] = esc_html__('Single profile', 'medican');
    $azexo_templates['list_profile'] = esc_html__('List profile', 'medican');
    return $azexo_templates;
}

add_filter('azexo_template_name', 'azexo_azlp_template_name');

function azexo_azlp_template_name($template_name) {
    if (in_array(get_post_type(), array('azl_profile'))) {
        return 'single_profile';
    }
    return $template_name;
}

add_filter('azexo_fields', 'azexo_azlp_fields');

function azexo_azlp_fields($azexo_fields) {
    return array_merge($azexo_fields, array(
        'profile_rating' => esc_html__('Profile: Average rating', 'medican'),
        'profile_review_count' => esc_html__('Profile: Review count', 'medican'),
        'profile_link' => esc_html__('Profile: Link to author profile', 'medican'),
        'profile_products_count' => esc_html__('Profile: Products count', 'medican'),
    ));
}

add_filter('azexo_fields_post_types', 'azexo_azlp_fields_post_types');

function azexo_azlp_fields_post_types($azexo_fields_post_types) {
    $azexo_fields_post_types['profile_link'] = '';
    $azexo_fields_post_types['profile_products_count'] = '';
    return $azexo_fields_post_types;
}

add_action('wp_update_comment_count', 'azexo_azlp_update_comment_count');

function azexo_azlp_update_comment_count($post_id) {
    delete_post_meta($post_id, '_azlp_average_rating');
    delete_post_meta($post_id, '_azlp_rating_count');
}

add_filter('azexo_entry_field', 'azexo_azlp_entry_field', 10, 2);

function azexo_azlp_entry_field($output, $name) {
    global $post;
    switch ($name) {
        case 'profile_products_count':
            $posts = get_posts(array(
                'post_type' => 'product',
                'post_status' => 'publish',
                'author' => $post->post_author,
                'ignore_sticky_posts' => 1,
                'no_found_rows' => 1,
                'posts_per_page' => '-1',
            ));
            $count = count($posts);
            $posts = get_posts(array(
                'post_type' => 'azl_profile',
                'post_status' => 'publish',
                'author' => $post->post_author,
                'ignore_sticky_posts' => 1,
                'no_found_rows' => 1,
                'posts_per_page' => '-1',
            ));
            if (!empty($posts)) {
                $profile = reset($posts);
                $products = '<a href="' . esc_url(get_permalink($profile)) . '"><span class="count">' . esc_html($count) . '</span><span class="label">' . esc_html__('products', 'medican') . '</span></a>';
                return '<span class="products-count">' . $products . '</span>';
            }
            break;
        case 'profile_link':
            $posts = get_posts(array(
                'post_type' => 'azl_profile',
                'post_status' => 'publish',
                'author' => $post->post_author,
            ));
            if (!empty($posts)) {
                $profile = reset($posts);
                $options = get_option(AZEXO_FRAMEWORK);
                $label = (isset($options['profile_link_prefix']) && !empty($options['profile_link_prefix'])) ? esc_html($options['profile_link_prefix']) : esc_html__('Profile', 'medican');
                return '<div class="azl-profile"><a href="' . esc_url(get_permalink($profile)) . '">' . esc_html($label) . '</a></div>';
            }
            break;
        case 'profile_rating':
            if (!metadata_exists('post', $post->ID, '_azlp_rating_count')) {
                global $wpdb;

                $counts = array();
                $raw_counts = $wpdb->get_results($wpdb->prepare("
			SELECT meta_value, COUNT( * ) as meta_value_count FROM $wpdb->commentmeta
			LEFT JOIN $wpdb->comments ON $wpdb->commentmeta.comment_id = $wpdb->comments.comment_ID
			WHERE meta_key = 'rating'
			AND comment_post_ID = %d
			AND comment_approved = '1'
			AND meta_value > 0
			GROUP BY meta_value
		", $post->ID));

                foreach ($raw_counts as $count) {
                    $counts[$count->meta_value] = $count->meta_value_count;
                }

                update_post_meta($post->ID, '_azlp_rating_count', $counts);
            }
            $counts = get_post_meta($post->ID, '_azlp_rating_count', true);
            $rating_count = array_sum($counts);

            $average = 0;
            if (!metadata_exists('post', $post->ID, '_azlp_average_rating')) {
                if ($rating_count) {
                    global $wpdb;

                    $ratings = $wpdb->get_var($wpdb->prepare("
				SELECT SUM(meta_value) FROM $wpdb->commentmeta
				LEFT JOIN $wpdb->comments ON $wpdb->commentmeta.comment_id = $wpdb->comments.comment_ID
				WHERE meta_key = 'rating'
				AND comment_post_ID = %d
				AND comment_approved = '1'
				AND meta_value > 0
			", $post->ID));
                    $average = number_format($ratings / $rating_count, 2, '.', '');
                }
                update_post_meta($post->ID, '_azlp_average_rating', $average);
            }
            $average = floatval(get_post_meta($post->ID, '_azlp_average_rating', true));

            if ($rating_count > 0) {
                ob_start();
                ?>
                <div class="star-rating" title="<?php printf(__('Rated %s out of 5', 'medican'), $average); ?>">
                    <span style="width:<?php echo ( ( $average / 5 ) * 100 ); ?>%">
                        <strong class="rating"><?php echo esc_html($average); ?></strong> <?php printf(__('out of %s5%s', 'medican'), '<span>', '</span>'); ?>
                        <?php printf(_n('based on %s user rating', 'based on %s user ratings', $rating_count, 'medican'), '<span class="rating">' . $rating_count . '</span>'); ?>
                    </span>
                </div>
                <?php
                return ob_get_clean();
            }
            break;
        case 'profile_review_count':
            if (!metadata_exists('post', $post->ID, '_azlp_review_count')) {
                global $wpdb;
                $count = $wpdb->get_var($wpdb->prepare("
				SELECT COUNT(*) FROM $wpdb->comments
				WHERE comment_parent = 0
				AND comment_post_ID = %d
				AND comment_approved = '1'
			", $post->ID));

                update_post_meta($post->ID, '_azlp_review_count', $count);
            } else {
                $count = get_post_meta($post->ID, '_azlp_review_count', true);
            }


            if (comments_open()) {
                ob_start();
                ?>
                <a href="#reviews" class="profile-review-link" rel="nofollow">(<?php printf(_n('%s customer review', '%s customer reviews', $count, 'medican'), '<span class="count">' . $count . '</span>'); ?>)</a>
                <?php
                return ob_get_clean();
            }
            break;
    }
    return $output;
}

add_filter('azexo_entry_field_meta_field', 'azexo_azlp_entry_field_meta_field', 10, 2);

function azexo_azlp_entry_field_meta_field($value, $name) {
    if ($value == '') {
        $posts = get_posts(array(
            'post_type' => 'azl_profile',
            'post_status' => 'publish',
            'author' => get_the_author_meta('ID'),
        ));
        if (!empty($posts)) {
            $profile = reset($posts);
            return get_post_meta($profile->ID, $name, true);
        }
    }
    return $value;
}

add_filter('azexo_entry_field_taxonomy_field', 'azexo_azlp_entry_field_taxonomy_field', 10, 2);

function azexo_azlp_entry_field_taxonomy_field($term_list, $name) {
    if ($term_list === false) {
        $taxonomies = get_object_taxonomies('azl_profile');
        $slug = str_replace('taxonomy_', '', $name);
        if (in_array($slug, $taxonomies)) {
            $posts = get_posts(array(
                'post_type' => 'azl_profile',
                'post_status' => 'publish',
                'author' => get_the_author_meta('ID'),
            ));
            if (!empty($posts)) {
                $profile = reset($posts);
                $term_list = get_the_term_list($profile->ID, $slug, '', '<span class="delimiter">,</span> ', '');
                return $term_list;
            }
        }
    }
    return $term_list;
}

add_action('azsl_social_login_insert_user', 'azexo_azlp_social_login_insert_user');

function azexo_azlp_social_login_insert_user($user_id) {
    if (isset($_POST['picture'])) {
        $posts = get_posts(array(
            'post_type' => 'azl_profile',
            'post_status' => 'publish',
            'author' => $user_id,
        ));
        if (!empty($posts)) {
            $profile = reset($posts);

            wp_update_post(array(
                'ID' => $profile->ID,
                'post_title' => sanitize_text_field($_POST['name']),
            ));

            $file_array = array();
            $file_array['name'] = basename(untrailingslashit(esc_url($_POST['picture'])));
            $file_array['tmp_name'] = download_url(esc_url($_POST['picture']));
            if (!is_wp_error($file_array['tmp_name'])) {
                $id = media_handle_sideload($file_array, $profile->ID);
                if (!is_wp_error($id)) {
                    set_post_thumbnail($profile, $id);
                }
            }
        }
    }
}

add_filter('azexo_review_allowed', 'azexo_azlp_review_allowed', 10, 4);

function azexo_azlp_review_allowed($allowed, $customer_email, $user_id, $post) {
    if ($post->post_type == 'azl_profile') {
        return true;
    }
    return $allowed;
}
$userid = get_current_user_id();
$caps = get_user_meta($userid, 'wp_capabilities', true);


 $roles = array_keys((array)$caps);
 if (!in_array("vendor", $roles)) {
add_filter('azexo_dashboard_links', 'azexo_azlp_dashboard_links');
 }
function azexo_azlp_dashboard_links($links) {

    $posts = get_posts(array(
        'post_type' => 'azl_profile',
        'author' => get_current_user_id(),
        'role'=>'subscriber'
    ));
    $profile = reset($posts);
    if ($profile) {
        $edit_profile = 0;
        if (function_exists('cmb2_get_option')) {
            $forms = cmb2_get_option('azl_options', 'forms');
            if (is_array($forms)) {
                foreach ($forms as $form) {
                    if ($form['post_type'] == 'azl_profile') {
                        if (isset($form['page']) && is_numeric($form['page'])) {
                            $edit_profile = $form['page'];
                        }
                    }
                }
            }
        }
        
       
        $customerphone1  = $profile->post_author;
        $customerphone =  get_user_meta( $customerphone1, 'user_phone', true );  
        $userid = get_current_user_id();
        $int = wc_get_customer_order_count( $user_id );
        $storepagelink =  get_permalink(1372);
        $customcaps = get_user_meta($userid, 'custom_capability', true);
        
            $links = array_merge(array(

            array(
                'id' => $profile->ID,
                'url' => esc_url(get_permalink($profile)),
                'title' => esc_html__('Hello,&nbsp;%'.$customerphone.'%', 'medican'),
            ),

            array(
              'id' => $profile->ID,
              'url' => esc_url(get_permalink($profile)),
              'title' => esc_html__('Consumer Dashboard', 'medican'),
            ),
            array(
              'id' => $profile->ID,
              'url' => esc_url(get_permalink($profile)),
              'title' => esc_html__('Orders (00)', 'medican'),
            ),  
            array(
              'id' => $profile->ID,
              'url' => $storepagelink,
              'title' => esc_html__('Sign up as a Store', 'medican'),
            ),
            ), $links); 
        }
     


    return $links;
}

add_action('azsl_social_login', 'azexo_azlp_social_login');

function azexo_azlp_social_login($user_id) {
    $posts = get_posts(array(
        'post_type' => 'azl_profile',
        'author' => $user_id,
    ));
    $profile = reset($posts);
    if ($profile) {
        print get_permalink($profile);
    }
}

function azexo_azlp_closest_profile_author_filter($args, $query) {
    global $wpdb;

    $profile_post = azexo_get_closest_current_post('azl_profile');
    if ($profile_post) {
        $args['where'] .= " AND ( $wpdb->posts.post_author = " . esc_sql($profile_post->post_author) . ") ";
    }

    return $args;
}

function azexo_azlp_closest_product_author_filter($args, $query) {
    global $wpdb;

    $product_post = azexo_get_closest_current_post('product');
    if ($product_post) {
        $args['where'] .= " AND ( $wpdb->posts.post_author = " . esc_sql($product_post->post_author) . ") ";
    }

    return $args;
}

add_action('comment_post', 'azexo_azlp_comment_post', 10, 1);

function azexo_azlp_comment_post($comment_id) {
    if ('azl_profile' === get_post_type($_POST['comment_post_ID'])) {
        $review_marks = azexo_review_marks();
        if (empty($review_marks)) {
            if (isset($_POST['rating'])) {
                if (!$_POST['rating'] || $_POST['rating'] > 5 || $_POST['rating'] < 0) {
                    return;
                }
                add_comment_meta($comment_id, 'rating', (int) esc_attr($_POST['rating']), true);
            }
        } else {
            $rating = 0;
            foreach ($review_marks as $slug => $label) {
                if (isset($_POST[$slug])) {
                    if (!$_POST[$slug] || $_POST[$slug] > 5 || $_POST[$slug] < 0) {
                        continue;
                    }
                    add_comment_meta($comment_id, $slug, (int) esc_attr($_POST[$slug]), true);
                    $rating += (int) $_POST[$slug];
                }
            }
            $rating = number_format($rating / count($review_marks), 1);
            delete_comment_meta($comment_id, 'rating');
            add_comment_meta($comment_id, 'rating', $rating, true);
        }
    }
}

add_filter('comments_template', 'azexo_azlp_comments_template');

function azexo_azlp_comments_template($template) {
    if (get_post_type() !== 'azl_profile') {
        return $template;
    }

    wp_enqueue_script('wc-single-product');

    $check_dirs = array(
        trailingslashit(get_stylesheet_directory()) . WC()->template_path(),
        trailingslashit(get_template_directory()) . WC()->template_path(),
        trailingslashit(get_stylesheet_directory()),
        trailingslashit(get_template_directory()),
        trailingslashit(WC()->plugin_path()) . 'templates/'
    );

    if (WC_TEMPLATE_DEBUG_MODE) {
        $check_dirs = array(array_pop($check_dirs));
    }

    foreach ($check_dirs as $dir) {
        if (file_exists(trailingslashit($dir) . 'single-product-reviews.php')) {
            return trailingslashit($dir) . 'single-product-reviews.php';
        }
    }
}

add_action('template_redirect', 'azexo_azlp_template_redirect');

function azexo_azlp_template_redirect() {
    if (class_exists('WCV_Vendors') && WCV_Vendors::is_vendor_page()) {
        $vendor_shop = urldecode(get_query_var('vendor_shop'));
        $vendor_id = WCV_Vendors::get_vendor_id($vendor_shop);

        $posts = get_posts(array(
            'post_type' => 'azl_profile',
            'author' => $vendor_id,
        ));
        $profile = reset($posts);
        if ($profile) {
            exit(wp_redirect(get_permalink($profile)));
        }
    }
}

add_filter('template_include', 'azexo_azlp_template_include');

function azexo_azlp_template_include($template) {
    global $wp_query, $azexo_queried_object;
    if (is_archive() && azexo_is_post_type_query($wp_query, 'azl_profile')) {
        if (isset($_GET['role']) && isset($_GET['role']) == 'vendor') {
            $template = azexo_locate_template('page-templates/vendors-profiles.php');
        } else {
            $template = azexo_locate_template('page-templates/profiles.php');
        }
    }
    return $template;
}

add_filter('update_post_metadata', 'azexo_azlp_update_post_metadata', 10, 5);

function azexo_azlp_update_post_metadata($check, $object_id, $meta_key, $meta_value, $prev_value) {
    if ($meta_key == '_wp_page_template' && $meta_value == 'page-templates/vendors-profiles.php') {
        update_option('azexo_vendors_profiles', $object_id);
    }
    if ($meta_key == '_wp_page_template' && $meta_value == 'page-templates/profiles.php') {
        update_option('azexo_profiles', $object_id);
    }
    return $check;
}

add_filter('azl_register_post_type_profile', 'azexo_azlp_register_post_type_profile');

function azexo_azlp_register_post_type_profile($post_type) {
    if (isset($_GET['role']) && isset($_GET['role']) == 'vendor') {
        $page_id = get_option('azexo_vendors_profiles');
    } else {
        $page_id = get_option('azexo_profiles');
    }
    if (empty($page_id)) {
        $pages = get_pages(array(
            'meta_key' => '_wp_page_template',
            'meta_value' => (isset($_GET['role']) && isset($_GET['role']) == 'vendor') ? 'page-templates/vendors-profiles.php' : 'page-templates/profiles.php'
        ));
        if (!empty($pages)) {
            $page = reset($pages);
            $page_id = $page->ID;
            if (isset($_GET['role']) && isset($_GET['role']) == 'vendor') {
                update_option('azexo_vendors_profiles', $page_id);
            } else {
                update_option('azexo_profiles', $page_id);
            }
        }
    }
    if (!empty($page_id)) {
        $page = get_post($page_id);
        if ($page) {
            post_type_archive_title();
            $post_type['labels']['name'] = $page->post_title;
            $post_type['labels']['singular_name'] = $page->post_title;
            $post_type['has_archive'] = get_page_uri($page);
        }
    }

    return $post_type;
}

function azexo_azlp_get_author_posts($post_author) {
    $ids = get_posts(array(
        'fields' => 'ids',
        'author' => $post_author,
        'post_type' => 'product', //need - not in azl_profile
        'post_status' => 'publish',
        'ignore_sticky_posts' => 1,
        'no_found_rows' => 1,
        'posts_per_page' => '-1',
    ));
    return $ids;
}

add_filter('azexo_posts_list_post_terms', 'azexo_azlp_posts_list_post_terms', 10, 3);

function azexo_azlp_posts_list_post_terms($terms, $post, $taxonomy) {
    if ($post->post_type == 'azl_profile') {
        $taxonomy_names = get_object_taxonomies('azl_profile');
        if (!in_array($taxonomy, $taxonomy_names)) {
            $terms = wp_get_object_terms(azexo_azlp_get_author_posts($post->post_author), $taxonomy, array('fields' => 'all'));
        }
    }
    return $terms;
}
