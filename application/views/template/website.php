<!DOCTYPE html>

<html lang="en">
<head>

	<meta charset="utf-8" />
	<title><?php echo HTML::chars($title) ?></title>

	<link rel="stylesheet" href="<?php echo URL::site('css/kohanajobs.css') ?>" />
	<link rel="alternate" type="application/rss+xml" title="Kohana Jobs rss" href="<?php echo URL::site('rss') ?>" />
</head>
<body>

	<?php echo Message::render() ?>

	<div style="padding:1em; background:yellow; text-align:center;">
		<strong>KohanaJobs v2 is still under construction.</strong><br />
		<a href="http://www.kohanajobs.com/">Go to v1</a> or <a href="http://github.com/GeertDD/kohanajobs">Follow v2 development</a>
	</div>

	<div id="header">
		<p id="identity">KohanaJobs</p>

		<p>
			<?php echo HTML::anchor(Route::get('jobs')->uri(), 'Home') ?> —
			<?php echo HTML::anchor(Route::get('post')->uri(), 'Post a new job') ?> —

			<?php if (Auth::instance()->logged_in()) { ?>

				<?php if (Auth::instance()->logged_in_oauth()) { ?>
					You signed in via OAuth —
				<?php } else { ?>
					You are signed in as <?php echo HTML::chars($user->username) ?> —
					<?php echo HTML::anchor(Route::get('user')->uri(array('action' => 'change_password')), 'Change password') ?> —
				<?php } ?>

				<?php echo HTML::anchor(Route::get('user')->uri(array('action' => 'change_email')), 'Change email') ?> —
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
			Powered by <a href="http://kohanaframework.org/">Kohana</a> v<?php echo Kohana::VERSION ?> —
			<?php echo HTML::anchor('http://github.com/GeertDD/kohanajobs', 'KohanaJobs at GitHub') ?>
		</p>
	</div><!-- #footer -->

	<?php if (Kohana::$environment !== Kohana::PRODUCTION) { ?>
		<div id="kohana-profiler">
			<?php echo View::factory('profiler/stats') ?>
			<p>$_GET = <?php echo Kohana::debug($_GET) ?></p><hr />
			<p>$_POST = <?php echo Kohana::debug($_POST) ?></p><hr />
			<p>$_COOKIE = <?php echo Kohana::debug($_COOKIE) ?></p><hr />
			<p>$_SESSION = <?php echo Kohana::debug(Session::instance()->as_array()) ?></p><hr />
			<!-- <p>$_SERVER = <?php echo Kohana::debug($_SERVER) ?></p> -->
		</div><!-- #kohana-profiler -->
	<?php } ?>

</body>
</html>