<?php
$title = apply_filters('widget_title', $instance['title']);
echo $args['before_widget'];
if (!empty($title))
    echo $args['before_title'] . $title . $args['after_title'];

if ($in_widget) {
    ?><nav><?php
}

$options_post_id = array();
if (!empty($instance['options-post-id'])) {
    foreach (explode(',', $instance['options-post-id']) as $id) {
        $id2 = explode('-', $id);
        if (count($id2) == 1) {
            array_push($options_post_id, $id2[0]);
        } else {
            for ($i = $id2[0]; $i <= $id2[1]; $i++) {
                array_push($options_post_id, $i);
            }
        }
    }
}
$options_category = implode(',', $instance['options-category']);
if (!empty($options_category)) {
    $args = array('cat' => $options_category, 'post_status' => 'publish', 'posts_per_page' => -1);
    $post_list = get_posts($args);
    foreach ($post_list as $post) {
        array_push($options_post_id, $post->ID);
    }
}

$options_tag = implode(',', $instance['options-tag']);
if (!empty($options_tag)) {
    $args = array('tag_id' => $options_tag, 'post_status' => 'publish', 'posts_per_page' => -1);
    $post_list = get_posts($args);
    foreach ($post_list as $post) {
        array_push($options_post_id, $post->ID);
    }
}

$options_post_format = implode(',', $instance['options-format']);
if (!empty($options_post_format)) {
    $args = array(
        'numberposts' => -1,
        'tax_query' => array(
            array(
                'taxonomy' => 'post_format',
                'field' => 'slug',
                'operator' => 'IN',
                'terms' => $instance['options-format']
            )
        )
    );
    $post_list = get_posts($args);
    foreach ($post_list as $post) {
        array_push($options_post_id, $post->ID);
    }

}

$options_post_id = implode(',', $options_post_id);

$options_post_type = implode("', '", $instance['options-post-type']);
if (!empty($options_post_type)) {
    $options_post_type = "'" . $options_post_type . "'";
}

$options_author = implode(',', $instance['options-user']);

$group_by = '';
$order_by = '';

switch ($instance['options-group']) {
    case 'title':
        $order_by = '`posts`.`post_title`';
        break;
    case 'date':
        $group_by = 'date';
        $order_by = '`posts`.`post_date`';
        break;
    case 'author':
        $group_by = 'author';
        $order_by = '`posts`.`post_date`';
        break;
    case 'category':
        $group_by = 'category';
        $order_by = '`posts`.`post_date`';
        break;
    case 'tag':
        $group_by = 'tag';
        $order_by = '`posts`.`post_date`';
        break;
}

if (isset($instance['options-reverse']) && $instance['options-reverse'] == 1) {
    $order_by = $order_by . " DESC";
}

