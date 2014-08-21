<?php

/**
 * Created by PhpStorm.
 * User: benohead
 * Date: 09.05.14
 * Time: 13:55
 */
class WPSM_Menu_Widget extends WP_Widget
{

    function __construct()
    {
        parent::__construct(
            'wpsm_menu_widget',
            __('Menu Site Map', 'wpsm_menu_widget_domain'),
            array('description' => __('WP Site Mapping', 'wpsm_menu_widget_domain'),)
        );
    }

    public function widget($args, $instance)
    {
        $title = apply_filters('widget_title', $instance['title']);
        echo $args['before_widget'];
        if (!empty($title))
            echo $args['before_title'] . $title . $args['after_title'];
        wp_nav_menu(array('menu' => $instance['menu'], 'menu_class' => 'menu wpsm-menu'));
        echo $args['after_widget'];
    }

    public function form($instance)
    {
        if (isset($instance['title'])) {
            $title = $instance['title'];
        } else {
            $title = __('New title', 'wpsm_menu_widget_domain');
        }
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>"
                   name="<?php echo $this->get_field_name('title'); ?>" type="text"
                   value="<?php echo esc_attr($title); ?>"/>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('menu'); ?>"><?php _e('Menu:'); ?></label>
            <?php $menus = get_terms('nav_menu'); ?>
            <select class="widefat" style="min-width: 190px;"
                    id="<?php echo $this->get_field_id('menu'); ?>"
                    name="<?php echo $this->get_field_name('menu'); ?>">
                <?php foreach ($menus as $menu) { ?>
                    <option
                        value="<?php echo esc_attr($menu->term_id); ?>" <?php selected($menu->term_id, $instance['menu']); ?>><?php echo esc_html($menu->name); ?></option>
                <?php } ?>
            </select>
        </p>
    <?php
    }

    public function update($new_instance, $old_instance)
    {
        $instance = array();
        if (isset($new_instance['title'])) {
            $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        } else {
            $instance['title'] = $old_instance['title'];
        }
        if (isset($new_instance['menu'])) {
            $instance['menu'] = $new_instance['menu'];
        } else {
            $instance['menu'] = $old_instance['menu'];
        }
        return $instance;
    }
} // Class wpsm_menu_widget ends here

// Register and load the widget
function wpsm_load_menu_widget()
{
    register_widget('wpsm_menu_widget');
}

add_action('widgets_init', 'wpsm_load_menu_widget');