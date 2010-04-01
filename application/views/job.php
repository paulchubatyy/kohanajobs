<h1><?php echo HTML::chars($job->title) ?></h1>

<div>
	<?php echo HTML::chars($job->description) ?>
</div>

<p><?php echo HTML::anchor(Route::get('jobs')->uri(), 'View all jobs') ?></p>