<?php
/*
  Plugin Name:Custom  Frontend  Login Registration Form
  Plugin URI: http://dropsofvisions.com/
  Description: Simple Frontend Login  Registration Form This plugin creates a user interface for adding custom registration forms to any post or page on your WordPress website. Simply navigate to the edit page for  post .login form Shortcode: [cflrf_custom_login_form] >Registration Form Shortcode: [cflrf_registration_form]
  

  Version: 1.01
  Author: Anil Shah
  Author URI: http://dropsofvisions.com/
 */
 class cflrf_registration_form
{
 
    // form properties
    private $username;
    private $email;
    private $password;
    private $website;
    private $first_name;
    private $last_name;
    private $nickname;
    private $bio;

	function __construct()
{
    add_shortcode('cflrf_registration_form', array($this, 'shortcode'));	
    add_action('wp_enqueue_scripts', array($this, 'cflrf_flat_ui_kit'));
}
public function registration_form()
    {
 
        ?>
        
 
        <form method="post" action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>">
            <div class="login-form">
                <div class="form-group">
                    <input name="reg_name" type="text" class="form-control login-field"
                           value="<?php echo(isset($_REQUEST['reg_name']) ? $_REQUEST['reg_name'] : null); ?>"
                           placeholder="Username" id="reg-name">
                    <label class="login-field-icon fui-user" for="reg-name"></label>
                </div>
 
                <div class="form-group">
                    <input name="reg_email" type="email" class="form-control login-field"
                           value="<?php echo(isset($_REQUEST['reg_email']) ? $_REQUEST['reg_email'] : null); ?>"
                           placeholder="Email" id="reg-email">
                    <label class="login-field-icon fui-mail" for="reg-email"></label>
                </div>
 
                <div class="form-group">
                    <input name="reg_password" type="password" class="form-control login-field"
                           value="<?php echo(isset($_REQUEST['reg_password']) ? $_REQUEST['reg_password'] : null); ?>"
                           placeholder="Password" id="reg-pass">
                    <label class="login-field-icon fui-lock" for="reg-pass"></label>
                </div>
 
                <div class="form-group">
                    <input name="reg_website" type="text" class="form-control login-field"
                           value="<?php echo(isset($_REQUEST['reg_website']) ? $_REQUEST['reg_website'] : null); ?>"
                           placeholder="Website(optional)" id="reg-website"/>
                    <label class="login-field-icon fui-chat" for="reg-website"></label>
                </div>
 
                <div class="form-group">
                    <input name="reg_fname" type="text" class="form-control login-field"
                           value="<?php echo(isset($_REQUEST['reg_fname']) ? $_REQUEST['reg_fname'] : null); ?>"
                           placeholder="First Name" id="reg-fname"/>
                    <label class="login-field-icon fui-user" for="reg-fname"></label>
                </div>
 
                <div class="form-group">
                    <input name="reg_lname" type="text" class="form-control login-field"
                           value="<?php echo(isset($_REQUEST['reg_lname']) ? $_REQUEST['reg_lname'] : null); ?>"
                           placeholder="Last Name" id="reg-lname"/>
                    <label class="login-field-icon fui-user" for="reg-lname"></label>
                </div>
 
                <div class="form-group">
                    <input name="reg_nickname" type="text" class="form-control login-field"
                           value="<?php echo(isset($_REQUEST['reg_nickname']) ? $_REQUEST['reg_nickname'] : null); ?>"
                           placeholder="Nickname" id="reg-nickname"/>
                    <label class="login-field-icon fui-user" for="reg-nickname"></label>
                </div>
 
                <div class="form-group">
                    <input name="reg_bio" type="text" class="form-control login-field"
                           value="<?php echo(isset($_REQUEST['reg_bio']) ? $_REQUEST['reg_bio'] : null); ?>"
                           placeholder="About / Bio" id="reg-bio"/>
                    <label class="login-field-icon fui-new" for="reg-bio"></label>
                </div>
 
                <input class="btn btn-primary btn-lg btn-block" type="submit" name="reg_submit" value="Register"/>
        </form>
        </div>
    <?php
    }
	function validation()
    {
 
        if (empty($this->username) || empty($this->password) || empty($this->email)) {
            return new WP_Error('field', 'Required form field is missing');
        }
 
        if (strlen($this->username) < 4) {
            return new WP_Error('username_length', 'Username too short. At least 4 characters is required');
        }
 
        if (strlen($this->password) < 5) {
            return new WP_Error('password', 'Password length must be greater than 5');
        }
 
        if (!is_email($this->email)) {
            return new WP_Error('email_invalid', 'Email is not valid');
        }
 
        if (email_exists($this->email)) {
            return new WP_Error('email', 'Email Already in use');
        }
 
        if (!empty($website)) {
            if (!filter_var($this->website, FILTER_VALIDATE_URL)) {
                return new WP_Error('website', 'Website is not a valid URL');
            }
        }
 
        $details = array('Username' => $this->username,
            'First Name' => $this->first_name,
            'Last Name' => $this->last_name,
            'Nickname' => $this->nickname,
            'bio' => $this->bio
        );
 
        foreach ($details as $field => $detail) {
            if (!validate_username($detail)) {
                return new WP_Error('name_invalid', 'Sorry, the "' . $field . '" you entered is not valid');
            }
        }
 
    }
	function registration()
{
 
    $userdata = array(
        'user_login' => esc_attr($this->username),
        'user_email' => esc_attr($this->email),
        'user_pass' => esc_attr($this->password),
        'user_url' => esc_attr($this->website),
        'first_name' => esc_attr($this->first_name),
        'last_name' => esc_attr($this->last_name),
        'nickname' => esc_attr($this->nickname),
        'description' => esc_attr($this->bio),
    );
 
    if (is_wp_error($this->validation())) {
        echo '<div style="margin-bottom: 6px" class="btn btn-block btn-lg btn-danger">';
        echo '<strong>' . $this->validation()->get_error_message() . '</strong>';
        echo '</div>';
    } else {
        $register_user = wp_insert_user($userdata);
        if (!is_wp_error($register_user)) {
 
            echo '<div style="margin-bottom: 6px" class="btn btn-block btn-lg btn-danger">';
            echo '<strong>Registration complete.</strong>';
            echo '</div>';
        } else {
            echo '<div style="margin-bottom: 6px" class="btn btn-block btn-lg btn-danger">';
            echo '<strong>' . $register_user->get_error_message() . '</strong>';
            echo '</div>';
        }
    }
 
}
function shortcode()
    {
 
        ob_start();
 
        if ($_REQUEST['reg_submit']) {
            $this->username = $_REQUEST['reg_name'];
            $this->email = $_REQUEST['reg_email'];
            $this->password = $_REQUEST['reg_password'];
            $this->website = $_REQUEST['reg_website'];
            $this->first_name = $_REQUEST['reg_fname'];
            $this->last_name = $_REQUEST['reg_lname'];
            $this->nickname = $_REQUEST['reg_nickname'];
            $this->bio = $_REQUEST['reg_bio'];
 
            $this->validation();
            $this->registration();
        }
 
        $this->registration_form();
        return ob_get_clean();
    }
	function cflrf_flat_ui_kit()
{
    wp_enqueue_style('bootstrap-css', plugins_url('bootstrap/css/bootstrap.css', __FILE__), false, false, 'screen');
    wp_enqueue_style('flat-ui-kit', plugins_url('css/flat-ui.css', __FILE__), false, false, 'screen');
 
}
}
new cflrf_registration_form;


