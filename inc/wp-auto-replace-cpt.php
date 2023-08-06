<?php
/**
 * Register WP Auto Replace Pages post type
 */
add_action('init', function () {
    $type = 'advancePageCopy';
    $label = 'WP Auto Replace Pages';
    $arguments = [
        'public' => true,
        'description' => 'WP Auto Replace Pages will replace selected pages with new pre-written content at a future date.',
        'label' => $label,
        'hierarchical' => true,
        'show_in_rest' => true,
    ];
    register_post_type($type, $arguments);
});
//------END Register WP Auto Replace Pages post type

/**
 * Add the option box for our meta fields
 */

function wpbyb_advanceCopy_option_box()
{
    //--Only show on the WP Auto Replace Pages post type
    $currentScreen = get_current_screen();
    $screen = 'advancepagecopy';

    if ($currentScreen->id == $screen): //Only if we're on the correct edit screen
        add_meta_box(
            'wpbyb_advanceCopy_scheduled',
            'Advance Publish',
            'wpbyb_advanceCopy_publish_data',
            $screen,// Post type
            "side",
            "high"
        );
    endif;
}

add_action('add_meta_boxes', 'wpbyb_advanceCopy_option_box');
//--End Add option box

/**
 * Add meta field for the publish date
 */
function wpbyb_advanceCopy_publish_data($post)
{
    $wpbybReplaceCopyDate = get_post_meta($post->ID, 'wpbyb_replaceCopy_date', true);
    ?>
    <label for="wpbyb_replaceCopy_date"><b>Select a date: </b></label>
    <input type="text" name="wpbyb_replaceCopy_date" id="datepickerReplace" placeholder="MM/DD/YYYY"
           value="<?= $wpbybReplaceCopyDate ?>">
    <br/>
    <br/>
    <label for="wpbyb_scheduledPages">Choose a page:</label>
    <br/>
    <?php $wpbybScheduledPages = get_post_meta($post->ID, 'wpbyb_scheduledPages', true);
    ?>
    <select name="wpbyb_scheduledPages">
        <option value="">
            <?php echo esc_attr(__('Select page')); ?></option>
        <?php
        //--Get a list of all pages
        $pages = get_pages();
        //--loop through all pages  to produce the select list
        foreach ($pages as $page) {
            ?>
            <option value="<?= $page->ID ?>" <?php selected($wpbybScheduledPages, $page->ID); ?>><?= $page->post_title ?></option>';
            <?php
        }

        ?>
    </select>
    <?php
}

//--Save all the things
function wpbyb_save_advanceCopy_postdata($post_id)
{
    if (array_key_exists('wpbyb_scheduledPages', $_POST)) {
        update_post_meta(
            $post_id,
            'wpbyb_scheduledPages',
            $_POST['wpbyb_scheduledPages']
        );
    }

    if (array_key_exists('wpbyb_advanceCopy_field', $_POST)) {
        update_post_meta(
            $post_id,
            'wpbyb_advanceCopy_scheduled',
            $_POST['wpbyb_advanceCopy_field']
        );
    }
    if (array_key_exists('wpbyb_replaceCopy_date', $_POST)) {
        update_post_meta(
            $post_id,
            'wpbyb_replaceCopy_date',
            $_POST['wpbyb_replaceCopy_date']
        );
    }
}

add_action('save_post', 'wpbyb_save_advanceCopy_postdata');