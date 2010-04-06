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
		<?php echo HTML::anchor('', 'KohanaJobs') ?>
	</div>

	<?php if (Request::instance()->uri !== Route::get('post')->uri()) { ?>
		<p><?php echo HTML::anchor(Route::get('post')->uri(), 'Post a new job') ?></p>
	<?php } ?>

	<?php echo $content ?>

	<div id="footer">
		© <?php echo date('Y') ?>
	</div>

	<?php if (Kohana::$environment !== Kohana::PRODUCTION) { ?>
		<div id="kohana-profiler">
			<?php echo View::factory('profiler/stats') ?>
		</div>
	<?php } ?>

</body>
</html>