switch ($group_by) {
    case '':
        $count = 0;
        echo WordPress_Site_Mapping::get_instance()->get_post_tree_level(0, 0, $instance['options-depth'], $instance['options-inc-exc'], $options_post_id, $options_post_type, $options_author, $instance['options-link'], $order_by, '', '', $count, $options_category, $options_tag, $instance['options-group-only']);
        break;
    case 'author':
        $order="ASC";
        if (isset($instance['options-reverse']) && $instance['options-reverse'] == 1) {
            $order="DESC";
        }
        $allUsers = get_users('orderby=display_name&order='.$order);
        ?>
        <ul class="sitemap_list_users">
            <?php
            foreach ($allUsers as $currentUser) {
                if (!in_array('subscriber', $currentUser->roles)) {
                    $count = 0;
                    $echo = WordPress_Site_Mapping::get_instance()->get_post_tree_level(0, 0, $instance['options-depth'], $instance['options-inc-exc'], $options_post_id, $options_post_type, $options_author, $instance['options-link'], $order_by, '`posts`.`post_author` = ' . $currentUser->ID, '', $count, $options_category, $options_tag, $instance['options-group-only']);
                    if ($count > 0) {
                        ?>
                        <li class="sitemap_list_user sitemap_list_user_<?php echo get_author_posts_url($currentUser->ID); ?>">
                            <a href="<?php echo get_author_posts_url($currentUser->ID); ?>"><?php echo esc_html($currentUser->display_name)." ($count)"; ?></a>
                            <?php
                            echo $echo;
                            ?>
                        </li>
                    <?php
                    }
                }
            }
            ?>
        </ul>
        <?php
        break;
    case 'category':
        $categories = get_terms('category');
        if (isset($instance['options-reverse']) && $instance['options-reverse'] == 1) {
            $categories = array_reverse($categories);
        }
        ?>
        <ul class="sitemap_list_categories">
            <?php
            foreach ($categories as $category) {
                if (!empty($options_category)>0 && in_array($category->term_id, $instance['options-category']) && $instance['options-inc-exc'] == 1) continue;
                if (!empty($options_category)>0 && !in_array($category->term_id, $instance['options-category']) && $instance['options-inc-exc'] == 0) continue;
                $count = 0;
                $echo = WordPress_Site_Mapping::get_instance()->get_post_tree_level(0, 0, $instance['options-depth'], $instance['options-inc-exc'], $options_post_id, $options_post_type, $options_author, $instance['options-link'], $order_by, '`term_taxonomy`.`term_id` = ' . $category->term_id, "INNER JOIN $wpdb->term_relationships as `term_relationships` ON `posts`.ID = `term_relationships`.`object_id` INNER JOIN $wpdb->term_taxonomy as `term_taxonomy` ON `term_relationships`.`term_taxonomy_id` = `term_taxonomy`.`term_taxonomy_id`", $count, $options_category, $options_tag, $instance['options-group-only']);
                if ($count > 0) {
                    ?>
                    <li class="sitemap_list_category sitemap_list_category_<?php echo $category->term_id; ?>">
                        <a href="<?php echo get_category_link($category->term_id); ?>"><?php echo esc_html($category->name)." ($count)"; ?></a>
                        <?php
                        echo $echo;
                        ?>
                    </li>
                <?php
                }
            }
            ?>
        </ul>
        <?php
        break;
    case 'tag':
        $tags = get_terms('post_tag');
        if (isset($instance['options-reverse']) && $instance['options-reverse'] == 1) {
            $tags = array_reverse($tags);
        }
        ?>
        <ul class="sitemap_list_tags">
            <?php
            foreach ($tags as $tag) {
                if (!empty($options_tag)>0 && in_array($tag->term_id, $instance['options-tag']) && $instance['options-inc-exc'] == 1) continue;
                if (!empty($options_tag)>0 && !in_array($tag->term_id, $instance['options-tag']) && $instance['options-inc-exc'] == 0) continue;
                $count = 0;
                $echo = WordPress_Site_Mapping::get_instance()->get_post_tree_level(0, 0, $instance['options-depth'], $instance['options-inc-exc'], $options_post_id, $options_post_type, $options_author, $instance['options-link'], $order_by, '`term_taxonomy`.`term_id` = ' . $tag->term_id, "INNER JOIN $wpdb->term_relationships as `term_relationships` ON `posts`.ID = `term_relationships`.`object_id` INNER JOIN $wpdb->term_taxonomy as `term_taxonomy` ON `term_relationships`.`term_taxonomy_id` = `term_taxonomy`.`term_taxonomy_id`", $count, $options_category, $options_tag, $instance['options-group-only']);
                if ($count > 0) {
                    ?>
                    <li class="sitemap_list_tag sitemap_list_tag_<?php echo $tag->term_id; ?>">
                        <a href="<?php echo get_tag_link($tag->term_id); ?>"><?php echo esc_html($tag->name)." ($count)"; ?></a>
                        <?php
                        echo $echo;
                        ?>
                    </li><br/>
                <?php
                }
            }
            ?>
        </ul>
        <?php
        break;
    case 'date':
        $post_dates = WordPress_Site_Mapping::get_instance()->get_post_dates();
        if (isset($instance['options-reverse']) && $instance['options-reverse'] == 1) {
            $post_dates = array_reverse($post_dates);
        }
        ?>
        <ul class="sitemap_list_tags">
            <?php
            foreach ($post_dates as $post_date) {
                $post_date = $post_date["post_date"];
                $count = 0;
                $echo = WordPress_Site_Mapping::get_instance()->get_post_tree_level(0, 0, $instance['options-depth'], $instance['options-inc-exc'], $options_post_id, $options_post_type, $options_author, $instance['options-link'], $order_by, 'DATE_FORMAT(`posts`.`post_date`,"%Y-%m") = "' . $post_date .'"', "", $count, $options_category, $options_tag, $instance['options-group-only']);
                if ($count > 0) {
                    ?>
                    <li class="sitemap_list_tag sitemap_list_date_<?php echo $post_date; ?>">
                        <!--a href="<?php echo get_tag_link($tag->term_id); ?>"-->
                            <?php echo esc_html($post_date)." ($count)"; ?>
                        <!--/a-->
                        <?php
                        echo $echo;
                        ?>
                    </li><br/>
                <?php
                }
            }
            ?>
        </ul>
        <?php
        break;
}

if ($in_widget) {
    ?></nav><?php
}

echo $args['after_widget'];
