<?php
if (!defined('ABSPATH'))
    exit;


function azexo_remove_class_filter($hook_name = '', $class_name = '', $method_name = '', $priority = 10) {
    global $wp_filter;
    if (!isset($wp_filter[$hook_name][$priority]) || !is_array($wp_filter[$hook_name][$priority]))
        return false;
    foreach ((array) $wp_filter[$hook_name][$priority] as $unique_id => $filter_array) {
        if (isset($filter_array['function']) && is_array($filter_array['function'])) {
            if (is_object($filter_array['function'][0]) && get_class($filter_array['function'][0]) && get_class($filter_array['function'][0]) == $class_name && $filter_array['function'][1] == $method_name) {
                unset($wp_filter[$hook_name][$priority][$unique_id]);
            }
        }
    }
    return false;
}


class AZEXO_Import {

    private $wp_filesystem = null;

    function __construct() {
        if (isset($_GET['page']) && $_GET['page'] == 'azexo_import') {
            azexo_remove_class_filter('admin_init', 'WC_Vendors', 'check_install');
            azexo_remove_class_filter('admin_init', 'SF_Settings_API', 'register_options');
            
            require_once(ABSPATH . 'wp-admin/includes/file.php');
            require_once(ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php');
            require_once(ABSPATH . 'wp-admin/includes/class-wp-filesystem-direct.php');
            $this->wp_filesystem = new WP_Filesystem_Direct(array());
        }
        add_action('admin_menu', array(&$this, 'init'));
    }

    function init() {

        add_theme_page(
                'AZEXO Import Configuration', 'AZEXO Import', 'edit_theme_options', 'azexo_import', array(&$this, 'import')
        );

        wp_enqueue_style('azexo-import', get_template_directory_uri() . '/azexo/importer/import.css', false, time(), 'all');
        wp_enqueue_script('azexo-import', get_template_directory_uri() . '/azexo/importer/import.js', false, time(), true);
    }

    function array_filter_recursive($input, $callback = null) {
        if (is_array($input)) {
            foreach ($input as &$value) {
                $value = $this->array_filter_recursive($value, $callback);
            }
            return $input;
        } else {
            return $callback($input);
        }
    }

    function import_content($file = 'content.xml') {
        $xml = get_template_directory() . '/azexo/importer/data/' . $file;
        if (file_exists($xml)) {

            if ($_POST && key_exists('clear-wp', $_POST) && $_POST['clear-wp']) {
                global $wpdb;
                $wpdb->query("TRUNCATE {$wpdb->posts}");
                $wpdb->query("TRUNCATE {$wpdb->postmeta}");
                $wpdb->query("TRUNCATE {$wpdb->comments}");
                $wpdb->query("TRUNCATE {$wpdb->commentmeta}");
                $wpdb->query("TRUNCATE {$wpdb->terms}");
                $wpdb->query("TRUNCATE {$wpdb->term_relationships}");
                $wpdb->query("TRUNCATE {$wpdb->term_taxonomy}");
                update_option('sidebars_widgets', array());
            }

            $import = new WP_Import();
            $import->fetch_attachments = ( $_POST && key_exists('attachments', $_POST) && $_POST['attachments'] ) ? true : false;

            ob_start();
            $import->import($xml);
            ob_end_clean();

            // set home & blog page
            $home = get_page_by_title('Home');
            $blog = null;
            foreach (array('Blog', 'News', 'Journal') as $title) {
                $blog = get_page_by_title($title);
                if (is_object($blog)) {
                    break;
                }
            }
            if (is_object($home) && is_object($blog)) {
                update_option('show_on_front', 'page');
                update_option('page_on_front', $home->ID); // Front Page
                update_option('page_for_posts', $blog->ID); // Blog Page
            }

            if (function_exists('vc_default_editor_post_types')) {
                $pt_array = ( $pt_array = get_option('wpb_js_content_types') ) ? ( $pt_array ) : vc_default_editor_post_types();
            } else {
                $pt_array = ( $pt_array = get_option('wpb_js_content_types') ) ? ( $pt_array ) : array();
            }
            if (!in_array('page', $pt_array)) {
                $pt_array[] = 'page';
                update_option('wpb_js_content_types', $pt_array);
            }
        } else {
            $this->error = esc_html__('File which need for import not exist.', 'medican');
        }
    }

    public function import_menus($file = 'menus.json') {
        $file_path = get_template_directory() . '/azexo/importer/data/' . $file;
        if (file_exists($file_path)) {
            global $wpdb;
            $file_data = $this->wp_filesystem->get_contents($file_path);
            $data = json_decode($file_data, true);
            $menu_array = array();
            foreach ($data as $registered_menu => $menu_slug) {
                $term_rows = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->prefix}terms where slug=%s", $menu_slug), ARRAY_A);
                if (isset($term_rows[0]['term_id'])) {
                    $term_id_by_slug = $term_rows[0]['term_id'];
                } else {
                    $term_id_by_slug = null;
                }
                $menu_array[$registered_menu] = $term_id_by_slug;
            }
            set_theme_mod('nav_menu_locations', array_map('absint', $menu_array));
        } else {
            $this->error = esc_html__('File which need for import not exist.', 'medican');
        }
    }

