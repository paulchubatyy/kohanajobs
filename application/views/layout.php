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
		<h1 id="identity"><?php echo HTML::anchor('', HTML::image('img/layout/kohanajobs.png', array('alt' => 'KohanaJobs'))) ?></h1>

	<?php if (Request::instance()->uri !== Route::get('post')->uri()) { ?>
		<p id="post"><?php echo HTML::anchor(Route::get('post')->uri(), HTML::image('img/layout/post.png', array('alt' => __('Post a new job')))) ?></p>
	<?php } ?>

	</div><!-- #header -->

	<div id="main">
		<div id="content">
		<?php echo $content ?>

		</div>

		<div id="sidebar">
		<?php echo View::factory('sidebar/intro') ?>

		<?php echo View::factory('sidebar/kohana') ?>
		
		<?php if (Request::instance()->uri === Route::get('post')->uri()): ?>
		<?php echo View::factory('sidebar/run') ?>

		<?php echo View::factory('sidebar/cost') ?>
		<?php else: ?>
		<?php echo View::factory('sidebar/post') ?>
		<?php endif ?>

		<?php echo View::factory('sidebar/faq') ?>

		</div>
	</div><!-- #main -->

	<div id="footer">
		© <?php echo date('Y') ?>
	</div>

	<?php if (Kohana::$environment !== 'production') { ?>
		<div id="kohana-profiler">
			<?php echo View::factory('profiler/stats') ?>
		</div>
	<?php } ?>

</body>
</html>