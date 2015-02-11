<?php

/**
 * Created by PhpStorm.
 * User: benohead
 * Date: 09.05.14
 * Time: 13:55
 */
class WPSM_Widget extends WP_Widget
{

    function __construct()
    {
        parent::__construct(
            'wpsm_widget',
            __('Site Map', 'wpsm_widget_domain'),
            array('description' => __('WP Site Mapping', 'wpsm_widget_domain'),)
        );
    }

    public function widget($args, $instance)
    {
        global $wpdb;

        echo self::render_template('wpsm-widget/widget.php', array('wpdb' => $wpdb, 'instance' => $instance, 'args' => $args, 'in_widget' => true), 'always');
    }

    public function form($instance)
    {
        $instance = wp_parse_args((array)$instance, array(
            'options-post-id' => '',
            'options-category' => array(),
            'options-tag' => array(),
            'options-format' => array(),
            'options-post-type' => array(),
            'options-user' => array(),
            'options-depth' => 10,
            'options-group' => '',
            'options-reverse' => 0,
            'options-link' => '<a title="%title%" href="%permalink%">%title%</a>',
            'options-inc-exc' => 0,
            'options-group-only' => 0,
        ));
        echo self::render_template('wpsm-settings/widget-settings-fields.php', array('widget' => $this, 'instance' => $instance, 'in_editor' => false), 'always');
    }

    /**
     * Render a template
     *
     * Allows parent/child themes to override the markup by placing the a file named basename( $default_template_path ) in their root folder,
     * and also allows plugins or themes to override the markup by a filter. Themes might prefer that method if they place their templates
     * in sub-directories to avoid cluttering the root folder. In both cases, the theme/plugin will have access to the variables so they can
     * fully customize the output.
     *
     * @mvc @model
     *
     * @param  string $default_template_path The path to the template, relative to the plugin's `views` folder
     * @param  array $variables An array of variables to pass into the template's scope, indexed with the variable name so that it can be extract()-ed
     * @param  string $require 'once' to use require_once() | 'always' to use require()
     * @return string
     */
    protected static function render_template($default_template_path = false, $variables = array(), $require = 'once')
    {
        do_action('wpsm_render_template_pre', $default_template_path, $variables);

        $template_path = locate_template(basename($default_template_path));
        if (!$template_path) {
            $template_path = dirname(__DIR__) . '/views/' . $default_template_path;
        }
        $template_path = apply_filters('wpsm_template_path', $template_path);

        if (is_file($template_path)) {
            extract($variables);
            ob_start();

            if ('always' == $require) {
                require($template_path);
            } else {
                require_once($template_path);
            }

            $template_content = apply_filters('wpsm_template_content', ob_get_clean(), $default_template_path, $template_path, $variables);
        } else {
            $template_content = '';
        }

        do_action('wpsm_render_template_post', $default_template_path, $variables, $template_path, $template_content);
        return $template_content;
    }

// Updating widget replacing old instances with new
    public function update($new_instance, $old_instance)
    {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        $instance['options-depth'] = $new_instance['options-depth'];
        $instance['options-group'] = $new_instance['options-group'];
        $instance['options-reverse'] = $new_instance['options-reverse'];
        $instance['options-link'] = (!empty($new_instance['options-link'])) ? $new_instance['options-link'] : '<a title="%title%" href="%permalink%">%title%</a>';
        $instance['options-inc-exc'] = $new_instance['options-inc-exc'];
        $instance['options-group-only'] = $new_instance['options-group-only'];
        $instance['options-post-id'] = (!empty($new_instance['options-post-id'])) ? strip_tags($new_instance['options-post-id']) : '';
        $instance['options-category'] = (isset($new_instance['options-category'])) ? $new_instance['options-category'] : array();
        $instance['options-tag'] = (isset($new_instance['options-tag'])) ? $new_instance['options-tag'] : array();
        $instance['options-user'] = (isset($new_instance['options-user'])) ? $new_instance['options-user'] : array();
        $instance['options-format'] = (isset($new_instance['options-format'])) ? $new_instance['options-format'] : array();
        $instance['options-post-type'] = (isset($new_instance['options-post-type'])) ? $new_instance['options-post-type'] : array();
        return $instance;
    }
} // Class wpsm_widget ends here

// Register and load the widget
function wpsm_load_widget()
{
    register_widget('wpsm_widget');
}

add_action('widgets_init', 'wpsm_load_widget');