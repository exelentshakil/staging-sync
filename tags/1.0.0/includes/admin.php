<?php

namespace SSync;

class SSync_Admin
{

    private $menu_name;
    private $plugin_url;
    private $settingsNames;
    private $text;


    function __construct($param)
    {

        $this->settingsNames = [
            'ssync_secret',
            'ssync_mode',
            'ssync_hours',
            'ssync_user_email',
            'ssync_staging_email',
            'ssync_staging_url',
            'ssync_live_url',
        ];
        $this->plugin_url = $param['plugin_url'];

        add_action('admin_menu', array($this, 'add_menu_pg'));
        add_filter('wp_get_attachment_url', array($this, 'fetch_from_live'), PHP_INT_MAX);
        add_action('admin_notices', array($this, 'pseudo_cronjob'));
    }


    function fetch_from_live($url)
    {
        if (get_option('ssync_mode', 'live') == 'live') {
            return;
        }
        $home = get_option('siteurl');
        $url_live = get_option('ssync_live_url', '');
        return str_replace(\trailingslashit($home), \trailingslashit($url_live), $url);
    }

    function pseudo_cronjob()
    {
        if (get_option('ssync_mode', 'live') == 'live') {
            return;
        }
        $url_live = get_option('ssync_live_url', '');
        $hours = get_option('ssync_hours', 12);
        $last_time = get_option('ssync_last_time', time());
        $time = '12 hours';
        switch ($hours) {
            case '6':
                $time = '6 hours';
                break;
            case '12':
                $time = '12 hours';
                break;
            case '24':
                $time = '1 day';
                break;

            default:
                $time = '1 week';
                # code...
                break;
        }
        //if(true){
        if (time() - $last_time >= 60 && trim($url_live) != '') {
            ?>

            <div class="notice notice-info is-dismissible">
                <p><?php _e('It\'s been ' . $time . ' or maybe some more time since you synced your content from the live site. <a class="button button-primary" id="syncTrigger">Sync now?</a>', 'ssync'); ?></p>
            </div>
            <script>
                jQuery(function () {
                    jQuery('#syncTrigger').click(function () {
                        jQuery(this).attr('disabled', 'disabled').text('Fetching information...');
                        jQuery.ajax({
                            url: '<?php echo esc_url($url_live); ?>wp-admin/admin-ajax.php',
                            type: 'POST',
                            data: {
                                action: 'startsync',
                                private_key: '<?php echo sanitize_text_field(get_option('ssync_secret', '')); ?>'
                            },
                            success: function (data) {
                                var form = jQuery('<form action="<?php echo esc_attr(filter_input(INPUT_SERVER, 'REQUEST_URI')); ?>" method="post" id="syncform">\
			<?php wp_nonce_field('ssync_nonce', 'ssync_nonce'); ?>\
			<input type="hidden" name="ssync_stream" value="parameters" id="parameters">\
		</form>').prependTo('body');
                                jQuery('#parameters').val(data);
                                setTimeout(function () {
                                    jQuery('#syncform').submit();
                                }, 100);
                            },
                            error: function (e1, e2) {
                                console.log(e1, e2);
                                alert('There was an error requesting the information for synching. Details of error were logged in browser console.');
                                jQuery('this').removeAttr('disabled').text('Sync Now?');

                            }
                        });

                    });
                });
            </script>
            <?php
        }
    }


    public function add_menu_pg()
    {
        $main_page = add_menu_page('Staging Sync Settings', 'Staging Sync Settings', 'manage_options', 'ssync_settings', [$this, 'settings']);
    }

