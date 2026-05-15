<?php

namespace google_login\ShortCodes;

class Google_Btn
{
    private $google_login_instance;
    private $class;
    private $id;
    public function __construct()
    {
        $settings = \Google_Login::get_settings();
        $this->google_login_instance = $settings['client_id'];
        $this->id = $settings['id'];
        $this->class = $settings['class'];
        add_shortcode('google_login', array($this, 'callback'));
    }
    public function callback($atts)
    {

?>
        <!-- <div class="google-login-btn <?php //echo $this->class 
                                            ?>" id="<?php //echo $this->id 
                                                    ?>">
            <div id="g_id_onload"
                data-client_id="<?php //echo $this->google_login_instance 
                                ?>"
                data-callback="handleCredentialResponse">
            </div>
            <div class="g_id_signin" data-type="standard"></div>
        </div> -->
        <div class="google-login <?php echo $this->class
                                ?>" id="<?php echo $this->id
                                        ?>">
            <div class="google-login-btn">
                <span class="icon">
                    <img src="<?php echo GOOGLE_LOGIN_URL ?>assets/images/logo.png" loading="lazy" alt="" />
                </span>
                <span class="text"><?php echo __('Đăng nhập bằng Google', 'google-login'); ?></span>

            </div>
            <div class="google-login-response"></div>

        </div>
<?php
    }
}