    function import_menu_location($file = 'menus.json') {
        $file_path = get_template_directory() . '/azexo/importer/data/' . $file;
        if (file_exists($file_path)) {
            $file_data = $this->wp_filesystem->get_contents($file_path);
            $data = json_decode($file_data, true);
            $menus = wp_get_nav_menus();

            foreach ($data as $key => $val) {
                foreach ($menus as $menu) {
                    if ($menu->slug == $val) {
                        $data[$key] = absint($menu->term_id);
                    }
                }
            }

            set_theme_mod('nav_menu_locations', $data);
        } else {
            $this->error = esc_html__('File which need for import not exist.', 'medican');
        }
    }

    function import_options($file = 'options.json', $url = false) {

        $file_path = get_template_directory() . '/azexo/importer/data/' . $file;
        if (file_exists($file_path)) {
            $file_data = $this->wp_filesystem->get_contents($file_path);
            $data = json_decode($file_data, true);
            if (is_array($data)) {
                if ($url) {
                    $replace = home_url('/');
                    foreach ($data as $name => $option) {
                        if (is_array($option)) {
                            foreach ($option as $key => $op) {
                                if (is_string($op)) {
                                    $data[$name][$key] = str_replace($url, $replace, $op);
                                }
                            }
                        }
                    }
                }
                foreach ($data as $name => $option) {
                    update_option($name, $option);
                }
                do_action('azexo_import_configuration');
            }
        } else {
            $this->error = esc_html__('File which need for import not exist.', 'medican');
        }
    }

    function import_configuration($file = 'configuration.json', $url = false) {
        $file_path = get_template_directory() . '/azexo/importer/data/' . $file;
        if (file_exists($file_path)) {
            $file_data = $this->wp_filesystem->get_contents($file_path);
            $data = json_decode($file_data, true);
            if (is_array($data)) {
                update_option(AZEXO_THEME_NAME, $data);
                do_action('azexo_import_configuration');
            }
        } else {
            $this->error = esc_html__('File which need for import not exist.', 'medican');
        }
    }

    function import_widgets($file = 'widget_data.json') {
        $file_path = get_template_directory() . '/azexo/importer/data/' . $file;
        if (file_exists($file_path)) {
            $file_data = $this->wp_filesystem->get_contents($file_path);
            $this->import_widgets_data($file_data);
        } else {
            $this->error = esc_html__('File which need for import not exist.', 'medican');
        }
    }

