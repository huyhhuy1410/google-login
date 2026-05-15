# Google Login for WordPress

A WordPress plugin that adds Google account login to the frontend and optionally to the WordPress admin login screen. It includes a settings page, shortcode rendering, Google Identity Services integration, token verification, user provisioning, and avatar import.

## What It Does

- Adds a Google login button through shortcode.
- Optionally displays the Google login button on the WordPress login screen.
- Provides a WordPress admin settings page for Google OAuth configuration.
- Uses Google Identity Services on the frontend.
- Verifies Google ID tokens before authenticating users.
- Creates WordPress users from verified Google profile data when needed.
- Logs existing users in when the Google email matches an existing WordPress account.
- Imports and stores Google profile images as WordPress avatar metadata.
- Supports redirect configuration after successful login.

## Shortcodes

- `[google_login]` renders the frontend Google login button.
- `[admin_google_login]` renders the admin/login-screen button.

## Main Features

- Configurable Google Client ID.
- Optional Client Secret setting field for projects that require it.
- Optional redirect URL after login.
- Optional display on the WordPress admin login form.
- Custom class and ID settings for the rendered shortcode button.
- Admin tabs for setup guide, general settings, and usage.

## Technical Notes

- Main class: `Google_Login`
- Login method class: `Google_Login_Method`
- Admin page class: `Google_Login_Admin_Page`
- Shortcode classes:
  - `google_login\Shortcodes\Google_Btn`
  - `google_login\Shortcodes\Admin_Google_Btn`
- AJAX action: `google_login`
- Frontend data object: `googleLoginData`
- Frontend assets:
  - `assets/js/login.js`
  - `assets/css/login.css`
- Token verification endpoint: Google OAuth token info API.

## Installation

1. Upload the plugin folder to `wp-content/plugins/google-login`.
2. Activate the plugin in WordPress Admin.
3. Open the Google Login settings page.
4. Enter the Google OAuth Client ID.
5. Add `[google_login]` to any page, template, or builder field where the login button should appear.
