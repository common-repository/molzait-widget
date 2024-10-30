<?php
/*
Plugin Name: Molzait Widget
Description: Shows the Molzait Widget on your website.
Version: 1.1.1
Author: Molzait
License: GPL v2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// Add the options page
function molzait_add_options_page() {
    add_options_page('Molzait Settings', 'Molzait Settings', 'manage_options', 'molzait-settings', 'molzait_render_options_page');
}
add_action('admin_menu', 'molzait_add_options_page');

// Render the options page
function molzait_render_options_page() {
    ?>
    <div class="wrap">
        <h1>Molzait Settings</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('molzait_options');
            do_settings_sections('molzait-settings');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

// Register and define the settings
function molzait_settings_init() {
    add_settings_section('molzait_section', 'Molzait Options', '', 'molzait-settings');
    add_settings_field('molzait_restaurant_id', 'Restaurant ID', 'molzait_restaurant_id_callback', 'molzait-settings', 'molzait_section');
    add_settings_field('molzait_open_selectors', 'Elements that open widget on click (CSS selectors, comma separated)', 'molzait_open_selectors_callback', 'molzait-settings', 'molzait_section');
    add_settings_field('molzait_hide_button', 'Hide the widget launch button', 'molzait_hide_button_callback', 'molzait-settings', 'molzait_section');

    register_setting('molzait_options', 'molzait_restaurant_id');
    register_setting('molzait_options', 'molzait_open_selectors');
    register_setting('molzait_options', 'molzait_hide_button', array('type' => 'boolean'));
}
add_action('admin_init', 'molzait_settings_init');

// Callback function for the new Hide Button field
function molzait_hide_button_callback() {
    $hide_button = get_option('molzait_hide_button');
    echo '<input type="checkbox" name="molzait_hide_button" value="1"' . checked(1, $hide_button, false) . '/>';
}

// Callback function for Restaurant ID field
function molzait_restaurant_id_callback() {
    $restaurant_id = get_option('molzait_restaurant_id');
    echo '<input type="text" name="molzait_restaurant_id" value="' . esc_attr($restaurant_id) . '"/>';
}

// Callback function for Open Selectors field
function molzait_open_selectors_callback() {
    $open_selectors = get_option('molzait_open_selectors');
    echo '<input type="text" name="molzait_open_selectors" value="' . esc_attr($open_selectors) . '"/>';
}

// Inject the script into wp_head
function molzait_inject_script() {
    $restaurant_id = get_option('molzait_restaurant_id');
    $open_selectors = get_option('molzait_open_selectors') ?? "";
    $hide_button = get_option('molzait_hide_button') ?? false;

    if ($restaurant_id) {
        $hide_button_attr = $hide_button ? 'data-hide-button="true"' : '';
        echo '<script molzait type="text/javascript" src="https://reserve.molzait.com/assets/embed.js" data-restaurant="' . esc_attr($restaurant_id) . '" data-open-selectors="' . esc_attr($open_selectors) . '" ' . $hide_button_attr . ' defer></script>';
    }
}
add_action('wp_head', 'molzait_inject_script');
