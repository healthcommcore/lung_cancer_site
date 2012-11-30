<?php
################################################################################
###########
### An email script that will attach images inline in an HTML and plain text e-mail ###
### Just enter you own email infomation and file names with paths where indicated in ###
### the script. Have fun with it alter it add to it or whatever you want. When you are ###
### done reading all the confusing tutorials about this kind of using mail to send your ###
### own without PHPmailer then you can just use this file to suit your self. ###
################################################################################
###########
$headers = "From: Me <me@email.com>";//put you own stuff here or use a variable
$to = 'therese_lung@dfci.harvard.edu';// same as above
// $to = 'theresel1@yahoo.com';// same as above
$subject = 'Testing Inline attachment HTML Emails';//your own stuff goes here
$html ="<img src='/var/www/html/help.trackmychanges.org/images/hd2/tracking/weekIMG1.gif'><br /><br />
<b>This</b> is HTML <span style='background:cyan'>and this is a cyan highlight</span>
<br />So this should be a new line.<br /><br />This should be a new line with a space between the above.
<br />Here's dead Al<br><img src='/var/www/html/help.trackmychanges.org/images/hd2/tracking/weekIMG2.gif'><br />He is dead in this photo!<br />This is a martyr, well
OK then I think I will pass on looking like that all blowed up and all.<br /><br />So much for being a martyr!<br /> He's just another dead terrorist in the pile of the others ... ougggh nooooo!";//make up your own html or use an include
//the below is your own plain text message (all the $message(x))
$message0 = 'Dear valued customer,';// or make up your own for plain text message
$message1 = 'NukeXtra just released our new search engine optimisation
(SEO) services.
We have exciting new packages from Cost-Per-Click (CPC, Paid advertising) to specialised optimization of your website by a designated SEO campaign manager.';
$message2 = 'Studies have proven that top placement in search engines, among other forms of online marketing, provide a more favourable return on investment compared to traditional forms of advertising such as, email marketing, radio commercials and television.';
$message3 = 'Search engine optimization is the ONLY fool proof method to earning guaranteed Top 10 search engine placement.';
$message4 = '95% of monthly Internet users utilize search engines to find and access websites';
$message5 = 'Attached is the NukeXtra SEO & CPC packages guide for your information.';
$message6 = 'If you have any questions or are interested in proceeding with our SEO services, please do not hesitate to contact us.';
$message7 = 'I look forward to this opportunity for us to work together.';
$message8 = 'With Kindest regards,';
$message9 = 'Someone';
$message10 = 'PHP Web Programmer';
$message11 = 'NukeXtra - stevedemarcus@ahost.com - http://dhost.info/stevedemarcus/steve/' ;
$message12 = '218 Some Court<br />Somewhere, ST 55555';
$message12 = 'Tel: (xxx)-xxx-xxx | Fax: {xxx)-xxx-xxxx';
//Now lets set up some attachments (two in this case)
//first file to attach
$fileatt2 = 'weekIMG1.gif';//put the relative path to the file here on your server
$fileatt_name2 = 'weekIMG1.gif';//just the name of the file here
$fileatt_type2 = filetype($fileatt2);
$file2 = fopen($fileatt2,'rb');
$data2 = fread($file2,filesize($fileatt2));
fclose($file2);
//another file to attach

$fileatt = 'weekIMG2.gif';//relative path to image two and more (this one is in the same directory)
$fileatt_name = 'weekIMG2.gif';//just the name of the file
$fileatt_type = filetype($fileatt);
$file = fopen($fileatt,'rb');
$data = fread($file,filesize($fileatt));
fclose($file);
// Generate a boundary string that is unique
$semi_rand = md5(time());
$mime_boundary = "==Multipart_Boundary_x{$semi_rand}x";
// Add the headers for a file attachment
$headers .= "\nMIME-Version: 1.0\n" .
"Content-Type: multipart/alternative;\n" .
" boundary=\"{$mime_boundary}\"";
$message = "--{$mime_boundary}\n" .
"Content-Type: text/html; charset=\"iso-8859-1\"\n" .
"Content-Transfer-Encoding: 7bit\n\n" .
"<font face=Arial>" .
$html."\r\n";
$message .= "--{$mime_boundary}\n" .
"Content-Type: text/plain; charset=\"iso-8859-1\"\n" .
"Content-Transfer-Encoding: 7bit\n\n" .
$message0 . "\n\n" .
$message1 . "\n\n" .
$message2 . "\n\n" .
$message3 . "\n\n" .
$message4 . "\n\n" .
$message5 . "\n\n" .
$message6 . "\n\n" .
$message7 . "\n\n" .
$message8 . "\n\n" .
$message9 . "\n" .
$message10 . "\n" .
$message11 . "\n" .
$message12 . "\n\n";
// Add the headers for a file attachment
$headers .= "\nMIME-Version: 1.0\n" .
"Content-Type: multipart/mixed;\n" .
" boundary=\"{$mime_boundary}\"";
// Base64 encode the file data
$data2 = chunk_split(base64_encode($data2));
// Add file attachment to the message
$message .= "--{$mime_boundary}\n" .
"Content-Type: image/gif;\n" . // {$fileatt_type}
" name=\"{$fileatt_name2}\"\n" .
"Content-Disposition: inline;\n" .
" filename=\"{$fileatt_name2}\"\n" .
"Content-Transfer-Encoding: base64\n\n" .
$data2 . "\n\n" .
"--{$mime_boundary}--\n";
// Add another file attachment to the message as many as you have
$data = chunk_split(base64_encode($data));
// Add file attachment to the message
$message .= "--{$mime_boundary}\n" .
"Content-Type: image/gif;\n" . // {$fileatt_type}
" name=\"{$fileatt_name}\"\n" .
"Content-Disposition: inline;\n" .
" filename=\"{$fileatt_name}\"\n" .
"Content-Transfer-Encoding: base64\n\n" .
$data . "\n\n" .
"--{$mime_boundary}--\n";
// Send the message
$send = mail($to, $subject, $message, $headers);
if ($send) {
echo "<p>Email Sent to intended recipients successfully!</p>";
} else {
echo "<p>Mail could not be sent. You missed something in the script. Sorry!</p>";
}
?> 