    function import() {

        deactivate_plugins(plugin_basename(WP_PLUGIN_DIR . '/wordpress-importer/wordpress-importer.php'));

        if (key_exists('azexo_import_nonce', $_POST)) {
            if (wp_verify_nonce($_POST['azexo_import_nonce'], 'azexo_import_nonce')) {

                // Importer classes
                if (!defined('WP_LOAD_IMPORTERS'))
                    define('WP_LOAD_IMPORTERS', true);

                if (!class_exists('WP_Import')) {
                    require( WP_PLUGIN_DIR . '/wordpress-importer/wordpress-importer.php');
                }

                if (class_exists('WP_Import')) {

                    switch ($_POST['import']) {

                        case 'all':
                            // Full Demo Data ---------------------------------
                            $this->import_options();
                            $this->import_content();
                            $this->import_menus();
                            $this->import_widgets();
                            break;

                        case 'demo':
                            // Single Demo Data ---------------------------------
                            $_POST['demo'] = htmlspecialchars(stripslashes($_POST['demo']));

                            $file = $_POST['demo'] . '/options.json';
                            $this->import_options($file);

                            $file = $_POST['demo'] . '/content.xml';
                            $this->import_content($file);

                            $file = $_POST['demo'] . '/menus.json';
                            $this->import_menus($file);

                            $file = $_POST['demo'] . '/widget_data.json';
                            $this->import_widgets($file);

                            break;

                        case 'configuration':
                            // Single Demo Data ---------------------------------
                            $_POST['demo'] = htmlspecialchars(stripslashes($_POST['demo']));

                            $file = $_POST['demo'] . '/configuration.json';
                            $this->import_configuration($file);

                            $file = $_POST['demo'] . '/vc_widgets.xml';
                            $this->import_content($file);

                            break;

                        case 'content':
                            if ($_POST['content']) {
                                $_POST['content'] = htmlspecialchars(stripslashes($_POST['content']));
                                $file = 'content/' . $_POST['content'] . '.xml';
                                $this->import_content($file);
                            } else {
                                $this->import_content();
                            }
                            break;

                        case 'options':
                            // Theme Options ----------------------------------
                            $this->import_options();
                            break;

                        case 'widgets':
                            // Widgets ----------------------------------------
                            $this->import_widgets();
                            break;

                        default:
                            // Empty select.import
                            $this->error = esc_html__('Please select data to import.', 'medican');
                            break;
                    }

                    // message box
                    if (isset($this->error)) {
                        echo '<div class="error settings-error">';
                        echo '<p><strong>' . $this->error . '</strong></p>';
                        echo '</div>';
                    } else {
                        echo '<div class="updated settings-error">';
                        echo '<p><strong>' . esc_html__('All done. Have fun!', 'medican') . '</strong></p>';
                        echo '</div>';
                    }
                }
            }
        }
        ?>
        <div id="azexo-wrapper" class="azexo-import wrap">

            <h2><?php echo esc_html(get_admin_page_title()); ?></h2>

            <form action="" method="post">

                <input type="hidden" name="azexo_import_nonce" value="<?php echo wp_create_nonce('azexo_import_nonce'); ?>" />

                <table class="form-table">

                    <tr class="row-import">
                        <th scope="row">
                            <label for="import">Import</label>
                        </th>
                        <td>
                            <select name="import" class="import">
                                <option value="demo" selected>Demo - new site</option>
                                <option value="configuration">Configuration - existing site</option>
                            </select>
                        </td>
                    </tr>

                    <tr class="row-demo hide">
                        <th scope="row">
                            <label for="demo">Demo</label>
                        </th>
                        <td>
                            <select name="demo">
                                <?php
                                foreach (new DirectoryIterator(get_template_directory() . '/azexo/importer/data/') as $fileInfo) {
                                    if ($fileInfo->isDir() && !$fileInfo->isDot()) {
                                        print '<option value="' . $fileInfo->getFilename() . '">' . $fileInfo->getFilename() . '</option>';
                                    }
                                }
                                ?>
                            </select>
                        </td>
                    </tr>

                    <tr class="row-content hide">
                        <th scope="row">
                            <label for="content">Demo content</label>
                        </th>
                        <td>
                            <select name="content">
                                <option value="">-- All --</option>
                                <option value="pages">Pages</option>
                                <option value="posts">Posts</option>
                                <option value="vc_widgets">VC Widgets</option>
                            </select>
                        </td>
                    </tr>

                    <tr class="row-attachments hide">
                        <th scope="row">Attachments</th>
                        <td>
                            <fieldset>
                                <label for="attachments"><input type="checkbox" value="1" id="attachments" name="attachments">Import attachments</label>
                                <p class="description">Download all attachments from the demo may take a while. Please be patient.</p>
                            </fieldset>
                        </td>
                    </tr>

                    <tr class="row-clear-wp hide">
                        <th scope="row">Clear WP</th>
                        <td>
                            <fieldset>
                                <label for="clear-wp"><input type="checkbox" value="1" id="clear-wp" name="clear-wp">Clear WP database before import content</label>
                                <p class="description">Remove all posts, pages, comments, taxonomies, custom posts types and correspond meta data.</p>
                            </fieldset>
                        </td>
                    </tr>

                </table>

                <input type="submit" name="submit" class="button button-primary" value="Import data" />

            </form>

        </div>	
        <?php
    }

