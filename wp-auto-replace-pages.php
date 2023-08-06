<?php
/**
 * Plugin Name: WP Auto Replace Pages
 * Description: Automatically replace selected page content on a pre-selected date. This allows us to pre-write updates well in advance and set the date we want our advanced copy to replace the current copy.
 * Author: Bill Fitzgerald
 * Version: 1.0.0-alpha
 * Plugin URI: https://wpbybill.com/wpbyb-auto-replace-pages/
 */

/**
 * Enqueue our scripts
 */
function wpbyb_add_scripts(): void
{
    wp_register_style('jquery-ui', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css');
    if (!wp_script_is('jquery-ui', 'enqueued')) {
        wp_enqueue_style('jquery-ui');
    }
    if (!wp_script_is('jquery-ui-datepicker', 'enqueued')) {
        wp_enqueue_script('jquery-ui-datepicker');
    }
    wp_enqueue_script('wpbyb-admin-js', plugins_url('/js/wpbyb-admin.js', __FILE__), array('jquery', 'jquery-ui-datepicker'));

}

add_action('admin_enqueue_scripts', 'wpbyb_add_scripts');
//--Get the CPT set up.
include_once "inc/wp-auto-replace-cpt.php";

/**
 * @return void
 * Replace post content as  scheduled
 */

function wpbyb_au_replace_post(): void
{
    $currentTime = time();

    $args = array(
        'posts_per_page' => -1, //--get all posts
        'post_type' => 'advancePageCopy',
        'post_status' => 'publish',
    );

    $wpbybAdvPosts = get_posts($args);

    if ($wpbybAdvPosts) {

        foreach ($wpbybAdvPosts as $advPost) {
            //--loop through our Replacement pages and compare dates, replace if it's time
            $replaceDate = get_post_meta($advPost->ID, 'wpbyb_replaceCopy_date', true);
            $replacePage = get_post_meta($advPost->ID, 'wpbyb_scheduledPages', true);
            $postNew = get_post($advPost->ID);
            $newContent = $postNew->post_content;
            $newTitle = $postNew->post_title;
            /**
             * if it's not time then get out now
             */
            if ($currentTime < strtotime($replaceDate)):
                return;
            endif;
            /**
             * if it is time then insert Advanced Page Copy into  the appropriate page
             */
            wp_update_post(array(
                'ID' => $replacePage,
                'post_title' => $newTitle,
                'post_content' => $newContent
            ));

        }
    }
}

add_action('init', 'wpbyb_au_replace_post');