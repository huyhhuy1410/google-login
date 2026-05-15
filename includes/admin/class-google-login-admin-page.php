<?php
if (! defined('ABSPATH')) {
	exit;
}
require_once GOOGLE_LOGIN_DIR . 'includes/notice/class-notice.php';
/**
 * Create the admin page under wp-admin -> WooCommerce -> Woo Viet
 */
class Google_Login_Admin_Page
{
	/**
	 * @var string The message to display after saving settings
	 */
	public $message = '';
	private $notices;
	/**
	 * Google_Login_Admin_Page constructor.
	 */
	public function __construct()
	{
		$this->notices = new google_login\Notice();
		// Catch and run the save_settings() action
		if (isset($_REQUEST['google_login_nonce']) && isset($_REQUEST['action']) && 'google_login_save_settings' == $_REQUEST['action']) {
			$this->save_settings();
		}
		add_action('admin_menu', array($this, 'register_submenu_page'));
	}
	/**
	 * Save settings for the plugin
	 */
	public function save_settings()
	{
		$settings = $_REQUEST['settings'];
		if (wp_verify_nonce($_REQUEST['google_login_nonce'], 'google_login_save_settings')) {
			update_option('google-login', $this->sanitize_settings($settings));
			$this->notices->add_notice(__('Cài đặt đã được lưu.', 'google-login'), 'updated');
		} else {
			$this->notices->add_notice(__('Không thể lưu! Vui lòng thử tải lại trang.', 'google-login'), 'error');
		}
		// Example validation checks
		if (empty($settings['client_id'])) {
			$this->notices->add_notice(__('Client ID chưa hợp lệ.', 'google-login'), 'error');
		}
		// if (empty($settings['client_secret'])) {
		// 	$this->notices->add_notice(__('Client Secret chưa hợp lệ.', 'google-login'), 'error');
		// }
	}
	/**
	 * Register the sub-menu under "WooCommerce"
	 * Link: http://my-site.com/wp-admin/admin.php?page=google-login
	 */
	public function register_submenu_page()
	{
		add_submenu_page(
			'options-general.php',          // Parent slug
			__('Đăng nhập bằng Google', 'google-login'),
			__('Đăng nhập bằng Google', 'google-login'),
			'manage_options',
			'google-login',
			array($this, 'admin_page_html'),
		);
	}
	/**
	 * Generate the HTML code of the settings page
	 */
	public function admin_page_html()
	{
		// check user capabilities
		if (! current_user_can('manage_options')) {
			return;
		}
		$settings = Google_Login::get_settings();
		// Tab navigation
		$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'general';
?>
		<div class="wrap">
			<h1><?php echo esc_html(get_admin_page_title()); ?></h1>
			<?php $this->notices->display_notices(); ?>
			<h2 class="nav-tab-wrapper">
				<a href="?page=google-login&tab=guide" class="nav-tab <?php echo $active_tab === 'guide' ? 'nav-tab-active' : ''; ?>">
					<?php echo __('Hướng dẫn', 'google-login'); ?>
				</a>
				<a href="?page=google-login&tab=general" class="nav-tab <?php echo $active_tab === 'general' ? 'nav-tab-active' : ''; ?>">
					<?php echo __('Cài đặt', 'google-login'); ?>
				</a>
				<a href="?page=google-login&tab=usage" class="nav-tab <?php echo $active_tab === 'usage' ? 'nav-tab-active' : ''; ?>">
					<?php echo __('Sử dụng', 'google-login'); ?>
				</a>
			</h2>
			<?php
			// Display content based on the selected tab
			if ($active_tab === 'general') {
				$this->render_general_settings($settings);
			} elseif ($active_tab === 'guide') {
				$this->render_guide();
			} elseif ($active_tab === 'usage') {
				$this->render_usage($settings);
			}
			?>
		</div>
	<?php
	}
	private function render_general_settings($settings)
	{
	?>
		<div class="gg-container" style="max-width: 65em;">
			<div id="google-login-header"
				style="padding: 5px;">
				<p><strong>
						<?php
						printf(
							__('Vui lòng xem hướng dẫn <a href="%1$s" target="_blank">tại đây</a> và nhập đầy đủ thông tin để phương thức đăng nhập hoạt động.', 'mone-admin'),
							'?page=google-login&tab=guide',
						)
						?>
					</strong></p>
			</div>
			<form method="post">
				<?php echo $this->message; ?>
				<input type="hidden" id="action" name="action" value="google_login_save_settings">
				<input type="hidden" id="google_login_nonce" name="google_login_nonce" value="<?php echo wp_create_nonce('google_login_save_settings'); ?>">
				<table class="form-table">
					<tbody>
						<tr>
							<th scope="row"><?php _e('Client ID - <em>(Bắt buộc)</em>', 'google-login'); ?></th>
							<td>
								<input name="settings[client_id]" type="text" value="<?php echo esc_attr($settings['client_id']); ?>" class="regular-text">
							</td>
						</tr>
						<!-- <tr>
							<th scope="row"><?php _e('Client Secret - <em>(Bắt buộc)</em>', 'google-login'); ?></th>
							<td>
								<input name="settings[client_secret]" type="text" value="<?php //echo esc_attr($settings['client_secret']); 
																							?>" class="regular-text">
							</td>
						</tr> -->
						<tr>
							<th scope="row"><?php _e('Redirect URL', 'google-login'); ?></th>
							<td>
								<input name="settings[login_redirect]" type="text" value="<?php echo esc_attr($settings['login_redirect']); ?>" class="regular-text">
								<p><?php echo __('Nhập URL muốn chuyển hướng sau khi đăng nhập. <br/>Mặc định:<strong> ' . get_site_url() . '</strong>', 'google-login'); ?></p>
							</td>
						</tr>
						<tr>
							<th scope="row"><?php _e('Đăng nhập Admin', 'google-login'); ?></th>
							<td>
								<input name="settings[show_google_button]" type="checkbox" value="yes" <?php checked('yes', $settings['show_google_button']); ?> />
								<p><?php echo __('Hiển thị nút đăng nhập bằng Google ở trang đăng nhập Admin', 'google-login'); ?></p>
							</td>
						</tr>
					</tbody>
				</table>
				<input name="settings[id]" type="hidden" value="<?php echo esc_attr($settings['id']); ?>" class="regular-text">
				<input name="settings[class]" type="hidden" value="<?php echo esc_attr($settings['class']); ?>" class="regular-text">

				<p class="submit">
					<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php echo __('Lưu thay đổi', 'google-login'); ?>">
				</p>
			</form>
		</div>
	<?php
	}
	private function render_usage($settings)
	{
	?>
		<div class="gg-container" style="max-width: 65em;">
			<div id="google-login-header"
				style="padding: 5px;">
				<h2><?php echo __('Shortcode nút đăng nhập Google', 'google-login'); ?><h4>

			</div>
			&nbsp;
			<textarea readonly>[google_login]</textarea>
			<p><strong>
					<?php
					echo  __('Shortcode chỉ hiển thị khi người dùng chưa đăng nhập.', 'google-login')
					?>
				</strong></p>
			<form method="post">
				<?php echo $this->message; ?>
				<input type="hidden" id="action" name="action" value="google_login_save_settings">
				<input type="hidden" id="google_login_nonce" name="google_login_nonce" value="<?php echo wp_create_nonce('google_login_save_settings'); ?>">
				<table class="form-table">
					<tbody>
						<tr>
							<th scope="row"><?php _e('Tuỳ chỉnh Class', 'google-login'); ?></th>
							<td>
								<input name="settings[class]" type="text" value="<?php echo esc_attr($settings['class']); ?>" class="regular-text">
								<p><?php echo __('Thêm class cho shortcode nút đăng nhập<br/>
								<strong>Ví dụ:</strong>', 'google-login'); ?></p><textarea readonly>class-1 class-2 class-3</textarea>
							</td>
						</tr>
						<tr>
							<th scope="row"><?php _e('Tuỳ chỉnh ID', 'google-login'); ?></th>
							<td>
								<input name="settings[id]" type="text" value="<?php echo esc_attr($settings['id']); ?>" class="regular-text">
								<p><?php echo __('Thêm ID cho shortcode nút đăng nhập', 'google-login'); ?></p>
							</td>
						</tr>
						<!-- <tr>
							<th scope="row"><?php //_e('Redirect', 'google-login'); 
											?></th>
							<td>
								<input name="settings[redirect]" type="text" value="<?php //echo esc_attr($settings['redirect']); 
																					?>" class="regular-text">
							</td>
						</tr> -->
					</tbody>
				</table>
				<input name="settings[client_id]" type="hidden" value="<?php echo esc_attr($settings['client_id']); ?>" class="regular-text">
				<input name="settings[login_redirect]" type="hidden" value="<?php echo esc_attr($settings['login_redirect']); ?>" class="regular-text">
				<input name="settings[show_google_button]" type="hidden" value="yes" <?php checked('yes', $settings['show_google_button']); ?> />

				<p class="submit">
					<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php echo __('Lưu thay đổi', 'google-login'); ?>">
				</p>
			</form>
		</div>
	<?php
	}
	// Function to render guide
	private function render_guide()
	{
	?>
		<div class="gg-container" style="max-width: 65em;">
			<h2><?php _e('Hướng dẫn sử dụng', 'google-login'); ?></h2>
			<p>
				<?php _e('Để cho phép người truy cập đăng nhập bằng tài khoản Google của họ, trước tiên bạn phải tạo Ứng dụng Google. Sau đó chuyển đến <strong>"Cài đặt"</strong> và cấu hình <strong>"Client ID"</strong> theo hướng dẫn.', 'google-login'); ?>
			</p>
			
			<h2><?php _e('Tạo Ứng Dụng Google', 'google-login'); ?></h2>
			<ol>
				<li><?php _e('Đi tới <a href="https://console.developers.google.com/apis/">Google Developers Console</a>.', 'google-login'); ?></li>
				<li><?php _e('Đăng nhập bằng thông tin tài khoản Google của bạn.', 'google-login'); ?></li>
				<li><?php _e('Nếu bạn chưa có dự án, bạn sẽ cần tạo một cái mới. Nhấp vào <strong>"Create Project"</strong> ở bên phải! (Nếu bạn đã có dự án, hãy nhấp vào tên dự án của bạn ở thanh trên cùng).', 'google-login'); ?></li>
				<li><?php _e('Đặt tên cho dự án và nhấn nút <strong>"Create"</strong>!', 'google-login'); ?></li>
				<li><?php _e('Khi đã có dự án, bạn sẽ thấy bảng điều khiển. (Để ý thanh trên cùng xem có đúng tên dự án đã tạo không)', 'google-login'); ?></li>
				<li><?php _e('Nhấp vào nút <strong>“OAuth consent screen”</strong> ở sidebar bên trái.', 'google-login'); ?></li>
				<li><?php _e('Chọn User Type phù hợp với nhu cầu và nhấn <strong>"Create"</strong>. Nếu muốn kích hoạt đăng nhập bằng Google cho bất kỳ người dùng nào có tài khoản Google, chọn tùy chọn <strong>"External"</strong>!', 'google-login'); ?></li>
				<li><?php _e('Nhập tên cho Ứng dụng vào trường <strong>"App name"</strong>, tên này sẽ xuất hiện khi ứng dụng yêu cầu sự đồng ý từ người dùng.', 'google-login'); ?></li>
				<li><?php _e('Ở trường <strong>"User support email"</strong>, chọn địa chỉ email mà người dùng có thể liên hệ với bạn.', 'google-login'); ?></li>
				<li><?php _e('Điền trường <strong>"Application home page"</strong> bằng URL trang chính của bạn, có thể là:<br/> <strong>' . get_site_url() . '</strong>', 'google-login'); ?></li>
				<li><?php _e('Nhập liên kết chính sách quyền riêng tư và điều khoản dịch vụ vào các trường <strong>"Application privacy policy link"</strong> và <strong>"Application terms of service link"</strong>.', 'google-login'); ?></li>
				<li><?php _e('Trong phần <strong>"Authorized domains"</strong>, nhấn nút <strong>"Add Domain"</strong> và nhập tên miền của bạn, có thể là: <strong>' . $this->get_domain_url(get_site_url()) . '</strong> (không bao gồm tên miền phụ)!', 'google-login'); ?></li>
				<li><?php _e('Ở phần <strong>"Developer contact information"</strong>, nhập địa chỉ email mà Google có thể sử dụng để thông báo cho bạn về dự án.', 'google-login'); ?></li>
				<li><?php _e('Nhấn <strong>"Save and Continue"</strong>, sau đó nhấn tiếp tục ở các trang <strong>"Scopes", "Test users"</strong>!', 'google-login'); ?></li>
				<li><?php _e('Ở bên trái, nhấp vào mục <strong>"Credentials"</strong>, sau đó nhấn nút <strong>"+ Create Credentials"</strong> ở thanh trên cùng.', 'google-login'); ?></li>
				<li><?php _e('Chọn tùy chọn <strong>"OAuth client ID"</strong>.', 'google-login'); ?></li>
				<li><?php _e('Chọn <strong>"Web application"</strong> dưới loại ứng dụng.', 'google-login'); ?></li>
				<li><?php _e('Nhập <strong>"Name"</strong> cho OAuth client ID của bạn.', 'google-login'); ?></li>
				<li><?php _e('Trong phần <strong>"Authorized JavaScript origins"</strong> và <strong>"Authorized redirect URIs"</strong>, nhấp <strong>"Add URI"</strong> và thêm URL sau:<br/> <strong>' . get_site_url() . '</strong>', 'google-login'); ?></li>
				<li><?php _e('Nhấn nút <strong>"Create"</strong>.', 'google-login'); ?></li>
				<li><?php _e('Một cửa sổ pop-up sẽ xuất hiện với thông tin xác thực. Nếu không, nhấp vào <strong>"Credentials"</strong> trong sidebar bên trái và chọn tên ứng dụng của bạn ở danh sách bên phải. Sau đó sao chép <strong>"Client ID"</strong> từ đó.', 'google-login'); ?></li>
				<li><?php _e('Ứng dụng sẽ ở trạng thái Test. Để mở công khai, nhấp vào tùy chọn <strong>"OAuth consent screen"</strong> ở bên trái, sau đó nhấp vào nút <strong>"PUBLISH APP"</strong> dưới phần <strong>"Publishing status"</strong> và nhấn nút <strong>"Confirm"</strong>.', 'google-login'); ?></li>
			</ol>
		</div>
<?php
	}
	/**
	 * Sanitize admin settings.
	 *
	 * @param  array  $settings User input settings.
	 *
	 * @return array
	 */
	private function sanitize_settings(array $settings): array
	{
		$sanitized_settings = array();
		// error_log(print_r($settings));
		foreach ($settings as $feature => $value) {
			// foreach ($feature_options as $option => $value) {
			$sanitized_settings[$feature] = esc_html(sanitize_text_field($value));
			// }
		}
		return $sanitized_settings;
	}
	private function get_domain_url($url)
	{
		$parsed_url = parse_url($url);
		$host = preg_replace('/^www\./', '', $parsed_url['host']);
		return $host;
	}
}
?>