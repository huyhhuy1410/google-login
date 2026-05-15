<?php
if (!defined('ABSPATH')) {
	exit;
}

class Google_Login_Method
{
	private $client_id;
	private $redirect_url;
	private $show_admin;

	public function __construct() {}

	public function __call_action($client_id, $client_secret, $redirect_url, $show_admin)
	{
		$this->client_id = sanitize_text_field($client_id);
		$this->redirect_url = esc_url_raw($redirect_url);
		$this->show_admin = sanitize_text_field($show_admin);

		add_action('wp_head', [$this, 'url_google_dev']);
		// add_action('wp_head', [$this, 'url_header']);
		add_action('wp_enqueue_scripts', [$this, 'google_login_style']);
		// add_action('wp_ajax_nopriv_google_ajax_login_social', [$this, 'google_ajax_login_social']);
		// add_action('wp_ajax_nopriv_google_ajax_social_data', [$this, 'google_ajax_social_data']);
		add_action('wp_ajax_nopriv_google_login', [$this, 'handle_google_login']);

		if ($this->show_admin === 'yes') {
			add_action('login_head', [$this, 'add_google_login_head']);
			add_action('login_enqueue_scripts', [$this, 'google_login_style']);
			add_action('login_form', [$this, 'add_google_login_to_login_form']);
		}
	}

	public function url_google_dev()
	{
		echo '<script src="https://accounts.google.com/gsi/client"></script>';
	}

	public function url_header()
	{
		echo '<meta name="google-signin-client_id" content="' . esc_attr($this->client_id) . '">';
	}

	public function google_login_style()
	{
		wp_enqueue_style('google-login-css', GOOGLE_LOGIN_URL . 'assets/css/login.css', [], false);
		// wp_enqueue_script('google-login-js', GOOGLE_LOGIN_URL . 'assets/js/login.js', [], false, true);
		wp_enqueue_script('google-login-js', GOOGLE_LOGIN_URL . 'assets/js/login.js', [], false, true);

		wp_localize_script('google-login-js', 'googleLoginData', [
			'client_id'    => $this->client_id,
			'redirect_uri' => get_site_url(),
			'login_uri' => get_site_url() . '/?google-login-ajax',
			'ajaxURL'      => admin_url('admin-ajax.php'),
			'credential'   => isset($_POST['credential']) ? $_POST['credential'] : '',
		]);
		// wp_enqueue_script('google-login-api-js', 'https://apis.google.com/js/platform.js?onload=renderButton', [], false, true);
	}

	public function add_google_login_head()
	{
		// $this->url_header();
		$this->url_google_dev();
	}

	public function add_google_login_to_login_form()
	{
		echo do_shortcode('[admin_google_login]');
	}

	// public function google_ajax_social_data()
	// {
	// 	$data = sanitize_text_field($_POST['code']);
	// 	if (sanitize_text_field($_POST['type']) === 'google') {
	// 		$data = [
	// 			'id' => sanitize_text_field($data['id']),
	// 			'user_name' => sanitize_text_field($data['name']),
	// 			'user_email' => sanitize_email($data['email']),
	// 		];
	// 	}
	// 	$email = $data['user_email'];
	// 	$name = $data['user_name'];
	// 	$fbid = $data['id'];

	// 	if ($fbid) {
	// 		$user = get_user_by('email', $email) ?: get_user_by('login', 'google_social_' . $fbid);
	// 		if ($user) {
	// 			wp_clear_auth_cookie();
	// 			wp_set_auth_cookie($user->ID, true);
	// 			do_action('wp_login', $user->user_login, $user);
	// 			wp_send_json_success(['message' => __('Login successful, redirecting shortly...', 'google-login'), 'url' => get_author_posts_url($user->ID)]);
	// 		} else {
	// 			wp_send_json_success(['status' => 'register', 'message' => ['email' => $email, 'name' => $name, 'login' => 'social_' . $fbid]]);
	// 		}
	// 	} else {
	// 		wp_send_json_error(['message' => __('An error occurred while logging in. Please try again later.', 'google-login')]);
	// 	}
	// }

	// public function google_ajax_login_social()
	// {
	// 	$form = array_map('sanitize_text_field', $_POST['data']);
	// 	$userRedirect = esc_url_raw($_POST['userRedirect']);
	// 	$login = $form['login'] ?: $form['email'];

