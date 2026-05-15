<?php
// Include WordPress core functionalities if needed, adjust the path as necessary
require_once(dirname(__FILE__) . '/wp-load.php');

// Extract the 'credential' from the URL
if (isset($_GET['credential'])) {
    $google_credential = sanitize_text_field($_POST['credential']);

    // Prepare the AJAX request to handle the login
    $ajax_url = esc_url(admin_url('admin-ajax.php'));

    // Execute a server-side request to login via AJAX
    $response = wp_remote_post($ajax_url, [
        'body' => [
            'action' => 'google_login',
            'credential' => $google_credential
        ]
    ]);

    if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) == 200) {
        $body = json_decode(wp_remote_retrieve_body($response), true);
        if ($body['success']) {
            // Redirect to the specified URL on successful login
            wp_redirect($body['data']['redirect_url']);
            exit;
        }
    }

    // Handle failure (e.g., redirect to a login page or show an error message)
    wp_redirect(home_url('/login-failed')); // Change this to your failure redirect page
    exit;
} else {
    // Redirect if no credential found
    wp_redirect(home_url('/login-failed'));
    exit;
}
