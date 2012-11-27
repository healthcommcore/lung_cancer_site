<?php
$to = 'therese_lung@dfci.harvard.edu';// same as above
$subject = 'Test email message';
$random_hash = md5(date('r', time()));
$headers = "From: sender@domain.com\r\nReply-To: sender@domain.com\r\n";
$headers .= "Content-Type: multipart/mixed; boundary=\"PHP-mixed-".$random_hash."\"";
$attachment = chunk_split(base64_encode(file_get_contents("weekIMG1.gif")));
$output = "
–PHP-mixed-$random_hash
Content-Type: text/plain; charset='iso-8859-1'

This is the simple text version of the email message.

–PHP-mixed-$random_hash
Content-Type: text/html; charset='iso-8859-1'

This is the simple text version of the email message.

–PHP-mixed-$random_hash
Content-Type: application/pdf; name='weekIMG1.gif'
Content-Transfer-Encoding: base64
Content-Disposition: attachment

$attachment
–PHP-mixed-$random_hash–";

//if (@mail($to, $subject, $output, $headers)) {
$send = mail($to, $subject, $output, $headers);
if ($send) {
echo 'Mail sent';
} else {
echo 'Mail NOT sent';
}
// var_dump($headers, $output);
?>