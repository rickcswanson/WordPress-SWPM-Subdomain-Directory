<?php
/*
 Plugin Name: Subdomain Level Directory
 Description: A membership directory that compiles a list of all members of the current site/subdomain from the Simple Membership Plugin.
 Version: 1.0
 Author: Rick Swanson | Digital Dialect, Inc.
 */

// Add shortcode for displaying the directory
add_shortcode('charter_directory', 'display_charter_directory');

function display_charter_directory($atts) {
    global $wpdb;
    
    // Get current Blog ID
    $current_blog_id = get_current_blog_id();
    
    // Get prefix for the current subdomain's tables
    $table_prefix = $wpdb->get_blog_prefix($current_blog_id);
    
    // Construct the table name for the Simple Membership Plugin
    $members_table_name = $table_prefix . 'swpm_members_tbl';
    
    $custom_form_table = $table_prefix . 'swpm_form_builder_custom';
    
    // Query the database to get the members for the current subdomain
    $members_query = $wpdb->prepare("SELECT * FROM $members_table_name");
    $members = $wpdb->get_results($members_query);

    // Check for database errors
    if (!$members && $wpdb->last_error) {
        return 'Error retrieving members: ' . $wpdb->last_error;
    }

    // Function to get custom data for a specific member and field
    function get_custom_data($user_id, $field_id) {
        global $wpdb;
        $custom_table_name = $wpdb->prefix . 'swpm_form_builder_custom'; // Replace with your custom table name

        $custom_data_query = $wpdb->prepare("
            SELECT value
            FROM $custom_table_name
            WHERE user_id = %d AND field_id = %d
        ", $user_id, $field_id);

        $custom_data = $wpdb->get_var($custom_data_query);
        return !empty($custom_data) ? $custom_data : '------';
    }

    // Load HTML template
    ob_start();
    include(plugin_dir_path(__FILE__) . 'subdomain-level-directory-template.php');
    $output = ob_get_clean();
    
    return $output;
}


function get_membership_level_name($level) {
    // Define a mapping of numeric values to membership level names
    $membership_levels = array(
        1 => 'Membership 1',
        2 => 'Membership 2'
        // Add more mappings as needed
    );

    // Check if the given level exists in the mapping
    if (isset($membership_levels[$level])) {
        return $membership_levels[$level];
    } else {
        return 'Unknown'; // Default value if level is not found
    }
}

?>
