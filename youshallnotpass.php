<?php
/*
Plugin Name: You Shall Not Pass
Description: A plugin to control the visibility of specific pages for logged-in users.
Author: Christopher Spradlin
*/

// Create the plugin menu
add_action('admin_menu', 'you_shall_not_pass_menu');

function you_shall_not_pass_menu() {
    add_menu_page('You Shall Not Pass Settings', 'You Shall Not Pass', 'manage_options', 'you_shall_not_pass_settings', 'you_shall_not_pass_options', 'dashicons-shield');
}

// Define the plugin options page
function you_shall_not_pass_options() {
    // Check user capabilities
    if (!current_user_can('manage_options')) {
        return;
    }

    // Get all the pages
    $pages = get_pages();
    $page_options = array();
    foreach ($pages as $page) {
        $page_options[$page->ID] = $page->post_title;
    }

    // Save the selected page and visibility settings
    if (isset($_POST["submit"])) {
        update_option('selected_page_id', $_POST["selected_page_id"]);
        update_option('page_visibility', isset($_POST["page_visibility"]) ? 'yes' : 'no');
        echo '<div id="setting-error-settings_updated" class="updated settings-error notice is-dismissible"> 
        <p><strong>Settings saved.</strong></p></div>';
    }
    $selected_page_id = get_option('selected_page_id');
    $page_visibility = get_option('page_visibility');

    // Display the settings form
    ?>
    <div class="wrap">
        <h1>You Shall Not Pass Settings</h1>
        <form method="post" action="">
            <label for="selected_page_id">Select a page:</label>
            <select name="selected_page_id" id="selected_page_id">
                <?php
                foreach ($page_options as $page_id => $page_title) {
                    printf('<option value="%s" %s>%s</option>', $page_id, selected($selected_page_id, $page_id, false), $page_title);
                }
                ?>
            </select>
            <br><br>
            <input type="checkbox" id="page_visibility" name="page_visibility" <?php if ($page_visibility == 'yes') echo 'checked'; ?>>
            <label for="page_visibility">Visible only to logged-in users</label>
            <br><br>
            <input type="submit" name="submit" value="Save" class="button button-primary">
        </form>
        <div style="text-align: center; margin-top: 20px;">
            <img src="<?php echo plugin_dir_url(__FILE__) . 'logo.png'; ?>" alt="Plugin Logo" style="width: 30%;" />
        </div>
    </div>
    <?php
}

// Check if the current user is logged in and the selected page is set to be visible only to logged-in users
add_action('template_redirect', 'you_shall_not_pass_check');

function you_shall_not_pass_check() {
    $selected_page_id = get_option('selected_page_id');
    $page_visibility = get_option('page_visibility');

    if ($page_visibility == 'yes' && is_page($selected_page_id) && !is_user_logged_in()) {
        auth_redirect();
        exit;
    }
}
?>
