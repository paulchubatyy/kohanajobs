<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>

	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo HTML::chars($title) ?></title>

	<link rel="stylesheet" type="text/css" href="<?php echo URL::site('css/kohanajobs.css') ?>" />

</head>
<body>

	<div id="header">
		<p id="identity">KohanaJobs</p>

		<p>
			<?php echo HTML::anchor(Route::get('jobs')->uri(), 'Home') ?> —
			<?php echo HTML::anchor(Route::get('post')->uri(), 'Post a new job') ?> —

			<?php if (Auth::instance()->logged_in()) { ?>
				You are signed in as <?php echo HTML::chars(Auth::instance()->get_user()->username) ?> —
				<?php echo HTML::anchor(Route::get('user')->uri(array('action' => 'signout')), 'Sign out') ?>
			<?php } else { ?>
				<?php echo HTML::anchor(Route::get('user')->uri(array('action' => 'signin')), 'Sign in') ?> or
				<?php echo HTML::anchor(Route::get('user')->uri(array('action' => 'signup')), 'Sign up') ?>
			<?php } ?>
		</p>
	</div><!-- #header -->

	<div id="content">
		<?php echo $content ?>
	</div><!-- #content -->

	<div id="footer">
		<p>
			© 2008–<?php echo date('Y') ?> —
			<?php echo HTML::anchor('http://github.com/GeertDD/kohanajobs', 'KohanaJobs at GitHub') ?>
		</p>
	</div><!-- #footer -->

	<?php if (Kohana::$environment !== Kohana::PRODUCTION) { ?>
		<div id="kohana-profiler">
			<?php echo View::factory('profiler/stats') ?>
			<p>$_SESSION = <?php echo Kohana::debug(Session::instance()->as_array()) ?></p>
		</div><!-- #kohana-profiler -->
	<?php } ?>

</body>
</html>