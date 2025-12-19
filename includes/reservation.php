<?php

require_once('phpmailer/class.phpmailer.php');
require_once('phpmailer/class.smtp.php');

$mail = new PHPMailer();


//$mail->SMTPDebug = 3;                               // Enable verbose debug output
//$mail->isSMTP();                                      // Set mailer to use SMTP
$mail->Host = 'mail.cardiocare.co.tz';                  // Specify main and backup SMTP servers
$mail->SMTPAuth = true;                               // Enable SMTP authentication
$mail->Username = 'admin@cardiocare.co.tz';    // SMTP username
$mail->Password = '1MW0ziXtTYIE';                         // SMTP password
$mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
$mail->Port = 465;                                    // TCP port to connect to


$secretKey = "6LeqW7cqAAAAAIjzcBspGuFcbJImDRkO7501O5nr";
$captcha = $_POST["g-recaptcha-response"];


$message = "";
$status = "false";

if( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
    if( $_POST['reservation_email'] != '' AND $_POST['reservation_phone'] != '' AND $_POST['car_select'] != '') {

        $email = $_POST['reservation_email'];
        $phone = $_POST['reservation_phone'];
        $car = $_POST['car_select'];
        $message = $_POST['form_message'];

        $subject = isset($subject) ? $subject : 'New Message | Website Reservation Form';
        $name = isset($_POST['reservation_name']) ? $_POST['reservation_name'] : '';
        $reservation_date = isset($_POST['reservation_date']) ? $_POST['reservation_date'] : '';

        $botcheck = $_POST['form_botcheck'];

        $toemail = 'appointments@cardiocare.co.tz'; // Your Email Address
        $toname = 'Cardiocare Appointments';        // Receiver Name

        if( $botcheck == '' ) {

            $mail->SetFrom( $email , $name );
            $mail->AddReplyTo( $email , $name );
            $mail->AddAddress( $toemail , $toname );
            $mail->Subject = $subject;

            $name = isset($name) ? "Name: $name<br><br>" : '';
            $email = isset($email) ? "Email: $email<br><br>" : '';
            $phone = isset($phone) ? "Phone: $phone<br><br>" : '';
            $car = isset($car) ? "Requested Service: $car<br><br>" : '';
            $reservation_date = isset($reservation_date) ? "Appointment Date: $reservation_date<br><br>" : '';
            $message = isset($message) ? "Message: $message<br><br>" : '';

            $referrer = $_SERVER['HTTP_REFERER'] ? '<br><br><br>This Form was submitted from: ' . $_SERVER['HTTP_REFERER'] : '';

            $body = "$name $email $phone $car $reservation_date $message $referrer";

            $mail->MsgHTML( $body );
            $sendEmail = $mail->Send();

            if( $sendEmail == true ):
                
                
                $ip = $_SERVER['REMOTE_ADDR'];
        $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$secretKey."&response=".$captcha."&remoteip=".$ip);
        $responseKeys = json_decode($response,true);

        if(intval($responseKeys["success"]) !== 1) 
        {
          
          $message = 'Please check the captcha form';
          
        }    
                
                
                
                $message = 'We have <strong>successfully</strong> received your Message and will get Back to you as soon as possible.';
                $status = "true";
            else:
                $message = 'Email <strong>could not</strong> be sent due to some Unexpected Error. Please Try Again later.<br /><br /><strong>Reason:</strong><br />' . $mail->ErrorInfo . '';
                $status = "false";
            endif;
        } else {
            $message = 'Bot <strong>Detected</strong>.! Clean yourself Botster.!';
            $status = "false";
        }
    } else {
        $message = 'Please <strong>Fill up</strong> all the Fields and Try Again.';
        $status = "false";
    }
} else {
    $message = 'An <strong>unexpected error</strong> occured. Please Try Again later.';
    $status = "false";
}


if(!$captcha)


{
    
  $message = ' Please <strong>check</strong> the captcha form.';
    $status = "false";  
    
}


$status_array = array( 'message' => $message, 'status' => $status);
echo json_encode($status_array);
?>