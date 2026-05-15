<?php

/**
 * Plugin Name: Login with Google
 * Description: Plugin tích hợp cổng đăng nhập Google
 * Version:     1.0.0
 * Author:      Huy Vo
 * Text Domain: google-login
 */
if (!defined('ABSPATH')) {
	exit();
}
define('GOOGLE_LOGIN_DIR', plugin_dir_path(__FILE__));
define('GOOGLE_LOGIN_URL', plugins_url('/', __FILE__));


/**
 * Start the instance
 */

new Google_Login();

class Google_Login
{
	private $shortcodes = array();
	/**
	 * @var array The default settings for the whole plugin
	 */
	static $default_settings = array(

		'client_id' => '',
		'client_secret' => '',
		'login_redirect' => '',
		'show_google_button' => 'no',
		'class' => '',
		'id' => '',
		'redirect' => '',
	);

	protected $admin_page;
	protected $method;

	/**
	 * Setup class.
	 *
	 * @since 1.0
	 */
	public function __construct()
	{
		add_action('init', array($this, 'init'));
	}



	/**
	 * Run this method under the "init" action
	 */
	public function init()
	{


		// Run this plugin normally if WooCommerce is active
		$this->main();

		add_action('admin_enqueue_scripts', [$this, 'admin_assets']);

		// Add "Settings" link when the plugin is active
		add_filter('plugin_action_links_' . plugin_basename(__FILE__), array($this, 'add_settings_link'));
	}

	public function loadModule()
	{
		include(GOOGLE_LOGIN_DIR . 'public/shortcode/class-google-login-btn.php');
		$this->shortcodes[] = new google_login\Shortcodes\Google_Btn;
		include(GOOGLE_LOGIN_DIR . 'public/shortcode/class-google-login-google-btn.php');
		$this->shortcodes[] = new google_login\Shortcodes\Admin_Google_Btn;
	}
	/**
	 * The main method to load the components
	 */
	public function main()
	{

		if (is_admin()) {
			// Add the admin setting page
			include(GOOGLE_LOGIN_DIR . 'includes/admin/class-google-login-admin-page.php');
			$this->admin_page = new Google_Login_Admin_Page();
		}

		$settings = self::get_settings();

		// Check if "Add the OnePay Domestic Gateway" is enabled
		if (
			!empty($settings['client_id'])
			// and !empty($settings['client_secret'])
		) {
			$this->loadModule();
			$redirect_url =  (!empty($settings['redirect_url'])) ? $settings['redirect_url'] : get_site_url();
			include('includes/class-google-login-method.php');
			$this->method = (new Google_Login_Method())->__call_action($settings['client_id'], $settings['client_secret'], $redirect_url, $settings['show_google_button']);
		}
	}

	/**
	 * The wrapper method to get the settings of the plugin
	 * @return array
	 */
	static function get_settings()
	{
		$settings = get_option('google-login', self::$default_settings);
		$settings = wp_parse_args($settings, self::$default_settings);

		return $settings;
	}


	/**
	 * Add "Settings" link in the Plugins list page when the plugin is active
	 */
	public function add_settings_link($links)
	{
		$settings = array('<a href="' . admin_url('admin.php?page=google-login') . '">' . __('Settings', 'google-login') . '</a>');
		$links    = array_reverse(array_merge($links, $settings));

		return $links;
	}
	public function admin_assets()
	{
		wp_enqueue_style('google-login-admin-css', GOOGLE_LOGIN_URL . 'assets/css/admin.css', [], false, 'all');
	}
}