	// 	$user_data = get_user_by('email', $form['email']);
	// 	$picture_url = esc_url_raw($form['image']);
	// 	error_log(implode(',', $form));
	// 	if (!$user_data) {
	// 		$args = [
	// 			'user_login' => $login,
	// 			'user_email' => $form['email'],
	// 			'user_pass' => null,
	// 			'user_nicename' => $form['name'],
	// 			'display_name' => $form['name'],
	// 			'nickname' => $form['name'],
	// 			'role' => 'subscriber'
	// 		];
	// 		$user_id = wp_insert_user($args);
	// 		if (is_wp_error($user_id)) {
	// 			wp_send_json_error(['message' => $user_id->get_error_message()]);
	// 		}
	// 		$user_data = get_userdata($user_id);
	// 		update_user_meta($user_id, 'google_avatar', $this->upload_avatar_from_url($picture_url, 'google_avatar_' . $form['id']));
	// 	}

	// 	wp_clear_auth_cookie();
	// 	wp_set_auth_cookie($user_data->ID, true);
	// 	do_action('wp_login', $user_data->user_login, $user_data);
	// 	wp_send_json_success(['redirect' => $userRedirect]);
	// }

	public function handle_google_login()
	{
		$token = sanitize_text_field($_POST['credential']);
		// error_log($token);
		// echo '<script>alert("' . $token . '")</script>';

		$payload = $this->verify_google_token($token);
		// error_log(implode(', ', array_map(
		// 	function ($v, $k) {
		// 		return sprintf("%s='%s'", $k, $v);
		// 	},
		// 	$payload,
		// 	array_keys($payload)
		// )));
		if (!empty($payload['email'])) {
			// Get the user avatar URL from the payload (if available)
			$avatar_url = !empty($payload['picture']) ? sanitize_url($payload['picture']) : '';
			// error_log($avatar_url);
			$email = sanitize_email($payload['email']);
			$name = !empty($payload['name']) ? sanitize_text_field($payload['name']) : '';
			$user = get_user_by('email', $email);
			if (!$user) {
				// $user_id = wp_create_user($email, wp_generate_password(12, false), $email);
				$user_id = wp_insert_user([
					'user_login' => $email,
					'user_pass'  => wp_generate_password(12, false),
					'user_email' => $email,
					'display_name' => $name,
					'first_name' => $name, // Optionally split the name if needed
				]);
				if (is_wp_error($user_id)) {
					wp_send_json_error(['message' => __('Failed to create account.', 'google-login')]);
				}
				$user = get_user_by('id', $user_id);
			} // Check the stored avatar URL for comparison
			$stored_avatar_url = get_user_meta($user->ID, 'google_avatar_compare', true);
			// Upload the avatar from the URL
			if ($avatar_url && $stored_avatar_url !== $avatar_url) {
				$avatar_id = $this->upload_avatar_from_url($avatar_url, 'google_avatar_' . $user->ID);
				if ($avatar_id) {
					update_user_meta($user->ID, 'google_avatar', $avatar_id);
					update_user_meta($user->ID, 'google_avatar_compare', $avatar_url);
				}
			}
			wp_set_current_user($user->ID);
			wp_set_auth_cookie($user->ID);

			wp_send_json_success(['redirect_url' => esc_url($this->redirect_url)]);
		} else {
			wp_send_json_error(['message' => __('Google authentication failed.', 'google-login')]);
		}
	}

	public function upload_avatar_from_url($url, $filename = '', $title = null)
	{
		// require_once(ABSPATH . "/wp-load.php");
		// require_once(ABSPATH . "/wp-admin/includes/image.php");
		require_once(ABSPATH . "/wp-admin/includes/file.php");
		require_once(ABSPATH . "/wp-admin/includes/media.php");

		$tmp = download_url(esc_url_raw($url));
		// Check if the download was successful
		if (is_wp_error($tmp)) {
			error_log('Failed to download image: ' . $tmp->get_error_message());
			return false; // Early exit if download fails
		}

		$filename = sanitize_title($filename ?: pathinfo($url, PATHINFO_FILENAME));
		// $extension = pathinfo($url, PATHINFO_EXTENSION);
		$args = ['name' => "$filename.jpg", 'tmp_name' => $tmp];

		$attachment_id = media_handle_sideload($args, 0, $title);
		if (is_wp_error($attachment_id)) {
			error_log('Failed to upload image: ' . $attachment_id->get_error_message());
			return false; // Return false if upload fails
		}
		@unlink($tmp);
		return is_wp_error($attachment_id) ? false : $attachment_id;
	}

	public function verify_google_token($token)
	{
		$url = 'https://oauth2.googleapis.com/tokeninfo?id_token=' . sanitize_text_field($token);
		$response = wp_remote_get($url);
		if (is_wp_error($response)) {
			return false;
		}
		return json_decode(wp_remote_retrieve_body($response), true);
	}
}
