# Login with Google — Custom WordPress Plugin

> **Google Identity (OpenID Connect) Social Login Plugin for WordPress**  
> *A WordPress plugin that signs users in with Google's one-tap button and provisions or reuses the matching WordPress account.*

[![WordPress](https://img.shields.io/badge/WordPress-5.0%2B-21759B?style=flat-square&logo=wordpress&logoColor=white)](https://wordpress.org)
[![PHP](https://img.shields.io/badge/PHP-7.4%2B-777BB4?style=flat-square&logo=php&logoColor=white)](https://php.net)
[![Google](https://img.shields.io/badge/Google-Identity-4285F4?style=flat-square&logo=google&logoColor=white)](https://developers.google.com/identity)

---

## 📌 Technical Motivation

Passwordless login reduces registration drop-off. **Login with Google** is an **independent custom WordPress plugin** that adds Google's one-tap sign-in using the Google Identity Services client library, without a heavyweight third-party plugin.

---

## ⚙️ Core Technical Features

1. **Google Identity Services Login**
   - Renders Google's one-tap button (`g_id_signin` with `data-type="standard"`) using the GIS client library (`accounts.google.com/gsi/client`).
   - The returned ID credential is posted to `admin-ajax.php` with action `google_login`.
2. **Server-side ID Token Verification**
   - The handler sends the credential to Google's `tokeninfo` endpoint (`https://oauth2.googleapis.com/tokeninfo?id_token=...`) and reads `email`, `name`, and `picture` from the verified response. The WordPress session is only created from a verified token.
3. **Account Provisioning**
   - An existing WordPress user is looked up by the verified email and signed in with `wp_set_auth_cookie()`.
   - If no user exists, one is created with `wp_insert_user()` and a random 12-character password.
   - The Google picture is sideloaded into the media library with `download_url()` + `media_handle_sideload()`, and only re-fetched when the source URL differs from the stored `google_avatar_compare` user meta.
4. **Admin Settings & Shortcodes**
   - A settings page under **Google Login** for the Client ID and the redirect URL. The settings form is nonce-protected.
   - Shortcodes `[google_login]` (front-end button) and `[admin_google_login]` (button injected into the `wp-login.php` form).

---

## 🚀 Quick Start & Setup

1. Clone into your WordPress plugins directory:
   ```bash
   cd wp-content/plugins/
   git clone https://github.com/huyhhuy1410/google-login.git google-login
   ```
2. Activate **Login with Google** in **WordPress Admin $\rightarrow$ Plugins**.
3. Configure your **Google Client ID** and redirect URL under **Google Login**.
4. Place the shortcode on any login or registration template:
   ```text
   [google_login]
   ```

---

## ⚠️ Known Limitations

These are open items, not hidden behaviour. They are listed so a reviewer does not have to read the code to find them.

* **No nonce on the login AJAX endpoint.** The `google_login` action is public (`wp_ajax_nopriv_`) and does not verify a nonce. Because the ID token is verified server-side this is not an authentication bypass, but a `check_ajax_referer()` call would close the CSRF surface.
* **No provider-id account linking.** Users are matched by email only. A user who changes their Google email is treated as a new account, and there is no `google_sub` column on the WordPress user to link against.
* **`tokeninfo` is used instead of local JWT verification.** Google recommends verifying the ID token signature against Google's public keys (`jwks_uri`) with an audience check. The `tokeninfo` call is a valid but per-request network round trip that returns a decoded payload rather than a verified signature; it is the right choice for a plugin that has no client secret to keep.
* **No automated tests.** The plugin ships no PHPUnit or integration test suite.
* **`get-user.php` in the repository root is dead code.** It is not referenced by any other file and expects a WordPress bootstrap that a standalone root file does not load. It can be deleted.
* **Avatar downloads are unvalidated.** The picture URL is fetched with `download_url()` without an explicit content-type or size check before being sideloaded into the media library.

---

## 🤝 Contributing

Contributions, bug reports, and feature proposals are welcome! Feel free to open an issue or submit a Pull Request.

---

## 📄 License & Provenance Notice

Created by Vo Quang Huy for technical demonstration. Open-source and free of proprietary code.
