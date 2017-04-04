<?php
require_once("wp-load.php");
require_once( ABSPATH . WPINC . '/class-phpmailer.php' );
$mail = new PHPMailer();
$charset = get_bloginfo('charset');
$mail->CharSet = $charset;
$swpsmtp_options = get_option('swpsmtp_options');

$from_name = $swpsmtp_options['from_name_field'];
$from_email = $swpsmtp_options['from_email_field'];
$mail->IsSMTP();

/* If using smtp auth, set the username & password */

if ('yes' == $swpsmtp_options['smtp_settings']['autentication']) {

	$mail->SMTPAuth = true;

	$mail->Username = $swpsmtp_options['smtp_settings']['username'];

	$mail->Password = swpsmtp_get_password();

}

/* Set the SMTPSecure value, if set to none, leave this blank */

if ($swpsmtp_options['smtp_settings']['type_encryption'] !== 'none') {

	$mail->SMTPSecure = $swpsmtp_options['smtp_settings']['type_encryption'];

}

$subject = "Brst test email";
$logo = get_stylesheet_directory_uri().'/images/logo.png';
$site_url= get_site_url();
$fb = get_stylesheet_directory_uri().'/images/newfb.png';
$ping = get_stylesheet_directory_uri().'/images/newping.png';
$message = "<div style='float:left;width:100%;'>;

<img style='text-align:center; padding-bottom: 10px;' src='".$logo."'></br>
<div class='top' style='padding-top: 15px; border-top: 5px solid #4abdac;float: left; width: 100%;'>
	<table class='body-wrap'>
		<tr>
			<td><h3 style='color:black;'>Hi, 9637474474</h3>
				<p class='lead' style='color:black;'>Phasellus dictum sapien a neque luctus cursus. Pellentesque sem dolor, fringilla et pharetra vitae.</p>
				<p style='color:black;'>Phasellus dictum sapien a neque luctus cursus. Pellentesque sem dolor, fringilla et pharetra vitae. consequat vel lacus. Sed iaculis pulvinar ligula, ornare fringilla ante viverra et. In hac habitasse platea dictumst. Donec vel orci mi, eu congue justo. Integer eget odio est, eget malesuada lorem. Aenean sed tellus dui, vitae viverra risus. Nullam massa sapien, pulvinar eleifend fringilla id, convallis eget nisi. Mauris a sagittis dui. Pellentesque non lacinia mi. Fusce sit amet libero sit amet erat venenatis sollicitudin vitae vel eros. Cras nunc sapien, interdum sit amet porttitor ut, congue quis urna.</p>
			<div style='text-align:center;margin-bottom: 15px; margin-top: 30px;'><a style=' text-decoration: none; text-transform: uppercase; background: #f7b733 none repeat scroll 0 0; border-radius: 5px;color: white;float: none; font-family: sans-serif; font-size: 12px; padding: 10px 20px; text-align: center;' href='".$site_url."'>Go to Homepage</a>
			</div>
			
			
				<table style=' margin-top: 18px; background: #ebebeb none repeat scroll 0 0;float: left;width: 100%;'>
				<tr>
					<td>
					<table style='float: left;width: 50%;'>
							<tbody>
							<tr>
							<td style='padding: 15px;'>
							<h5 style='margin-bottom: 0; font-size: 17px; font-weight: 900; color: black;'>Connect with Us:</h5>
							<p>
							<img style='text-align:center; padding-bottom: 10px; height: 30px;' src='".$fb."'>
							<img style='text-align:center; padding-bottom: 10px; height: 30px;' src='".$ping."'></br>
							
							</p>
							</td>
							</tr>
							</tbody>
					</table>
					<table style='float: left;width: 50%;'>
						<tbody>
						<tr>
						<td style='padding: 15px;'>
						<h5 style='margin-bottom: 0; font-size: 17px; font-weight: 900; color: black;'>Contact Info:</h5>
						<p style='color:black;'>
						Phone:
						<strong>408.341.0600</strong>
						<br>
						Email:
						<strong>
						<a style='color:#2ba6cb;' href='emailto:hseldon@trantor.com'>hseldon@trantor.com</a>
						</strong>
						</p>
						</td>
						</tr>
						</tbody>
						</table>
					</td>
				</tr>
				</table>
				 <table style='width: 100%; padding-bottom: 15px; padding-top: 15px;'>
						<tbody>
						<tr>
						<td align='center'>
						<p>
						<a style='color:#2ba6cb;' href='#'>Terms</a>
						|
						<a style='color:#2ba6cb;'  href='#'>Privacy</a>
						|
						<a style='color:#2ba6cb;' href='#'>Unsubscribe</a>
						</p>
						</td>
						</tr>
						</tbody>
				</table>
				</td>
			
	  </tr>
	
	</table>

</div>
</div>";
$to_email = "brstdev12@gmail.com";

/* PHPMailer 5.2.10 introduced this option. However, this might cause issues if the server is advertising TLS with an invalid certificate. */
$mail->SMTPAutoTLS = false;

/* Set the other options */

$mail->Host = $swpsmtp_options['smtp_settings']['host'];

$mail->Port = $swpsmtp_options['smtp_settings']['port'];

$mail->SetFrom($from_email, $from_name);

$mail->isHTML(true);

$mail->Subject = $subject;

$mail->MsgHTML($message);

$mail->AddAddress($to_email);

$mail->SMTPDebug = 0;



/* Send mail and return result */

if (!$mail->Send())
	$errors = $mail->ErrorInfo;


$mail->ClearAddresses();
$mail->ClearAllRecipients();


if (!empty($errors)){
	return $errors;
} else{
	return 'Email was sent';
}