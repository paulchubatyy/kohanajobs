Hi <?php echo $username ?> 


If you lost the password to your account, you can reset it by simply following this URL:
<?php echo URL::site(Route::get('user/confirm_reset_password')->uri(array('id' => $id, 'code' => $code, 'time' => $time)), TRUE) ?> 


Best regards
www.kohanajobs.com