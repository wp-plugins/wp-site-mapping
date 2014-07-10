<?php

if (!class_exists('WPSM_Settings')) {

    /**
     * Handles plugin settings and user profile meta fields
     */
    class WPSM_Settings extends WPSM_Module
    {
        protected $settings;
        protected static $default_settings;
        protected static $readable_properties = array('settings');
        protected static $writeable_properties = array('settings');

        /*
         * General methods
         */

        /**
         * Constructor
         *
         * @mvc Controller
         */
        protected function __construct()
        {
            $this->register_hook_callbacks();
        }

        /**
         * Public setter for protected variables
         *
         * Updates settings outside of the Settings API or other subsystems
         *
         * @mvc Controller
         *
         * @param string $variable
         * @param array $value This will be merged with WPSM_Settings->settings, so it should mimic the structure of the WPSM_Settings::$default_settings. It only needs the contain the values that will change, though. See WordPress_Site_Mapping->upgrade() for an example.
         */
        public function __set($variable, $value)
        {
            // Note: WPSM_Module::__set() is automatically called before this

            if ($variable != 'settings') {
                return;
            }

            $this->settings = self::validate_settings($value);
            update_option('wpsm_settings', $this->settings);
        }

        /**
         * Register callbacks for actions and filters
         *
         * @mvc Controller
         */
        public function register_hook_callbacks()
        {
            add_action('init', array($this, 'init'));

            add_filter(
                'plugin_action_links_' . plugin_basename(dirname(__DIR__)) . '/bootstrap.php',
                __CLASS__ . '::add_plugin_action_links'
            );
        }

        /**
         * Prepares site to use the plugin during activation
         *
         * @mvc Controller
         *
         * @param bool $network_wide
         */
        public function activate($network_wide)
        {
        }

        /**
         * Rolls back activation procedures when de-activating the plugin
         *
         * @mvc Controller
         */
        public function deactivate()
        {
        }

        /**
         * Initializes variables
         *
         * @mvc Controller
         */
        public function init()
        {
            self::$default_settings = self::get_default_settings();
            $this->settings = self::get_settings();
        }

        /**
         * Executes the logic of upgrading from specific older versions of the plugin to the current version
         *
         * @mvc Model
         *
         * @param string $db_version
         */
        public function upgrade($db_version = 0)
        {
            /*
            if( version_compare( $db_version, 'x.y.z', '<' ) )
            {
                // Do stuff
            }
            */
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
            // Note: __set() calls validate_settings(), so settings are never invalid

            return true;
        }


        /*
         * Plugin Settings
         */

        /**
         * Establishes initial values for all settings
         *
         * @mvc Model
         *
         * @return array
         */
        protected static function get_default_settings()
        {
            return array(
                'db-version' => '0',
            );
        }

        /**
         * Retrieves all of the settings from the database
         *
         * @mvc Model
         *
         * @return array
         */
        protected static function get_settings()
        {
            $settings = shortcode_atts(
                self::$default_settings,
                get_option('wpsm_settings', array())
            );

            return $settings;
        }

        /**
         * Adds links to the plugin's action link section on the Plugins page
         *
         * @mvc Model
         *
         * @param array $links The links currently mapped to the plugin
         * @return array
         */
        public static function add_plugin_action_links($links)
        {
            array_unshift($links, '<a href="http://wordpress.org/extend/plugins/wp-site-mapping/faq/">Help</a>');

            return $links;
        }

        /**
         * Validates submitted setting values before they get saved to the database. Invalid data will be overwritten with defaults.
         *
         * @mvc Model
         *
         * @param array $new_settings
         * @return array
         */
        public function validate_settings($new_settings)
        {
            $new_settings = shortcode_atts($this->settings, $new_settings);

            if (!is_string($new_settings['db-version'])) {
                $new_settings['db-version'] = WordPress_Site_Mapping::VERSION;
            }

            return $new_settings;
        }
    } // end WPSM_Settings
}
