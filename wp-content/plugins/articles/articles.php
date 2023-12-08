<?php
/*
Plugin Name: Articles
Version: 1.0
Author: test
Description: This plugin generates a list of articles based on specified date range using shortcode.
*/


function custom_articles_list_menu() {
    add_menu_page(
        'Articles',
        'Articles',
        'manage_options',
        'custom-articles-list',
        'custom_articles_list_page_callback',
        'dashicons-list-view'
    );
}

add_action('admin_menu', 'custom_articles_list_menu');


function custom_articles_list_shortcode($atts) {
    $atts = shortcode_atts(array(
        'from' => '',
        'to' => '',
    ), $atts);

    $from_date = get_option('from_date');
    $to_date = get_option('to_date');

    if (empty($from_date) || empty($to_date)) {
        return 'Please set "from" and "to" dates in plugin settings.';
    }

    $args = array(
        'post_type' => 'post',
        'posts_per_page' => 5,
        'paged' => get_query_var('paged') ? get_query_var('paged') : 1,
        'date_query' => array(
            'after' => $from_date,
            'before' => $to_date,
            'inclusive' => true,
        ),
    );

    $query = new WP_Query($args);

    ob_start();

    if ($query->have_posts()) {
        echo '<div class="article-list">';
        foreach ($query->posts as $post) {
            setup_postdata($post);
            ?>
            <div class="article-card">
                <div class="article__info">
                    <div class="article__info__img">
                        <?php
                        if (has_post_thumbnail($post->ID)) {
                            $thumbnail_url = get_the_post_thumbnail_url($post->ID, 'thumbnail');
                            ?>
                            <img src="<?php echo esc_url($thumbnail_url); ?>" alt="<?php echo esc_attr(get_the_title($post->ID)); ?>" >
                            <?php
                        }
                        ?>
                    </div>

                    <h2><?php echo esc_html($post->post_title); ?></h2>
                </div>

                <span class="date"><?php echo get_the_date('d.m.Y'); ?></span>
                <p><?php echo wp_trim_words(get_the_excerpt($post->ID), 20); ?></p>
            </div>

            <div class="popup-content" style="display: none;">
                <div class="popup-inner">
                    <div class="pop__up__close">
                        <button class="close-popup"><i class="fas fa-times"></i></button>
                    </div>
                    <div class="popup__image">
                        <img class="popup-image" src="<?php echo esc_url($thumbnail_url); ?>" alt="">
                    </div>
                    <span class="date"><?php echo get_the_date('d.m.Y'); ?></span>
                    <h2 class="popup-title"><?php echo esc_html($post->post_title); ?></h2>
                    <p class="popup-excerpt"><?php echo wp_trim_words(get_the_excerpt($post->ID), 20); ?></p>
                    <a class="popup-read-more" href="<?php echo esc_url(get_permalink($post->ID)); ?>В">Read more</a>
                </div>
            </div>

            <?php
        }
        echo '<div class="pagination">';
        $big = 999999999;
        echo paginate_links(array(
            'base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
            'format' => '?paged=%#%',
            'current' => max(1, get_query_var('paged')),
            'total' => $query->max_num_pages,
            'prev_text' => __('&laquo; Попередня'),
            'next_text' => __('Наступна &raquo;'),
        ));
        echo '</div>';
        echo '</div>';

    } else {
        echo 'No articles found';
    }

    wp_reset_postdata();

    $output = ob_get_clean();

    return $output;
}
add_shortcode('custom_articles_list', 'custom_articles_list_shortcode');


function custom_articles_list_page_callback() {
    ?>
    <div class="wrap">
        <h1>Articles Settings</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('custom_articles_list_settings');
            do_settings_sections('custom_articles_list_settings');
            ?>
            <h2>Date settings</h2>
            <table class="form-table">
                <tr>
                    <th scope="row"><label for="from_date">From date:</label></th>
                    <td><input type="date" id="from_date" name="from_date" value="<?php echo esc_attr(get_option('from_date')); ?>" /></td>
                </tr>
                <tr>
                    <th scope="row"><label for="to_date">To date:</label></th>
                    <td><input type="date" id="to_date" name="to_date" value="<?php echo esc_attr(get_option('to_date')); ?>" /></td>
                </tr>
            </table>
            <?php
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

function custom_articles_list_settings_init() {
    register_setting('custom_articles_list_settings', 'from_date');
    register_setting('custom_articles_list_settings', 'to_date');
}
add_action('admin_init', 'custom_articles_list_settings_init');




