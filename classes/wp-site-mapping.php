<?php

if (!class_exists('WordPress_Site_Mapping')) {

    /**
     * Main / front controller class
     */
    class WordPress_Site_Mapping extends WPSM_Module
    {
        /**
         * @var array
         */
        protected static $readable_properties = array(); // These should really be constants, but PHP doesn't allow class constants to be arrays
        /**
         * @var array
         */
        protected static $writeable_properties = array();
        /**
         * @var array
         */
        protected $modules;

        /**
         *
         */
        const VERSION = '0.2.2';
        /**
         *
         */
        const PREFIX = 'wpsm_';
        /**
         *
         */
        const DEBUG_MODE = false;


        /*
         * Magic methods
         */

        /**
         * Constructor
         *
         * @mvc Controller
         */
        protected function __construct()
        {
            $this->register_hook_callbacks();

            $this->modules = array(
                'WPSM_Settings' => WPSM_Settings::get_instance()
            );
        }


        /*
         * Static methods
         */

        /**
         * Enqueues CSS, JavaScript, etc
         *
         * @mvc Controller
         */
        public static function load_resources()
        {
            wp_register_script(
                self::PREFIX . 'wp-site-mapping',
                plugins_url('javascript/wp-site-mapping.js', dirname(__FILE__)),
                array('jquery'),
                self::VERSION,
                true
            );

            wp_register_script(
                self::PREFIX . 'wp-site-mapping-admin',
                plugins_url('javascript/wp-site-mapping-admin.js', dirname(__FILE__)),
                array('jquery'),
                self::VERSION,
                true
            );

            wp_register_style(
                self::PREFIX . 'admin',
                plugins_url('css/admin.css', dirname(__FILE__)),
                array(),
                self::VERSION,
                'all'
            );

            wp_register_style(
                self::PREFIX . 'wpsm',
                plugins_url('css/wpsm.css', dirname(__FILE__)),
                array(),
                self::VERSION,
                'all'
            );

            if (is_admin()) {
                wp_enqueue_style(self::PREFIX . 'admin');
                wp_enqueue_script(self::PREFIX . 'wp-site-mapping-admin');
            } else {
                wp_enqueue_style(self::PREFIX . 'wpsm');
                wp_enqueue_script(self::PREFIX . 'wp-site-mapping');
            }
        }

        /**
         * Clears caches of content generated by caching plugins like WP Super Cache
         *
         * @mvc Model
         */
        protected static function clear_caching_plugins()
        {
            // WP Super Cache
            if (function_exists('wp_cache_clear_cache')) {
                wp_cache_clear_cache();
            }

            // W3 Total Cache
            if (class_exists('W3_Plugin_TotalCacheAdmin')) {
                $w3_total_cache = w3_instance('W3_Plugin_TotalCacheAdmin');

                if (method_exists($w3_total_cache, 'flush_all')) {
                    $w3_total_cache->flush_all();
                }
            }
        }


        /*
         * Instance methods
         */

        /**
         * Prepares sites to use the plugin during single or network-wide activation
         *
         * @mvc Controller
         *
         * @param bool $network_wide
         */
        public function activate($network_wide)
        {
            global $wpdb;

            if (function_exists('is_multisite') && is_multisite()) {
                if ($network_wide) {
                    $blogs = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");

                    foreach ($blogs as $blog) {
                        switch_to_blog($blog);
                        $this->single_activate($network_wide);
                    }

                    restore_current_blog();
                } else {
                    $this->single_activate($network_wide);
                }
            } else {
                $this->single_activate($network_wide);
            }
        }

        /**
         * Runs activation code on a new WPMS site when it's created
         *
         * @mvc Controller
         *
         * @param int $blog_id
         */
        public function activate_new_site($blog_id)
        {
            switch_to_blog($blog_id);
            $this->single_activate(true);
            restore_current_blog();
        }

        /**
         * Prepares a single blog to use the plugin
         *
         * @mvc Controller
         *
         * @param bool $network_wide
         */
        protected function single_activate($network_wide)
        {
            foreach ($this->modules as $module) {
                $module->activate($network_wide);
            }
        }

        /**
         * Rolls back activation procedures when de-activating the plugin
         *
         * @mvc Controller
         */
        public function deactivate()
        {
            foreach ($this->modules as $module) {
                $module->deactivate();
            }
        }

        /**
         * Register callbacks for actions and filters
         *
         * @mvc Controller
         */
        public function register_hook_callbacks()
        {
            add_action('wpmu_new_blog', __CLASS__ . '::activate_new_site');
            add_action('wp_enqueue_scripts', __CLASS__ . '::load_resources');
            add_action('admin_enqueue_scripts', __CLASS__ . '::load_resources');

            add_action('init', array($this, 'init'));
            add_action('init', array($this, 'upgrade'), 11);
            add_action('init', array($this, 'editor_buttons'));


            add_shortcode('showsitemap', array($this, 'handle_short_code'));
            add_action('wp_ajax_get_site_map', array($this, 'get_site_map_settings'));
        }

        /**
         *
         */
        function get_site_map_settings()
        {
            $instance = array();
            $instance['options-post-id'] = isset($_GET['post_id']) ? $_GET['post_id'] : '';
            $instance['options-category'] = isset($_GET['cat']) ? explode(',', $_GET['cat']) : array();
            $instance['options-format'] = isset($_GET['fmt']) ? explode(',', $_GET['fmt']) : array();
            $instance['options-post-type'] = isset($_GET['type']) ? explode(',', $_GET['type']) : array();
            $instance['options-tag'] = isset($_GET['tag']) ? explode(',', $_GET['tag']) : array();
            $instance['options-user'] = isset($_GET['aut']) ? explode(',', $_GET['aut']) : array();
            $instance['options-depth'] = isset($_GET['depth']) ? intval($_GET['depth']) : 10;
            $instance['options-group'] = isset($_GET['group']) ? $_GET['group'] : 'title';
            $instance['options-link'] = isset($_GET['link']) ? $_GET['link'] : '<a title="%title%" href="%permalink%">%title%</a>';
            $instance['options-inc-exc'] = isset($_GET['exclude']) ? intval($_GET['exclude']) : 0;
            $instance['options-group-only'] = isset($_GET['grouponly']) ? intval($_GET['grouponly']) : 0;
            $instance['class'] = isset($_GET['class']) ? $_GET['class'] : '';
            $instance['id'] = isset($_GET['id']) ? $_GET['id'] : '';

            echo "<style>.widefat { border-spacing: 0; clear: both; margin: 0; width: 100%; }</style>";

            echo self::render_template('wpsm-settings/widget-settings-fields.php', array('widget' => $this, 'instance' => $instance, 'in_editor' => true), 'always');
            die;
        }

        /**
         * @param $s
         * @return mixed
         */
        function get_field_name($s)
        {
            $mapping = array(
                'options-post-id' => 'post_id',
                'options-category' => 'cat',
                'options-tag' => 'tag',
                'options-format' => 'fmt',
                'options-post-type' => 'type',
                'options-user' => 'aut',
                'options-depth' => 'depth',
                'options-group' => 'group',
                'options-link' => 'link',
                'options-inc-exc' => 'exclude',
                'options-group-only' => 'grouponly',
                'class' => 'class',
                'id' => 'id',
            );
            return $mapping[$s];
        }

        /**
         * @param $s
         * @return mixed
         */
        function get_field_id($s)
        {
            $mapping = array(
                'options-post-id' => 'post_id',
                'options-category' => 'cat',
                'options-tag' => 'tag',
                'options-format' => 'fmt',
                'options-post-type' => 'type',
                'options-user' => 'aut',
                'options-depth' => 'depth',
                'options-group' => 'group',
                'options-link' => 'link',
                'options-inc-exc' => 'exclude',
                'options-group-only' => 'grouponly',
                'class' => 'class',
                'id' => 'id',
            );
            return $mapping[$s];
        }

        /**
         *
         */
        function editor_buttons()
        {
            if ((current_user_can('edit_posts') || current_user_can('edit_pages')) && get_user_option('rich_editing')) {
                add_filter('mce_external_plugins', array($this, 'add_buttons'));
                add_filter('mce_buttons', array($this, 'register_buttons'));
            }
        }

        /**
         * @param $plugin_array
         * @return mixed
         */
        function add_buttons($plugin_array)
        {
            $plugin_array['wpsm'] = plugins_url('../javascript/shortcode.js', __file__);
            return $plugin_array;
        }

        /**
         * @param $buttons
         * @return mixed
         */
        function register_buttons($buttons)
        {
            array_push($buttons, 'showsitemap');
            return $buttons;
        }


        /**
         * @param $attributes
         * @return string
         */
        function handle_short_code($attributes)
        {
            extract(shortcode_atts(array(
                'post_id' => '',
                'cat' => '',
                'tag' => '',
                'fmt' => '',
                'type' => '',
                'aut' => '',
                'depth' => 10,
                'group' => 'title',
                'link' => '<a title="%title%" href="%permalink%">%title%</a>',
                'exclude' => 0,
                'grouponly' => 0,
                'class' => '',
                'id' => 'showsitemap',
            ), $attributes));

            $instance = array();
            $instance['options-post-id'] = $post_id;
            $instance['options-category'] = explode(',', $cat);
            $instance['options-tag'] = explode(',', $tag);
            $instance['options-format'] = explode(',', $fmt);
            $instance['options-post-type'] = explode(',', $type);
            $instance['options-user'] = explode(',', $aut);
            $instance['options-depth'] = $depth;
            $instance['options-group'] = $group;
            $instance['options-link'] = html_entity_decode(html_entity_decode($link));
            $instance['options-inc-exc'] = $exclude;
            $instance['options-group-only'] = $grouponly;

            return "<div id='$id' class='wpsm $class'>" . $this->get_site_map($instance) . '</div>';
        }

        function get_post_tree_level($current_post_id, $depth, $max_depth, $exclude, $options_post_id, $options_post_type, $options_author, $link_template, $order_by, $add_where, $add_join, &$count, $options_category, $options_tag, $grouponly)
        {
            $site_map = "";
            if ($depth < $max_depth) {

                $my_posts = $this->get_post_descendants($current_post_id, $exclude, $options_post_id, $options_post_type, $options_author, $order_by, $add_where, $add_join, $options_category, $options_tag);
                $count = count($my_posts);
                error_log("grouponly=$grouponly");
                if ($count > 0 && $grouponly != 1) {
                    $site_map .= "<ul id='sitemap_list_$current_post_id' class='sitemap_depth_$depth'>\n";

                    foreach ($my_posts as $post) {
                        $site_map .= '<li class="post-item post-item-' . $post['ID'] . '">';
                        $site_map .= $this->get_link($link_template, $post['ID']);
                        $subcount = 0;
                        $site_map .= $this->get_post_tree_level($post['ID'], $depth + 1, $max_depth, $exclude, $options_post_id, $options_post_type, $options_author, $link_template, $order_by, $add_where, $add_join, $subcount, $options_category, $options_tag, $grouponly);
                        $site_map .= "</li>\n";
                    }

                    $site_map .= "</ul>\n";
                }
            }
            return $site_map;
        }

        /**
         * @param $template
         * @param $id
         */
        function get_link($template, $id)
        {
            $post_author_id = get_post_field('post_author', $id);
            $patterns = array(
                '/%title%/',
                '/%permalink%/',
                '/%year%/',
                '/%monthnum%/',
                '/%day%/',
                '/%hour%/',
                '/%minute%/',
                '/%second%/',
                '/%post_id%/',
                '/%category%/',
                '/%author%/',
            );
            $replacements = array(
                get_the_title($id),
                get_permalink($id),
                get_the_time('Y', $id),
                get_the_time('m', $id),
                get_the_time('d', $id),
                get_the_time('H', $id),
                get_the_time('i', $id),
                get_the_time('s', $id),
                $id,
                strip_tags(get_the_category_list(',', '', $id)),
                get_the_author_meta('display_name', $post_author_id),
            );
            return preg_replace($patterns, $replacements, $template);
        }

        function get_post_descendants($current_post_id, $exclude, $options_post_id, $options_post_type, $options_author, $order_by, $add_where, $add_join, $options_category, $options_tag)
        {
            global $wpdb;

            $query = "SELECT `posts`.`ID` ";
            $query .= "FROM $wpdb->posts as `posts` ";
            if (!empty($add_join)) {
                $query .= " $add_join ";
            }
            $query .= "WHERE `posts`.`post_status` = 'publish' ";
            $query .= "AND `posts`.`post_parent` = $current_post_id ";

            if ($exclude == 1) {
                if (!empty($options_post_id)) {
                    $query .= "AND `posts`.`ID` NOT IN ( $options_post_id ) ";
                }

                if (!empty($options_post_type)) {
                    $query .= "AND `posts`.`post_type` NOT IN ( $options_post_type ) ";
                }

                if (!empty($options_author)) {
                    $query .= "AND `posts`.`post_author` NOT IN ( $options_author ) ";
                }
            } else {
                if (!empty($options_post_id)) {
                    $query .= "AND `posts`.`ID` IN ( $options_post_id ) ";
                }

                if (!empty($options_post_type)) {
                    $query .= "AND `posts`.`post_type` IN ( $options_post_type ) ";
                }

                if (!empty($options_author)) {
                    $query .= "AND `posts`.`post_author` IN ( $options_author ) ";
                }
            }

            if (!empty($add_where)) {
                $query .= "AND $add_where ";
            }

            $query .= "GROUP BY `posts`.`ID` ";

            if (!empty($order_by)) {
                $query .= "ORDER BY $order_by ASC ";
            }

            $my_posts = $wpdb->get_results($query, ARRAY_A);
            return $my_posts;
        }

        function get_post_dates() {
            global $wpdb;

            $query = 'SELECT DISTINCT DATE_FORMAT(`posts`.`post_date`,"%Y-%m") AS post_date ';
            $query .= "FROM $wpdb->posts as `posts` ";
            $query .= "WHERE `posts`.`post_status` = 'publish' ";
            $query .= "ORDER BY post_date ASC ";

            $my_dates = $wpdb->get_results($query, ARRAY_A);
            return $my_dates;
        }

        /**
         * @param $instance
         * @return string
         */
        function get_site_map($instance)
        {
            global $wpdb;

            $args = array();
            $args['before_widget'] = "";
            $args['after_widget'] = "";

            return self::render_template('wpsm-widget/widget.php', array('wpdb' => $wpdb, 'instance' => $instance, 'args' => $args, 'in_widget' => false), 'always');
        }

        /**
         * @param $needle
         * @param $haystack
         * @return bool
         */
        public function in_array_substr($needle, $haystack)
        {
            foreach ($haystack as $hay_item) {
                if ($hay_item !== "" && strpos($needle, $hay_item)) {
                    return true;
                }
            }
            return false;
        }

        /**
         * Initializes variables
         *
         * @mvc Controller
         */
        public function init()
        {
            try {
            } catch (Exception $exception) {
                add_notice(__METHOD__ . ' error: ' . $exception->getMessage(), 'error');
            }
        }

        /**
         * Checks if the plugin was recently updated and upgrades if necessary
         *
         * @mvc Controller
         *
         * @param string $db_version
         */
        public function upgrade($db_version = 0)
        {
            if (version_compare($this->modules['WPSM_Settings']->settings['db-version'], self::VERSION, '==')) {
                return;
            }

            foreach ($this->modules as $module) {
                $module->upgrade($this->modules['WPSM_Settings']->settings['db-version']);
            }

            $this->modules['WPSM_Settings']->settings = array('db-version' => self::VERSION);
            self::clear_caching_plugins();
        }

        /**
         * Checks that the object is in a correct state
         *
         * @mvc Model
         *
         * @param string $property An individual property to check, or 'all' to check all of them
         * @return bool
         */
        protected function is_valid($property = 'all')
        {
            return true;
        }
    }

    ; // end WordPress_Site_Mapping
}
