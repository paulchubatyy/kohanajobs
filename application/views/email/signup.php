Hi <?php echo $username ?> 


Thanks for signing up at KohanaJobs.com!

Please confirm your sign-up by simply following this URL:
<?php echo URL::site(Route::get('user/confirm')->uri(array('id' => $id, 'code' => $code)), TRUE) ?> 


Best regards
www.kohanajobs.com