<?php
$to = "brstdev9@gmail.com";
$subject = "Test mail";
$message = "Hello! This is a simple email message.";
$from = "info@webnhathuoc.vn";
$headers = "From:" . $from;
$email = mail($to,$subject,$message,$headers);
if($email){
	echo "Mail Sent.";
}else{
	echo "Not Sent";
}
?> 
