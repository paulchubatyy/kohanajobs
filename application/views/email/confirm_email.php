Hi <?php echo $username ?> 


You requested to change your email address.

Please confirm your new email address by simply following this URL:
<?php echo URL::site(Route::get('user/confirm_email')->uri(array('id' => $id, 'code' => $code, 'new_email' => base64_encode($new_email))), TRUE) ?> 


Best regards
www.kohanajobs.com