# Login with Google — Custom WordPress Authentication Plugin

> **OAuth 2.0 & OpenID Connect Social Authentication for WordPress**  
> *A modular WordPress plugin integrating Google OAuth 2.0 authentication for fast, passwordless user registration and login.*

[![WordPress](https://img.shields.io/badge/WordPress-5.0%2B-21759B?style=flat-square&logo=wordpress&logoColor=white)](https://wordpress.org)
[![PHP](https://img.shields.io/badge/PHP-7.4%2B-777BB4?style=flat-square&logo=php&logoColor=white)](https://php.net)
[![Google Cloud](https://img.shields.io/badge/Google-OAuth%202.0-4285F4?style=flat-square&logo=google&logoColor=white)](https://console.cloud.google.com/)

---

## 📌 Technical Motivation

Passwordless login significantly reduces registration drop-off rates. **Login with Google** is an **independent custom WordPress plugin** designed to enable 1-click Google sign-in using OAuth 2.0 and Google API client libraries.

---

## ⚙️ Core Technical Features

1. **Google OAuth 2.0 & Identity Protocol**
   - Secure authorization handling via Google Identity OAuth endpoints (`https://accounts.google.com/o/oauth2/v2/auth`).
   - Verifies Google ID tokens and fetches user profile details (`email`, `given_name`, `family_name`, `picture`).
2. **Account Provisioning & Security**
   - Maps Google email addresses to WordPress user accounts (`wp_users`).
   - Sets secure session authentication cookies via `wp_set_auth_cookie()` upon verification.
3. **Admin Settings & Shortcode Renderer**
   - Admin menu for managing **Client ID** and **Client Secret**.
   - Embeddable shortcode `[google_login_btn]` for custom button placements across pages and modals.

---

## 🚀 Quick Start & Setup

1. Clone into your WordPress plugins directory:
   ```bash
   cd wp-content/plugins/
   git clone https://github.com/huyhhuy1410/google-login.git google-login
   ```
2. Activate **Login with Google** in **WordPress Admin $\rightarrow$ Plugins**.
3. Configure your **Google Client ID** and **Client Secret** under **Settings $\rightarrow$ Google Login**.
4. Place the shortcode on any login or registration template:
   ```text
   [google_login_btn]
   ```

---

## 📄 License & Provenance Notice

Created by Vo Quang Huy for technical demonstration. Open-source and free of proprietary code.