    public function settings()
    {
        $msg = '';
        if (isset($_POST['login']) && wp_verify_nonce($_POST['tss_login'], 'tss_login')) {

            if (isset($_POST['login'])) {
                $user = sanitize_email($_POST['login']['user']);
                $password = sanitize_text_field($_POST['login']['password']);
                $this->login($user, $password);
                wp_redirect(admin_url('?page=ssync_settings'));
            }
        }

        if (isset($_POST['signup']) && wp_verify_nonce($_POST['tss_signup'], 'tss_signup')) {
            if (isset($_POST['signup'])) {
                $user = sanitize_email($_POST['signup']['email']);
                $password = sanitize_text_field($_POST['signup']['password']);
                $this->signup($user, $password);
                wp_redirect(admin_url('?page=ssync_settings'));
            }
        }

        if (isset($_POST['tss_options']) && wp_verify_nonce($_POST['tss_options'], 'tss_options')) {


            foreach ($this->settingsNames as $key) {
                if (isset($_POST[$key]))
                    update_option($key, $key == 'ssync_staging_url' || $key == 'ssync_live_url' ?
                        rtrim($_POST[$key], '/') . '/'
                        : sanitize_text_field($_POST[$key])
                    );
                else {
                    $kk = 0;
                }
            }


            if (isset($_POST['ssync_mode']) && $_POST['ssync_mode'] == 'live') {
                if (isset($_POST['ssync_Send'])) {
                    $this->send_notification_email();
                    $msg = '<div class="notice notice-success">
							<p>' . __('An email has been sent to the admin address. Once owner of staging sets up staging for synching, the plugin will start to work!', 'tss') . '</p>
						</div>';
                }

            }

            if (isset($_POST['ssync_mode']) && $_POST['ssync_mode'] == 'staging') {
                if (!get_option('ssync_last_time', false)) {
                    update_option('ssync_last_time', time());
                    $msg = '<div class="notice notice-success">
							<p>' . __('Congratulations! Now both your staging site and your live site are connected. From now on you will receive notifications every 12 hours to start the synchronizing.', 'tss') . '</p>
						</div>';
                } else {
                    $msg = '<div class="notice notice-success">
							<p>' . __('Congratulations! Changes were saved successfully. ', 'tss') . '</p>
						</div>';
                }
            }
        } // of if options

        $current_user = get_option('ssync_user_email', false);
        if (!$current_user || trim($current_user) == ''):
            require_once 'not_loggedin.php';
        else:
            require_once 'settings_page.php';
        endif;
    }

    function send_notification_email()
    {

        $admin_email = get_option('ssync_staging_email', '');
        $staging_url = get_option('ssync_staging_url', '');

        $login_url = 'https://google.com';

        $subject = sprintf(__('Request for Synchronizing your site with the contents of %1$s'),
            get_bloginfo('name'));

        $content = "<p>Hello!</p>
         <p>If you are receiving this email message, that means that someone installed Two Sites Sync plugin on their WordPress site, and they want to start synchronizing their contents in your site database.</p><p>If you agree with this procedure, please follow these steps:</p>
         <ol>
         <li>Sign in to your staging site ( " . $staging_url . " )</li>
         <li>If you have not installed/activated yet Two Sites Sync plugin in your staging site, please do.</li>
         <li>Go to the Two Sites Sync settings link in your dashboard menu.</li>
         <li>Please sign in/sign up in Two Sites settings login dialog. If you do not have any Two Sites account, you can create one by following this link: <a href=\"" . $login_url . "\">" . $login_url . "</a></li>
         <li>In the settings page, please input the following information:</li>
         <ol>
         	<li>In the Private Key field, you must enter the private key that was set in the live site. This is a security requirement. If you do not know it, please contact the owner of live site.</li>
         	<li>In the Mode field, please check the option Staging.</li>
         	<li>In the Live Site URL option, please enter the following URL: " . home_url() . "</li>
         </ol>
         <li>Once you are done with the settings in your staging site, please click in the button Save Changes.</li>
         </ol>
         <p>If you do not have any idea about what this message means, please just ignore this email.</p>";

        $headers = 'Content-Type: text/html ' . "\r\n";

        wp_mail($admin_email, $subject, $content, $headers);
        return;
    }

    public function login($user, $password)
    {
        update_option('ssync_user_email', $user);
    }

    public function signup($user, $password)
    {
        update_option('ssync_user_email', $user);
    }


} 