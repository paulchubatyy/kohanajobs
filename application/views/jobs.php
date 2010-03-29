<h1><?php echo count($jobs), ' ', Inflector::plural('job', count($jobs)) ?> available</h1>

<ul>
	<?php foreach ($jobs as $job) { ?>
		<li>
			<?php echo HTML::anchor(Route::get('job')->uri(array('id' => $job->id)), HTML::chars($job->title)) ?>
		</li>
	<?php } ?>
</ul>