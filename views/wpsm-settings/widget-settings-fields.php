<?php if (!$in_editor) { ?>
    <p>
        <label for="<?php echo $widget->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
        <input class="widefat" id="<?php echo $widget->get_field_id('title'); ?>"
               name="<?php echo $widget->get_field_name('title'); ?>" type="text"
               value="<?php echo esc_attr($instance['title']); ?>"/>
    </p>
<?php } ?>
<p>
    <label for="<?php echo $widget->get_field_id('options-link'); ?>"><?php _e('Link format:'); ?></label>
    <input class="widefat" id="<?php echo $widget->get_field_id('options-link'); ?>"
           name="<?php echo $widget->get_field_name('options-link'); ?>" type="text"
           value="<?php echo esc_attr($instance['options-link']); ?>"/>
</p>
<p>
    <label for="<?php echo $widget->get_field_id('options-depth'); ?>"><?php _e('Depth:'); ?></label>
    <select class="widefat" style="min-width: 190px;" id="<?php echo $widget->get_field_id('options-depth'); ?>"
            name="<?php echo $widget->get_field_name('options-depth'); ?>">
        <?php for ($i = 1; $i < 11; $i++) { ?>
            <option
                value="<?= $i ?>" <?php selected($instance['options-depth'], $i); ?>><?= $i ?></option>
        <?php } ?>
    </select>
</p>
<p>
    <label for="<?php echo $widget->get_field_id('options-group'); ?>"><?php _e('Group and sort by:'); ?></label>
    <select class="widefat" style="min-width: 190px;" id="<?php echo $widget->get_field_id('options-group'); ?>"
            name="<?php echo $widget->get_field_name('options-group'); ?>">
        <option value="title" <?php selected($instance['options-group'], 'title'); ?>>Title</option>
        <option value="date" <?php selected($instance['options-group'], 'date'); ?>>Date</option>
        <option value="author" <?php selected($instance['options-group'], 'author'); ?>>Author</option>
        <option value="category" <?php selected($instance['options-group'], 'category'); ?>>Category</option>
        <option value="tag" <?php selected($instance['options-group'], 'tag'); ?>>Tag</option>
    </select>
</p>
<p>
    <label
        for="<?php echo $widget->get_field_id('options-reverse'); ?>"><?php _e('Reverse order:'); ?></label>
    <input type="checkbox" name="<?php echo $widget->get_field_name('options-reverse'); ?>"
           id="<?php echo $widget->get_field_id('options-reverse'); ?>"
           value="1" <?php checked(1, $instance['options-reverse']) ?>/>
</p>
<p>
    <label
        for="<?php echo $widget->get_field_id('options-inc-exc'); ?>"><?php _e('Include all except matching:'); ?></label>
    <input type="checkbox" name="<?php echo $widget->get_field_name('options-inc-exc'); ?>"
           id="<?php echo $widget->get_field_id('options-inc-exc'); ?>"
           value="1" <?php checked(1, $instance['options-inc-exc']) ?>/>
</p>
<p>
    <label
        for="<?php echo $widget->get_field_id('options-group-only'); ?>"><?php _e('Show only group headers:'); ?></label>
    <input type="checkbox" name="<?php echo $widget->get_field_name('options-group-only'); ?>"
           id="<?php echo $widget->get_field_id('options-group-only'); ?>"
           value="1" <?php checked(1, $instance['options-group-only']) ?>/>
</p>
<p>
    <label for="<?php echo $widget->get_field_id('options-post-id'); ?>"><?php _e('Post IDs:'); ?></label>
    <input class="widefat" type="text" name="<?php echo $widget->get_field_name('options-post-id'); ?>"
           id="<?php echo $widget->get_field_id('options-post-id'); ?>"
           value="<?php echo $instance['options-post-id']; ?>" placeholder="e.g. 32,9-19,33">
</p>
<p>
    <label for="<?php echo $widget->get_field_id('options-category'); ?>"><?php _e('Categories:'); ?></label>
    <?php $categories = get_terms('category'); ?>
    <select class="widefat" style="min-width: 190px;" id="<?php echo $widget->get_field_id('options-category'); ?>"
            name="<?php echo $widget->get_field_name('options-category'); ?>[]" size="4"
            multiple="multiple">
        <?php foreach ($categories as $category) { ?>
            <option
                value="<?php echo esc_attr($category->term_id); ?>" <?php echo(in_array($category->term_id, (array)$instance['options-category']) ? 'selected="selected"' : ''); ?>><?php echo esc_html($category->name); ?></option>
        <?php } ?>
    </select>
    <button id="clear-category" class="button-secondary"
            onclick="document.getElementById('<?php echo $widget->get_field_id('options-category'); ?>').selectedIndex = -1;return false;">
        Clear
    </button>