    /** ---------------------------------------------------------------------------
     * Parse JSON import file
     * http://wordpress.org/plugins/widget-settings-importexport/
     * ---------------------------------------------------------------------------- */
    function import_widgets_data($json_data) {

        $json_data = json_decode($json_data, true);

        $sidebar_data = $json_data[0];
        $widget_data = $json_data[1];

        // prepare widgets table
        $widgets = array();
        foreach ($widget_data as $k_w => $widget_type) {
            if ($k_w) {
                $widgets[$k_w] = array();
                foreach ($widget_type as $k_wt => $widget) {
                    if (is_int($k_wt))
                        $widgets[$k_w][$k_wt] = 1;
                }
            }
        }

        // sidebars
        foreach ($sidebar_data as $title => $sidebar) {
            $count = count($sidebar);
            for ($i = 0; $i < $count; $i++) {
                $widget = array();
                $widget['type'] = trim(substr($sidebar[$i], 0, strrpos($sidebar[$i], '-')));
                $widget['type-index'] = trim(substr($sidebar[$i], strrpos($sidebar[$i], '-') + 1));
                if (!isset($widgets[$widget['type']][$widget['type-index']])) {
                    unset($sidebar_data[$title][$i]);
                }
            }
            $sidebar_data[$title] = array_values($sidebar_data[$title]);
        }

        // widgets
        foreach ($widgets as $widget_title => $widget_value) {
            foreach ($widget_value as $widget_key => $widget_value) {
                $widgets[$widget_title][$widget_key] = $widget_data[$widget_title][$widget_key];
            }
        }

        $sidebar_data = array(array_filter($sidebar_data), $widgets);
        $this->parse_import_data($sidebar_data);
    }

    /** ---------------------------------------------------------------------------
     * Import widgets
     * http://wordpress.org/plugins/widget-settings-importexport/
     * ---------------------------------------------------------------------------- */
    function parse_import_data($import_array) {
        $sidebars_data = $import_array[0];
        $widget_data = $import_array[1];

        $current_sidebars = get_option('sidebars_widgets');
        $new_widgets = array();

        foreach ($sidebars_data as $import_sidebar => $import_widgets) :

            foreach ($import_widgets as $import_widget) :

                // if NOT the sidebar exists
                if (!isset($current_sidebars[$import_sidebar])) {
                    $current_sidebars[$import_sidebar] = array();
                }

                $title = trim(substr($import_widget, 0, strrpos($import_widget, '-')));
                $index = trim(substr($import_widget, strrpos($import_widget, '-') + 1));
                $current_widget_data = get_option('widget_' . $title);
                $new_widget_name = $this->get_new_widget_name($title, $index);
                $new_index = trim(substr($new_widget_name, strrpos($new_widget_name, '-') + 1));

                if (!empty($new_widgets[$title]) && is_array($new_widgets[$title])) {
                    while (array_key_exists($new_index, $new_widgets[$title])) {
                        $new_index++;
                    }
                }
                $current_sidebars[$import_sidebar][] = $title . '-' . $new_index;
                if (array_key_exists($title, $new_widgets)) {
                    $new_widgets[$title][$new_index] = $widget_data[$title][$index];

                    // notice fix
                    if (!key_exists('_multiwidget', $new_widgets[$title]))
                        $new_widgets[$title]['_multiwidget'] = '';

                    $multiwidget = $new_widgets[$title]['_multiwidget'];
                    unset($new_widgets[$title]['_multiwidget']);
                    $new_widgets[$title]['_multiwidget'] = $multiwidget;
                } else {
                    $current_widget_data[$new_index] = $widget_data[$title][$index];

                    // notice fix
                    if (!key_exists('_multiwidget', $current_widget_data))
                        $current_widget_data['_multiwidget'] = '';

                    $current_multiwidget = $current_widget_data['_multiwidget'];
                    $new_multiwidget = isset($widget_data[$title]['_multiwidget']) ? $widget_data[$title]['_multiwidget'] : false;
                    $multiwidget = ($current_multiwidget != $new_multiwidget) ? $current_multiwidget : 1;
                    unset($current_widget_data['_multiwidget']);
                    $current_widget_data['_multiwidget'] = $multiwidget;
                    $new_widgets[$title] = $current_widget_data;
                }

            endforeach;
        endforeach;

        if (isset($new_widgets) && isset($current_sidebars)) {
            update_option('sidebars_widgets', $current_sidebars);

            foreach ($new_widgets as $title => $content)
                update_option('widget_' . $title, $content);

            return true;
        }

        return false;
    }

    /** ---------------------------------------------------------------------------
     * Get new widget name
     * http://wordpress.org/plugins/widget-settings-importexport/
     * ---------------------------------------------------------------------------- */
    function get_new_widget_name($widget_name, $widget_index) {
        $current_sidebars = get_option('sidebars_widgets');
        $all_widget_array = array();
        foreach ($current_sidebars as $sidebar => $widgets) {
            if (!empty($widgets) && is_array($widgets) && $sidebar != 'wp_inactive_widgets') {
                foreach ($widgets as $widget) {
                    $all_widget_array[] = $widget;
                }
            }
        }
        while (in_array($widget_name . '-' . $widget_index, $all_widget_array)) {
            $widget_index++;
        }
        $new_widget_name = $widget_name . '-' . $widget_index;
        return $new_widget_name;
    }

}

$azexo_import = new AZEXO_Import();
?>