// custom frontend login form

function cflrf_login_form() {
 
?>
 
<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
    <div class="login-form">
        <div class="form-group">
            <input name="login_name" type="text" class="form-control login-field" value="" placeholder="Username" id="login-name" />
            <label class="login-field-icon fui-user" for="login-name"></label>
        </div>
 
        <div class="form-group">
            <input  name="login_password" type="password" class="form-control login-field" value="" placeholder="Password" id="login-pass" />
            <label class="login-field-icon fui-lock" for="login-pass"></label>
        </div>
        <input class="btn btn-primary btn-lg btn-block" type="submit"  name="login_submit" value="Log in" />
</form>
</div>
<?php
}
function login_check( $username, $password ) {
global $user;
$creds = array();
$creds['user_login'] = $username;
$creds['user_password'] =  $password;
$creds['remember'] = true;
$user = wp_signon( $creds, false );
if ( is_wp_error($user) ) {
echo $user->get_error_message();
}
if ( !is_wp_error($user) ) {
wp_redirect(home_url('wp-admin'));
}
}
function login_check_process() {
if (isset($_POST['login_submit'])) {
    login_check($_POST['login_name'], $_POST['login_password']);
}
 
cflrf_login_form();
}
function cflrf_custom_ui_kit() {
wp_enqueue_style('bootstrap-css', plugins_url('bootstrap/css/bootstrap.css', __FILE__));
wp_enqueue_style('flat-ui-kit', plugins_url('css/flat-ui.css', __FILE__));
 
}
 
add_action('wp_enqueue_scripts', 'cflrf_custom_ui_kit');
function cflrf_login_shortcode() {
ob_start();
login_check_process();
return ob_get_clean();
}
 
add_shortcode('cflrf_custom_login_form', 'cflrf_login_shortcode');