</p>
<p>
    <label for="<?php echo $widget->get_field_id('options-tag'); ?>"><?php _e('Tags:'); ?></label>
    <?php $tags = get_terms('post_tag'); ?>
    <select class="widefat" style="min-width: 190px;" id="<?php echo $widget->get_field_id('options-tag'); ?>"
            name="<?php echo $widget->get_field_name('options-tag'); ?>[]" size="4"
            multiple="multiple">
        <?php foreach ($tags as $tag) { ?>
            <option
                value="<?php echo esc_attr($tag->term_id); ?>" <?php echo(in_array($tag->term_id, (array)$instance['options-tag']) ? 'selected="selected"' : ''); ?>><?php echo esc_html($tag->name); ?></option>
        <?php } ?>
    </select>
    <button id="clear-tag" class="button-secondary"
            onclick="document.getElementById('<?php echo $widget->get_field_id('options-tag'); ?>').selectedIndex = -1;return false;">
        Clear
    </button>
</p>
<p>
    <label for="<?php echo $widget->get_field_id('options-user'); ?>"><?php _e('Authors:'); ?></label>
    <?php
    $allUsers = get_users('orderby=post_count&order=DESC');
    $users = array();
    // Remove subscribers from the list as they won't write any articles
    foreach ($allUsers as $currentUser) {
        if (!in_array('subscriber', $currentUser->roles)) {
            $users[] = $currentUser;
        }
    }
    ?>
    <select class="widefat" style="min-width: 190px;" id="<?php echo $widget->get_field_id('options-user'); ?>"
            name="<?php echo $widget->get_field_name('options-user'); ?>[]" size="4"
            multiple="multiple">
        <?php foreach ($users as $user) { ?>
            <option
                value="<?php echo esc_attr($user->ID); ?>" <?php echo(in_array($user->ID, (array)$instance['options-user']) ? 'selected="selected"' : ''); ?>><?php echo esc_html($user->display_name); ?></option>
        <?php } ?>
    </select>
    <button id="clear-user" class="button-secondary"
            onclick="document.getElementById('<?php echo $widget->get_field_id('options-user'); ?>').selectedIndex = -1;return false;">
        Clear
    </button>
</p>
<p>
    <label for="<?php echo $widget->get_field_id('options-format'); ?>"><?php _e('Post formats:'); ?></label>
    <?php $formats = get_theme_support('post-formats'); ?>
    <select class="widefat" style="min-width: 190px;" id="<?php echo $widget->get_field_id('options-format'); ?>"
            name="<?php echo $widget->get_field_name('options-format'); ?>[]" size="4"
            multiple="multiple">
        <?php
        if (is_array($formats) && count($formats) > 0) {
            ?>
            <option
                value="0" <?php echo(in_array('0', (array)$instance['options-format']) ? 'selected="selected"' : ''); ?>><?php echo get_post_format_string('standard'); ?></option>
            <?php
            foreach ($formats[0] as $format_name) {
                ?>
                <option
                    value="post-format-<?php echo esc_attr($format_name); ?>" <?php echo(in_array("post-format-$format_name", (array)$instance['options-format']) ? 'selected="selected"' : ''); ?>><?php echo esc_html(get_post_format_string($format_name)); ?></option>
            <?php
            }
        }
        ?>
    </select>
    <button id="clear-format" class="button-secondary"
            onclick="document.getElementById('<?php echo $widget->get_field_id('options-format'); ?>').selectedIndex = -1;return false;">
        Clear
    </button>
</p>
<p>
    <label for="<?php echo $widget->get_field_id('options-post-type'); ?>"><?php _e('Post types:'); ?></label>
    <?php $post_types = get_post_types(); ?>
    <select class="widefat" style="min-width: 190px;" id="<?php echo $widget->get_field_id('options-post-type'); ?>"
            name="<?php echo $widget->get_field_name('options-post-type'); ?>[]" size="4"
            multiple="multiple">
        <?php
        foreach ($post_types as $post_type_name) {
            ?>
            <option
                value="<?php echo esc_attr($post_type_name); ?>" <?php echo(in_array($post_type_name, (array)$instance['options-post-type']) ? 'selected="selected"' : ''); ?>><?php echo esc_html(get_post_type_object($post_type_name)->labels->name); ?></option>
        <?php
        }
        ?>
    </select>
    <button id="clear-post-type" class="button-secondary"
            onclick="document.getElementById('<?php echo $widget->get_field_id('options-post-type'); ?>').selectedIndex = -1;return false;">
        Clear
    </button>
</p>
<?php if ($in_editor) { ?>
    <p>
        <label for="<?php echo $widget->get_field_id('class'); ?>"><?php _e('Element class:'); ?></label>
        <input class="widefat" id="<?php echo $widget->get_field_id('class'); ?>"
               name="<?php echo $widget->get_field_name('class'); ?>" type="text"
               value="<?php echo esc_attr($instance['class']); ?>"/>
    </p>
    <p>
        <label for="<?php echo $widget->get_field_id('id'); ?>"><?php _e('Element ID:'); ?></label>
        <input class="widefat" id="<?php echo $widget->get_field_id('id'); ?>"
               name="<?php echo $widget->get_field_name('id'); ?>" type="text"
               value="<?php echo esc_attr($instance['id']); ?>"/>
    </p>
<?php } ?>
